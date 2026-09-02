<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Core\RateLimiter;
use App\Core\Validator;
use App\Core\PDPA;
use App\Models\WasteReport;
use App\Models\WasteType;
use App\Models\CollectionSchedule;

class CitizenController {
    public function home(): void {
        $metrics = WasteReport::getDashboardMetrics();
        $wasteTypes = WasteType::all();
        $activeReports = WasteReport::getFiltered(['status' => ''], 10);
        $nextSchedule = CollectionSchedule::getActiveOrNext();
        
        View::render('citizen.home', [
            'title' => 'ระบบแจ้งจัดเก็บขยะไร้บ้าน | เทศบาลและเมืองน่าอยู่',
            'metrics' => $metrics,
            'wasteTypes' => $wasteTypes,
            'activeReports' => $activeReports,
            'nextSchedule' => $nextSchedule
        ]);
    }

    public function schedule(): void {
        $nextSchedule = CollectionSchedule::getActiveOrNext();
        $upcomingSchedules = CollectionSchedule::getUpcomingList(12);
        $allSchedules = CollectionSchedule::allWithStats();

        View::render('citizen.schedule', [
            'title' => 'ตารางและรอบวันจัดเก็บขยะประจำเดือน | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'nextSchedule' => $nextSchedule,
            'upcomingSchedules' => $upcomingSchedules,
            'allSchedules' => $allSchedules
        ]);
    }

    public function reportForm(): void {
        $wasteTypes = WasteType::all();
        $nextSchedule = CollectionSchedule::getActiveOrNext();
        View::render('citizen.report', [
            'title' => 'แจ้งจัดเก็บขยะ | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'wasteTypes' => $wasteTypes,
            'nextSchedule' => $nextSchedule
        ]);
    }

    public function submitReport(): void {
        // 1. Anti-Bot Honeypot Trap
        $honeypot = Request::input('sys_bot_trap_field', '');
        if (!empty($honeypot)) {
            ActivityLogger::log('bot_trapped', "ตรวจพบบอทสแปมพยายามส่งข้อมูลจาก IP " . Request::ip(), null);
            Response::redirect('/success?ref=WB-TRAPPED');
            return;
        }

        // 2. IP Rate Limiting (Anti-Spam Throttling: Max 5 submissions per 10 minutes per IP)
        $ip = Request::ip();
        if (!RateLimiter::checkAndHit('citizen_report', $ip, 5, 600)) {
            $wait = RateLimiter::getWaitTimeText('citizen_report', $ip);
            $msg = "คุณส่งข้อมูลแจ้งขยะถี่เกินกำหนด เพื่อความปลอดภัยของระบบ กรุณารอ{$wait}ก่อนส่งใหม่อีกครั้ง";
            if (Request::isAjax()) {
                Response::json(['success' => false, 'message' => $msg], 429);
            }
            Response::redirect('/report', $msg, 'warning');
            return;
        }

        $inputData = [
            'reporter_name' => Request::input('reporter_name', ''),
            'reporter_phone' => Request::input('reporter_phone', ''),
            'address' => Request::input('address', ''),
            'latitude' => Request::input('latitude', 13.7563),
            'longitude' => Request::input('longitude', 100.5018),
            'description' => Request::input('description', '')
        ];

        // Unified Validation
        $validator = Validator::make($inputData, [
            'reporter_name' => 'required|min:2|max:150',
            'reporter_phone' => 'required|thai_phone',
            'address' => 'required|min:3|max:300',
            'latitude' => 'required|numeric|coordinates:lat',
            'longitude' => 'required|numeric|coordinates:lng',
            'description' => 'max:1500'
        ]);

        $validated = $validator->validated();
        $name = $validated['reporter_name'] ?? '';
        $phone = $validated['reporter_phone'] ?? '';
        $address = $validated['address'] ?? '';
        $lat = (float)($validated['latitude'] ?? 13.7563);
        $lng = (float)($validated['longitude'] ?? 100.5018);
        $description = $validated['description'] ?? '';

        // Multiple Waste Types & Weights Parsing
        $selectedTypes = Request::input('waste_types', []);
        $estimatedWeights = Request::input('estimated_weights', []);

        $items = [];
        if (!empty($selectedTypes) && is_array($selectedTypes)) {
            foreach ($selectedTypes as $typeId) {
                $typeId = (int)$typeId;
                if ($typeId > 0) {
                    $weight = isset($estimatedWeights[$typeId]) ? (float)$estimatedWeights[$typeId] : 0.0;
                    $items[] = [
                        'waste_type_id' => $typeId,
                        'estimated_weight' => max(0.0, $weight)
                    ];
                }
            }
        }

        // Fallback for single type
        if (empty($items)) {
            $singleTypeId = (int)Request::input('waste_type_id', 0);
            if ($singleTypeId > 0) {
                $singleWeight = (float)Request::input('estimated_weight', 0.0);
                $items[] = [
                    'waste_type_id' => $singleTypeId,
                    'estimated_weight' => max(0.0, $singleWeight)
                ];
            }
        }

        $errors = $validator->allErrors();
        if ($lat == 0.0 || $lng == 0.0) {
            $errors[] = 'กรุณาเลือกตำแหน่งพิกัดบนแผนที่';
        }
        if (empty($items)) {
            $errors[] = 'กรุณาเลือกประเภทขยะอย่างน้อย 1 ประเภท';
        }

        if (!empty($errors)) {
            if (Request::isAjax()) {
                Response::json(['success' => false, 'errors' => $errors], 422);
            }
            Response::redirect('/report', implode(', ', $errors), 'danger');
        }

        $scheduleId = Request::input('collection_schedule_id', null);

        // Create Report with Items
        $reportId = WasteReport::create([
            'reporter_name' => $name,
            'reporter_phone' => $phone,
            'address' => $address,
            'latitude' => $lat,
            'longitude' => $lng,
            'collection_schedule_id' => !empty($scheduleId) ? (int)$scheduleId : null,
            'items' => $items,
            'description' => $description
        ]);

        $createdReport = WasteReport::findById($reportId);
        $reportNumber = $createdReport['report_number'];

        // Handle Image Upload with Strict Magic Bytes & Extension Whitelisting
        $file = Request::file('image');
        if ($file) {
            $uploadedPath = Request::validateAndUploadImage($file, 'uploads', 10 * 1024 * 1024);
            if ($uploadedPath) {
                WasteReport::addImage($reportId, $uploadedPath, 'before');
            }
        }

        // Send Internal In-App Notification to Admins
        $typeNames = [];
        foreach ($items as $it) {
            $t = WasteType::findById($it['waste_type_id']);
            if ($t) $typeNames[] = $t['name'];
        }
        $typeNameSummary = implode(', ', $typeNames);

        NotificationService::notifyAdmins(
            'new_report',
            "มีรายการแจ้งขยะใหม่ [{$reportNumber}]",
            "ประชาชน ({$name}) แจ้งจัดเก็บ {$typeName} บริเวณ {$address}",
            'waste_report',
            $reportId
        );

        // Log Activity
        ActivityLogger::log('citizen_report', "ประชาชนแจ้งขยะรายการใหม่ {$reportNumber} ({$typeName})");

        if (Request::isAjax()) {
            Response::json([
                'success' => true,
                'report_number' => $reportNumber,
                'redirect_url' => Response::baseUrl() . "/success?number={$reportNumber}"
            ]);
        }

        Response::redirect("/success?number={$reportNumber}");
    }

