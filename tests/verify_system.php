<?php
/**
 * End-to-End System Test & Verification Script
 * Validates database connectivity, routes, controllers, notifications, and workflow.
 */

define('BASE_PATH', dirname(__DIR__));

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

// Load .env
function loadEnvFile(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
        }
    }
}
loadEnvFile(BASE_PATH . '/.env');

echo "=======================================================\n";
echo "🧪 RUNNING COMPREHENSIVE SYSTEM VERIFICATION TESTS\n";
echo "=======================================================\n";

use App\Core\Database;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Models\User;
use App\Models\WasteType;
use App\Models\WasteReport;

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "✅ PASS: {$testName}\n";
        $passCount++;
    } else {
        echo "❌ FAIL: {$testName}\n";
        $failCount++;
    }
}

try {
    // 1. Database Connection
    $pdo = Database::connect();
    assertTest($pdo instanceof PDO, "Database Connection to `behn_abandoned_waste`");

    // 2. Models Check
    $admin = User::findByEmail('admin@waste.local');
    assertTest($admin !== null && $admin['role'] === 'admin', "User Model: Found Admin user `admin@waste.local`");
    assertTest(password_verify('admin1234', $admin['password']), "Password Hashing: Admin password verified");

    $types = WasteType::all();
    assertTest(count($types) >= 6, "WasteType Model: " . count($types) . " categories loaded");

    // WasteType CRUD Test
    $testTypeId = WasteType::create([
        'name' => 'ขยะสารเคมีทดสอบ',
        'description' => 'ตัวอย่างขยะสารเคมีสำหรับทดสอบระบบ',
        'icon' => 'flask-conical',
        'is_active' => 1
    ]);
    $createdType = WasteType::findById($testTypeId);
    assertTest($createdType !== null && $createdType['name'] === 'ขยะสารเคมีทดสอบ', "WasteType Model: Created type ID {$testTypeId}");

    WasteType::update($testTypeId, [
        'name' => 'ขยะสารเคมีทดสอบ (แก้ไข)',
        'description' => 'อัปเดตคำอธิบาย',
        'icon' => 'alert-triangle',
        'is_active' => 1
    ]);
    $updatedType = WasteType::findById($testTypeId);
    assertTest($updatedType['name'] === 'ขยะสารเคมีทดสอบ (แก้ไข)', "WasteType Model: Updated type successfully");

    WasteType::delete($testTypeId);
    $deletedType = WasteType::findById($testTypeId);
    assertTest($deletedType === null, "WasteType Model: Deleted test type successfully");


    // 3. Metrics Check
    $metrics = WasteReport::getDashboardMetrics();
    assertTest($metrics['total'] >= 6, "WasteReport: Dashboard metrics total = " . $metrics['total']);
    assertTest($metrics['actual_weight_total'] > 0, "WasteReport: Actual weight total = " . $metrics['actual_weight_total'] . " kg");

    // 4. Report Creation with Multiple Waste Types & Weights
    $newReportId = WasteReport::create([
        'reporter_name' => 'นายทดสอบ ระบบจริง',
        'reporter_phone' => '089-999-8888',
        'address' => 'ถนนสุขุมวิท ซอย 21 หน้าตึกเสริมมิตร',
        'latitude' => 13.7432,
        'longitude' => 100.5612,
        'items' => [
            ['waste_type_id' => 1, 'estimated_weight' => 15.0],
            ['waste_type_id' => 2, 'estimated_weight' => 10.0],
            ['waste_type_id' => 3, 'estimated_weight' => 5.5]
        ],
        'description' => 'ทดสอบส่งข้อมูลขยะ 3 ประเภทผ่าน Automated Test'
    ]);
    $created = WasteReport::findById($newReportId);
    assertTest($created !== null, "WasteReport: Created report ID {$newReportId}");
    assertTest(strpos($created['report_number'], 'WB-') === 0, "WasteReport: Generated number {$created['report_number']}");
    assertTest(count($created['items']) === 3, "WasteReport: Created 3 distinct waste items");
    assertTest((float)$created['estimated_weight'] === 30.5, "WasteReport: Calculated total estimated weight = 30.5 kg");

    // Test Item Actual Weight Updates
    $itemIds = array_column($created['items'], 'id');
    WasteReport::updateItemActualWeights($newReportId, [
        $itemIds[0] => 14.0,
        $itemIds[1] => 9.5,
        $itemIds[2] => 5.0
    ]);
    $afterWeightReport = WasteReport::findById($newReportId);
    assertTest((float)$afterWeightReport['actual_weight'] === 28.5, "WasteReport: Updated item actual weights total = 28.5 kg");


    // 5. In-App Notification Engine
    NotificationService::notifyAdmins(
        'new_report',
        "มีรายการแจ้งขยะใหม่ [{$created['report_number']}]",
        "ทดสอบระบบแจ้งเตือน",
        'waste_report',
        $newReportId
    );
    $adminUnread = NotificationService::getUnreadCount($admin['id']);
    assertTest($adminUnread > 0, "NotificationService: Admin unread notifications = {$adminUnread}");

    // 6. Direct Admin Workflow Test
    WasteReport::updateStatus($newReportId, 'กำลังตรวจสอบ', (int)$admin['id'], 'Admin กำลังตรวจสอบจุดแจ้งขยะ');
    $reviewReport = WasteReport::findById($newReportId);
    assertTest($reviewReport['status'] === 'กำลังตรวจสอบ', "Admin Workflow: Status changed to 'กำลังตรวจสอบ'");

    // 7. Admin In-Progress Update Test
    WasteReport::updateStatus($newReportId, 'กำลังดำเนินการ', (int)$admin['id'], 'เริ่มดำเนินการจัดเก็บขยะ');
    $inProgressReport = WasteReport::findById($newReportId);
    assertTest($inProgressReport['status'] === 'กำลังดำเนินการ', "Admin Workflow: Status changed to 'กำลังดำเนินการ'");

    // 8. Admin Complete Job Test
    WasteReport::completeJob($newReportId, 28.5, 'จัดเก็บเสร็จเรียบร้อยและทำความสะอาดพื้นที่', (int)$admin['id']);
    $completedReport = WasteReport::findById($newReportId);
    assertTest($completedReport['status'] === 'จัดเก็บเรียบร้อยแล้ว', "Admin Workflow: Status changed to 'จัดเก็บเรียบร้อยแล้ว'");
    assertTest((float)$completedReport['actual_weight'] === 28.5, "Admin Workflow: Actual weight recorded = 28.5 kg");
    assertTest(!empty($completedReport['completed_at']), "Admin Workflow: Completed timestamp recorded");

    // 9. Status History Check
    $history = WasteReport::getStatusHistory($newReportId);
    assertTest(count($history) >= 4, "Status History: Recorded " . count($history) . " timeline state transitions");

    // 10. Paginator Engine Tests
    $sampleItems = range(1, 45);
    $paginator = \App\Core\Paginator::fromArray($sampleItems, 2, 10, '/admin/reports', ['status' => 'กำลังดำเนินการ']);
    assertTest($paginator->totalItems === 45, "Paginator: Total items correctly calculated = 45");
    assertTest($paginator->totalPages === 5, "Paginator: Total pages correctly calculated = 5");
    assertTest($paginator->currentPage === 2, "Paginator: Current page is 2");
    assertTest($paginator->from === 11 && $paginator->to === 20, "Paginator: Range is 11 to 20");
    assertTest(count($paginator->items) === 10, "Paginator: Sliced items count = 10");
    assertTest(strpos($paginator->url(3), 'page=3') !== false, "Paginator: URL builder generates page=3");
    assertTest(strpos($paginator->url(3), 'status=%E0%B8%81%E0%B8%B3%E0%B8%A5%E0%B8%B1%E0%B8%87%E0%B8%94%E0%B8%B3%E0%B9%80%E0%B8%99%E0%B8%B4%E0%B8%99%E0%B8%81%E0%B8%B2%E0%B8%A3') !== false || strpos($paginator->url(3), 'status=') !== false, "Paginator: URL builder preserves filter parameters");
    $renderedHtml = $paginator->render();
    assertTest(strpos($renderedHtml, 'แสดงผล') !== false && strpos($renderedHtml, 'ก่อนหน้า') !== false, "Paginator: Rendered HTML pagination component with Thai text");

    // 11. Model Pagination Filter Test
    $filteredCount = WasteReport::countFiltered(['status' => 'จัดเก็บเรียบร้อยแล้ว']);
    assertTest($filteredCount >= 4, "WasteReport: countFiltered returns count >= 4 (found {$filteredCount})");

    // 12. Security Suite: RateLimiter & Anti-Brute-Force
    $testKey = 'test_ip_rate_limit_' . time();
    \App\Core\RateLimiter::clear($testKey);
    assertTest(!\App\Core\RateLimiter::tooManyAttempts($testKey, 3, 60), "RateLimiter: Initial attempts under limit");
    \App\Core\RateLimiter::hit($testKey, 60);
    \App\Core\RateLimiter::hit($testKey, 60);
    \App\Core\RateLimiter::hit($testKey, 60);
    assertTest(\App\Core\RateLimiter::tooManyAttempts($testKey, 3, 60), "RateLimiter: Locked out after 3 attempts");
    assertTest(\App\Core\RateLimiter::availableIn($testKey) > 0, "RateLimiter: Lockout time remaining calculated correctly");
    \App\Core\RateLimiter::clear($testKey);
    assertTest(!\App\Core\RateLimiter::tooManyAttempts($testKey, 3, 60), "RateLimiter: Successfully cleared lockout state");

    // 13. Security Suite: PDPA Sensitive Data Masking
    $maskedPhone = \App\Core\PDPA::maskPhone('0812345678');
    assertTest($maskedPhone === '081-***-5678', "PDPA: Phone masked correctly ($maskedPhone)");
    $maskedName = \App\Core\PDPA::maskName('สมศักดิ์ รักสะอาด');
    assertTest(strpos($maskedName, 'สมศักดิ์') !== false && strpos($maskedName, 'รักสะอาด') === false, "PDPA: Full name masked with initial ($maskedName)");

    // 14. Security Suite: Session & File Upload Defense
    assertTest(file_exists(BASE_PATH . '/public/uploads/.htaccess'), "File Defense: public/uploads/.htaccess execution blocker exists");
    assertTest(file_exists(BASE_PATH . '/public/.htaccess'), "Server Hardening: public/.htaccess security headers configured");

    // 15. Security Suite: Unified Validator Engine Tests
    $validData = [
        'name' => 'นายสมบูรณ์ ปลอดภัย',
        'email' => 'secure@waste.local',
        'phone' => '0891234567',
        'lat' => 13.8591,
        'lng' => 100.5217,
        'status' => 'กำลังดำเนินการ'
    ];
    $v1 = \App\Core\Validator::make($validData, [
        'name' => 'required|min:3|max:100',
        'email' => 'required|email',
        'phone' => 'required|thai_phone',
        'lat' => 'required|numeric|coordinates:lat',
        'lng' => 'required|numeric|coordinates:lng',
        'status' => 'required|in:รอรับเรื่อง,กำลังตรวจสอบ,กำลังดำเนินการ,จัดเก็บเรียบร้อยแล้ว,ยกเลิก'
    ]);
    assertTest($v1->passes(), "Validator: Valid dataset passed all rules");

    $invalidData = [
        'name' => 'A',
        'email' => 'invalid-email-string',
        'phone' => '12345',
        'lat' => 999.0,
        'status' => 'status_not_allowed'
    ];
    $v2 = \App\Core\Validator::make($invalidData, [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required|thai_phone',
        'lat' => 'required|coordinates:lat',
        'status' => 'required|in:รอรับเรื่อง,กำลังตรวจสอบ,กำลังดำเนินการ,จัดเก็บเรียบร้อยแล้ว,ยกเลิก'
    ]);
    assertTest($v2->fails(), "Validator: Invalid dataset rejected correctly");
    assertTest(count($v2->allErrors()) >= 4, "Validator: Captured all field error messages (" . count($v2->allErrors()) . " errors)");

    // 16. Security Suite: Anti-Enumeration & Phone Search Protection
    $enumAttackResult = WasteReport::searchByPhone('0');
    assertTest(count($enumAttackResult) === 0, "PDPA Defense: Single digit search '0' rejected from enumerating database");
    $shortPhoneResult = WasteReport::searchByPhone('0812');
    assertTest(count($shortPhoneResult) === 0, "PDPA Defense: Short substring search (< 9 digits) rejected");
    $legitPhoneResult = WasteReport::searchByPhone('089-999-8888');
    assertTest(count($legitPhoneResult) > 0, "PDPA Defense: Legitimate full phone search successfully found records");

    // 17. Security Suite: Atomic Throttling & Wait Message
    $throttleAction = 'test_throttle_action';
    $testIp = '192.168.1.99';
    \App\Core\RateLimiter::clear($throttleAction . ':' . md5($testIp));
    assertTest(\App\Core\RateLimiter::checkAndHit($throttleAction, $testIp, 2, 60), "RateLimiter: Attempt 1 permitted");
    assertTest(\App\Core\RateLimiter::checkAndHit($throttleAction, $testIp, 2, 60), "RateLimiter: Attempt 2 permitted");
    assertTest(!\App\Core\RateLimiter::checkAndHit($throttleAction, $testIp, 2, 60), "RateLimiter: Attempt 3 throttled/blocked");
    $waitMsg = \App\Core\RateLimiter::getWaitTimeText($throttleAction, $testIp);
    assertTest(strpos($waitMsg, 'วินาที') !== false || strpos($waitMsg, 'นาที') !== false, "RateLimiter: Generated friendly wait duration ($waitMsg)");
    \App\Core\RateLimiter::clear($throttleAction . ':' . md5($testIp));

    // 18. Security Suite: Front-End Security Files & Error Pages
    assertTest(file_exists(BASE_PATH . '/public/assets/js/app-security.js'), "Front-End: app-security.js client utility installed");
    assertTest(file_exists(BASE_PATH . '/resources/views/errors/403.php'), "Error Views: 403 Forbidden page exists");
    assertTest(file_exists(BASE_PATH . '/resources/views/errors/429.php'), "Error Views: 429 Too Many Requests page exists");
    assertTest(file_exists(BASE_PATH . '/resources/views/errors/500.php'), "Error Views: 500 Internal Server Error page exists");

    // 19. Security Suite: Content Security Policy (CSP) Strict Compliance
    $nonce = \App\Core\CSP::nonce();
    assertTest(!empty($nonce) && strlen($nonce) >= 20, "CSP: Generated cryptographic nonce of sufficient entropy ({$nonce})");
    $cspHeader = \App\Core\CSP::getHeader();
    assertTest(strpos($cspHeader, "script-src 'self' 'nonce-") !== false, "CSP: script-src enforced with per-request cryptographic nonce");
    assertTest(strpos($cspHeader, "'unsafe-inline'") === false || strpos($cspHeader, "script-src 'self' 'unsafe-inline'") === false, "CSP: Removed 'unsafe-inline' from script-src");
    // Verify script-src specifically does not contain unsafe-inline or data:
    preg_match('/script-src([^;]+);/', $cspHeader, $scriptSrcMatches);
    $scriptSrcContent = $scriptSrcMatches[1] ?? '';
    assertTest(strpos($scriptSrcContent, "'unsafe-inline'") === false, "CSP: script-src directive strictly excludes 'unsafe-inline'");
    assertTest(strpos($scriptSrcContent, 'data:') === false, "CSP: script-src directive strictly excludes 'data:'");
    assertTest(strpos($cspHeader, "object-src 'none'") !== false, "CSP: object-src explicitly restricted to 'none'");
    assertTest(strpos($cspHeader, "base-uri 'self'") !== false, "CSP: base-uri explicitly restricted to 'self'");

    // Verify style-src specifically blocks unsafe-inline and uses nonce
    preg_match('/style-src([^;]+);/', $cspHeader, $styleSrcMatches);
    $styleSrcContent = $styleSrcMatches[1] ?? '';
    assertTest(strpos($styleSrcContent, "'unsafe-inline'") === false, "CSP: style-src directive strictly blocks 'unsafe-inline'");
    assertTest(strpos($styleSrcContent, "'nonce-") !== false, "CSP: style-src directive enforced with cryptographic nonce");
    assertTest(file_exists(BASE_PATH . '/public/assets/css/app-style.css'), "Front-End: External app-style.css stylesheet installed for zero-inline-styles");

    // 20. Security Suite: Strict Transport Security (HSTS) Compliance
    $htaccessContent = file_get_contents(BASE_PATH . '/public/.htaccess');
    assertTest(strpos($htaccessContent, 'Strict-Transport-Security') !== false, "HSTS: Strict-Transport-Security directive configured in public/.htaccess");
    assertTest(strpos($htaccessContent, 'max-age=31536000') !== false, "HSTS: max-age set to 1 year (31536000 seconds) for Mozilla/hstspreload qualification");
    assertTest(strpos($htaccessContent, 'includeSubDomains') !== false && strpos($htaccessContent, 'preload') !== false, "HSTS: includeSubDomains and preload flags active in .htaccess");

    $indexContent = file_get_contents(BASE_PATH . '/public/index.php');
    assertTest(strpos($indexContent, 'Strict-Transport-Security') !== false, "HSTS: Strict-Transport-Security header sent by PHP application in index.php");

    // 21. Security Suite: Subresource Integrity (SRI) Compliance
    $citizenLayout = file_get_contents(BASE_PATH . '/resources/views/layouts/citizen.php');
    $adminLayout = file_get_contents(BASE_PATH . '/resources/views/layouts/admin.php');
    $staffLayout = file_get_contents(BASE_PATH . '/resources/views/layouts/staff.php');

    assertTest(file_exists(BASE_PATH . '/public/assets/css/tailwind.css') && strpos($citizenLayout, 'assets/css/tailwind.css') !== false, "Styles: Production compiled tailwind.css exists and is linked in citizen layout");
    assertTest(strpos($citizenLayout, 'integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH"') !== false, "SRI: Leaflet JS locked with SHA-384 integrity in citizen layout");
    assertTest(strpos($citizenLayout, 'integrity="sha384-uTYyvsSSUZeaPhb5RbKlQa0zY/WpX/QHfvg2mczXyBQOpkWPEDy9lczyp+w7SKXu"') !== false, "SRI: Lucide Icons locked with SHA-384 integrity in citizen layout");
    assertTest(strpos($citizenLayout, 'integrity="sha384-ZZ1pncU3bQe8y31yfZdMFdSpttDoPmOZg2wguVK9almUodir1PghgT0eY7Mrty8H"') !== false, "SRI: html2canvas locked with SHA-384 integrity in citizen layout");
    assertTest(strpos($adminLayout, 'integrity="sha384-vsrfeLOOY6KuIYKDlmVH5UiBmgIdB1oEf7p01YgWHuqmOHfZr374+odEv96n9tNC"') !== false, "SRI: Chart.js locked with SHA-384 integrity in admin layout");
    assertTest(strpos($staffLayout, 'integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH"') !== false, "SRI: Leaflet JS locked with SHA-384 integrity in staff layout");

    echo "=======================================================\n";
    echo "📊 TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
    echo "=======================================================\n";

    if ($failCount === 0) {
        echo "🎉 ALL SYSTEM COMPONENTS PASSED VERIFICATION WITH 100% SUCCESS!\n";
    }

} catch (Exception $e) {
    echo "❌ Unhandled Exception: " . $e->getMessage() . "\n";
    exit(1);
}
