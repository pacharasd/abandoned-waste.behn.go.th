<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Core\Paginator;
use App\Core\Validator;
use App\Models\WasteReport;
use App\Models\WasteType;

class AdminReportController {
    public function index(): void {
        $filters = [
            'search' => Request::input('search', ''),
            'status' => Request::input('status', ''),
            'waste_type_id' => Request::input('waste_type_id', ''),
            'date_from' => Request::input('date_from', ''),
            'date_to' => Request::input('date_to', '')
        ];

        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 10));

        $totalItems = WasteReport::countFiltered($filters);
        $offset = ($page - 1) * $perPage;
        $reports = WasteReport::getFiltered($filters, $perPage, $offset);

        $paginator = new Paginator($reports, $totalItems, $page, $perPage);
        $wasteTypes = WasteType::all();
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        View::render('admin.reports.index', [
            'title' => 'จัดการรายการแจ้งขยะ | Admin Portal',
            'reports' => $reports,
            'paginator' => $paginator,
            'wasteTypes' => $wasteTypes,
            'filters' => $filters,
            'unreadCount' => $unreadCount
        ]);
    }

    public function show(int $id): void {
        $report = WasteReport::findById($id);

        if (!$report) {
            Response::redirect('/admin/reports', 'ไม่พบรายการที่ระบุ', 'warning');
        }

        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        View::render('admin.reports.show', [
            'title' => "รายละเอียดรายการ {$report['report_number']} | Admin Portal",
            'report' => $report,
            'unreadCount' => $unreadCount
        ]);
    }

    public function updateStatus(int $id): void {
        $report = WasteReport::findById($id);
        if (!$report) {
            Response::redirect('/admin/reports', 'ไม่พบรายการที่ระบุ', 'warning');
        }

        $newStatus = trim(Request::input('status', ''));
        $note = trim(Request::input('note', ''));
        $actualWeight = Request::input('actual_weight', null);

        $allowedStatuses = ['รอรับเรื่อง', 'กำลังตรวจสอบ', 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', 'ยกเลิก'];
        $validator = Validator::make([
            'status' => $newStatus,
            'note' => $note,
            'actual_weight' => $actualWeight
        ], [
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'note' => 'max:1000',
            'actual_weight' => 'numeric|min:0|max:100000'
        ]);

        if ($validator->fails()) {
            Response::redirect("/admin/reports/{$id}", $validator->allErrors()[0] ?? 'ข้อมูลไม่ถูกต้อง', 'danger');
        }

        // Handle After Image Upload with Strict Magic Bytes & Extension Whitelisting
        $file = Request::file('after_image');
        if ($file) {
            $uploadedPath = Request::validateAndUploadImage($file, 'uploads', 10 * 1024 * 1024);
            if ($uploadedPath) {
                WasteReport::addImage($id, $uploadedPath, 'after');
            }
        }

        // Handle Item-specific actual weights if provided
        $itemActualWeights = Request::input('item_actual_weights', []);
        if (!empty($itemActualWeights) && is_array($itemActualWeights)) {
            WasteReport::updateItemActualWeights($id, $itemActualWeights);
        }

        if ($newStatus === 'จัดเก็บเรียบร้อยแล้ว') {
            if ($actualWeight !== null && (float)$actualWeight > 0) {
                WasteReport::completeJob($id, (float)$actualWeight, $note, Auth::id());
            } else {
                // If items already updated total actual weight
                WasteReport::updateStatus($id, $newStatus, Auth::id(), $note);
            }
        } else {
            WasteReport::updateStatus($id, $newStatus, Auth::id(), $note);
        }

        ActivityLogger::log('admin_update_status', "Admin ปรับสถานะรายการ {$report['report_number']} เป็น '{$newStatus}'" . ($note ? " ({$note})" : ""), Auth::id());

        Response::redirect("/admin/reports/{$id}", "บันทึกการดำเนินการสถานะ '{$newStatus}' เรียบร้อยแล้ว", 'success');
    }


    public function export(): void {
        $filters = [
            'search' => Request::input('search', ''),
            'status' => Request::input('status', ''),
            'waste_type_id' => Request::input('waste_type_id', ''),
            'date_from' => Request::input('date_from', ''),
            'date_to' => Request::input('date_to', '')
        ];

        $reports = WasteReport::getFiltered($filters);
        $filename = 'waste_reports_export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'เลขที่รายการ',
            'ชื่อผู้แจ้ง',
            'เบอร์โทรศัพท์',
            'สถานที่ / ที่อยู่',
            'Latitude',
            'Longitude',
            'ประเภทขยะ',
            'น้ำหนักประมาณ (กก.)',
            'น้ำหนักจริง (กก.)',
            'สถานะปัจจุบัน',
            'วันที่แจ้ง',
            'วันที่ดำเนินการเสร็จ'
        ]);

        foreach ($reports as $r) {
            fputcsv($output, [
                $r['report_number'],
                $r['reporter_name'],
                $r['reporter_phone'],
                $r['address'],
                $r['latitude'],
                $r['longitude'],
                $r['waste_type_name'],
                $r['estimated_weight'],
                $r['actual_weight'] ?? '-',
                $r['status'],
                $r['submitted_at'],
                $r['completed_at'] ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }
}