    public function success(): void {
        $reportNumber = Request::input('number', '');
        $report = WasteReport::findByReportNumber($reportNumber);

        if (!$report) {
            Response::redirect('/track', 'ไม่พบข้อมูลรายการที่ระบุ', 'warning');
        }

        View::render('citizen.success', [
            'title' => 'ส่งข้อมูลสำเร็จ | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'report' => $report
        ]);
    }

    public function track(): void {
        $search = trim(Request::input('search', ''));
        $report = null;
        $phoneReports = [];
        $searchWarning = null;

        if (!empty($search)) {
            // Anti-Enumeration & Scraping Rate Limit (Max 30 searches per minute)
            $ip = Request::ip();
            if (!RateLimiter::checkAndHit('citizen_track', $ip, 30, 60)) {
                $wait = RateLimiter::getWaitTimeText('citizen_track', $ip);
                Response::redirect('/track', "คุณค้นหาถี่เกินกำหนด เพื่อความปลอดภัยของระบบกรุณารอ{$wait}ก่อนค้นหาใหม่", 'warning');
            }

            if (strpos(strtoupper($search), 'WB-') === 0) {
                $report = WasteReport::findByReportNumber($search);
            } else {
                $cleanPhone = PDPA::cleanPhone($search);
                if (strlen($cleanPhone) < 9) {
                    $searchWarning = 'กรุณากรอกเบอร์โทรศัพท์อย่างน้อย 9-10 หลักเพื่อค้นหา (เพื่อความปลอดภัยของข้อมูลส่วนบุคคล)';
                } else {
                    $phoneReports = WasteReport::searchByPhone($cleanPhone);
                    if (count($phoneReports) === 1) {
                        $report = WasteReport::findById((int)$phoneReports[0]['id']);
                    }
                }
            }
        }

        View::render('citizen.track', [
            'title' => 'ติดตามสถานะการจัดเก็บ | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'search' => $search,
            'report' => $report,
            'phoneReports' => $phoneReports,
            'searchWarning' => $searchWarning
        ]);
    }

    public function getMapPointsApi(): void {
        $reports = WasteReport::getFiltered();
        $points = [];

        foreach ($reports as $r) {
            $points[] = [
                'id' => $r['id'],
                'report_number' => $r['report_number'],
                'status' => $r['status'],
                'waste_type' => $r['waste_type_name'],
                'address' => $r['address'],
                'lat' => (float)$r['latitude'],
                'lng' => (float)$r['longitude'],
                'estimated_weight' => (float)$r['estimated_weight'],
                'actual_weight' => $r['actual_weight'] ? (float)$r['actual_weight'] : null,
                'submitted_at' => $r['submitted_at'],
                'image' => !empty($r['images']) ? $r['images'][0]['image_path'] : null
            ];
        }

        Response::json($points);
    }
}
