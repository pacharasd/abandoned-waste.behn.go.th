<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
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
        $name = trim(Request::input('reporter_name', ''));
        $phone = trim(Request::input('reporter_phone', ''));
        $address = trim(Request::input('address', ''));
        $lat = (float)Request::input('latitude', 13.7563);
        $lng = (float)Request::input('longitude', 100.5018);
        $description = trim(Request::input('description', ''));

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
                        'estimated_weight' => $weight
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
                    'estimated_weight' => $singleWeight
                ];
            }
        }

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = 'กรุณาระบุชื่อ-นามสกุล';
        if (empty($phone)) $errors[] = 'กรุณาระบุเบอร์โทรศัพท์สำหรับติดต่อ';
        if (empty($address)) $errors[] = 'กรุณาระบุรายละเอียดสถานที่จัดเก็บ';
        if ($lat == 0 || $lng == 0) $errors[] = 'กรุณาเลือกตำแหน่งพิกัดบนแผนที่';
        if (empty($items)) $errors[] = 'กรุณาเลือกประเภทขยะอย่างน้อย 1 ประเภท';

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

        // Handle Image Upload
        $file = Request::file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $maxSize = 10 * 1024 * 1024; // 10MB

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $uploadDir = BASE_PATH . '/public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = 'report_' . $reportId . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    WasteReport::addImage($reportId, 'uploads/' . $newFilename, 'before');
                }
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
        $search = Request::input('search', '');
        $report = null;
        $phoneReports = [];

        if (!empty($search)) {
            if (strpos(strtoupper($search), 'WB-') === 0) {
                $report = WasteReport::findByReportNumber($search);
            } else {
                $phoneReports = WasteReport::searchByPhone($search);
                if (count($phoneReports) === 1) {
                    $report = WasteReport::findById((int)$phoneReports[0]['id']);
                }
            }
        }

        View::render('citizen.track', [
            'title' => 'ติดตามสถานะการจัดเก็บ | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'search' => $search,
            'report' => $report,
            'phoneReports' => $phoneReports
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
