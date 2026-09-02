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

    public function reverseGeocodeApi(): void {
        $lat = (float)Request::input('lat', 0);
        $lng = (float)Request::input('lng', 0);

        if ($lat == 0 || $lng == 0) {
            Response::json(['success' => false, 'message' => 'Invalid coordinates'], 400);
            return;
        }

        // Cache in session by rounded coordinate (~11m precision)
        $cacheKey = 'geo_' . round($lat, 4) . '_' . round($lng, 4);
        if (!empty($_SESSION[$cacheKey])) {
            Response::json(['success' => true, 'address' => $_SESSION[$cacheKey], 'cached' => true]);
            return;
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1&accept-language=th";
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: NonthaburiWasteApp/2.0 (contact: admin@behn.go.th)\r\nAccept: application/json\r\n",
                'timeout' => 4
            ]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);

        if ($res !== false) {
            $data = json_decode($res, true);
            if (!empty($data['address'])) {
                $address = $this->formatThaiAddressFromData($data['address'], $data['display_name'] ?? '');
                $_SESSION[$cacheKey] = $address;
                Response::json(['success' => true, 'address' => $address, 'data' => $data['address']]);
                return;
            }
        }

        // Intelligent Fallback: Estimate based on coordinates in Nonthaburi
        $fallback = $this->fallbackNonthaburiAddress($lat, $lng);
        Response::json(['success' => true, 'address' => $fallback, 'fallback' => true]);
    }

    public function searchPlacesApi(): void {
        $query = trim(Request::input('q', ''));
        if (mb_strlen($query) < 2) {
            Response::json([]);
            return;
        }

        $encodedQ = urlencode($query);
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$encodedQ}&viewbox=100.35,13.98,100.65,13.75&bounded=0&countrycodes=th&limit=6&addressdetails=1&accept-language=th";
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: NonthaburiWasteApp/2.0 (contact: admin@behn.go.th)\r\nAccept: application/json\r\n",
                'timeout' => 4
            ]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);

        if ($res !== false) {
            $data = json_decode($res, true);
            if (is_array($data)) {
                Response::json($data);
                return;
            }
        }

        Response::json([]);
    }

    private function formatThaiAddressFromData(array $a, string $displayName = ''): string {
        $road = $a['road'] ?? $a['pedestrian'] ?? $a['footway'] ?? $a['street'] ?? '';
        $landmark = $a['residential'] ?? $a['building'] ?? $a['amenity'] ?? $a['office'] ?? $a['neighbourhood'] ?? $a['quarter'] ?? '';
        if ($landmark === $road) $landmark = '';

        $province = $a['province'] ?? $a['state'] ?? '';
        if (empty($province) && !empty($a['city']) && !str_contains($a['city'], 'เทศบาล') && !str_contains($a['city'], 'เมือง')) {
            $province = $a['city'];
        }

        $isBKK = str_contains($province, 'กรุงเทพ');
        $cleanProvince = trim(preg_replace('/^(จังหวัด|จ\.)\s*/u', '', $province));
        $formattedProvince = '';
        if ($cleanProvince === 'กรุงเทพมหานคร' || $cleanProvince === 'Bangkok') {
            $formattedProvince = 'กรุงเทพมหานคร';
            $isBKK = true;
        } elseif (!empty($cleanProvince)) {
            $formattedProvince = 'จ.' . $cleanProvince;
        }

        $rawSubdistrict = $a['subdistrict'] ?? $a['suburb'] ?? $a['village'] ?? '';
        $cleanSubdistrict = '';
        if (!empty($rawSubdistrict)) {
            $cleanSubdistrict = trim(preg_replace('/^(ตำบล|แขวง|ต\.|ข\.)\s*/u', '', $rawSubdistrict));
        }

        $rawDistrict = $a['district'] ?? $a['city_district'] ?? $a['county'] ?? '';
        $cleanDistrict = '';
        if (!empty($rawDistrict)) {
            $tempDist = trim(preg_replace('/^(อำเภอ|เขต|อ\.|ข\.|ตำบล|แขวง|ต\.)\s*/u', '', $rawDistrict));
            if ($tempDist !== $cleanSubdistrict) {
                $cleanDistrict = $tempDist;
            }
        }
        if (empty($cleanDistrict) && !empty($a['city'])) {
            $cityClean = trim(preg_replace('/^(เทศบาลนคร|เทศบาลเมือง|เทศบาลตำบล|อำเภอ|เขต|อ\.|ข\.)\s*/u', '', $a['city']));
            if (str_contains($cityClean, 'เมือง') || $cityClean === $cleanProvince) {
                $cleanDistrict = 'เมือง' . ($cleanProvince ? $cleanProvince : '');
            } elseif ($cityClean !== $cleanSubdistrict && $cityClean !== $cleanProvince) {
                $cleanDistrict = $cityClean;
            }
        }

        $formattedSubdistrict = $cleanSubdistrict ? ($isBKK ? 'แขวง' : 'ต.') . $cleanSubdistrict : '';
        $formattedDistrict = $cleanDistrict ? ($isBKK ? 'เขต' : 'อ.') . $cleanDistrict : '';

        $postcode = $a['postcode'] ?? $a['postal_code'] ?? '';
        if (empty($postcode) && (str_contains($cleanProvince, 'นนทบุรี') || str_contains($formattedProvince, 'นนทบุรี'))) {
            $postcode = '11000';
        }

        $parts = [];
        if ($road) $parts[] = $road;
        if ($landmark && $landmark !== $road && !in_array($landmark, $parts)) $parts[] = $landmark;
        if ($formattedSubdistrict && !in_array($formattedSubdistrict, $parts)) $parts[] = $formattedSubdistrict;
        if ($formattedDistrict && !in_array($formattedDistrict, $parts)) $parts[] = $formattedDistrict;
        if ($formattedProvince && !in_array($formattedProvince, $parts)) $parts[] = $formattedProvince;
        if ($postcode && !in_array($postcode, $parts)) $parts[] = $postcode;

        return !empty($parts) ? implode(' ', $parts) : $displayName;
    }

    private function fallbackNonthaburiAddress(float $lat, float $lng): string {
        if ($lat >= 13.86 && $lng >= 100.51) {
            return "ใกล้ศูนย์ราชการนนทบุรี ต.บางกระสอ อ.เมืองนนทบุรี จ.นนทบุรี 11000";
        } elseif ($lat >= 13.85 && $lng <= 100.49) {
            return "ใกล้ท่าน้ำนนทบุรี ต.สวนใหญ่ อ.เมืองนนทบุรี จ.นนทบุรี 11000";
        } elseif ($lat >= 13.87) {
            return "ถนนรัตนาธิเบศร์ ต.บางกระสอ อ.เมืองนนทบุรี จ.นนทบุรี 11000";
        }
        return "เขตเทศบาลนครนนทบุรี อ.เมืองนนทบุรี จ.นนทบุรี 11000";
    }
}
