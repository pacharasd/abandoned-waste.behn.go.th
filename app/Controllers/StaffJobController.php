<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Models\WasteReport;

class StaffJobController {
    public function dashboard(): void {
        $staffId = Auth::id();
        $statusFilter = Request::input('status', null);
        $jobs = WasteReport::getStaffJobs($staffId, $statusFilter);
        $unreadCount = NotificationService::getUnreadCount($staffId);

        View::render('staff.dashboard', [
            'title' => 'งานที่ได้รับมอบหมาย | Staff Portal',
            'jobs' => $jobs,
            'statusFilter' => $statusFilter,
            'unreadCount' => $unreadCount
        ]);
    }

    public function show(int $id): void {
        $staffId = Auth::id();
        $report = WasteReport::findById($id);

        if (!$report || ($report['assigned_staff_id'] != $staffId && !Auth::isAdmin())) {
            Response::redirect('/staff/dashboard', 'คุณไม่มีสิทธิ์เข้าถึงงานนี้', 'danger');
        }

        $unreadCount = NotificationService::getUnreadCount($staffId);

        View::render('staff.job_detail', [
            'title' => "งานจัดเก็บขยะ [{$report['report_number']}] | Staff Portal",
            'report' => $report,
            'unreadCount' => $unreadCount
        ]);
    }

    public function updateStatus(int $id): void {
        $staffId = Auth::id();
        $report = WasteReport::findById($id);

        if (!$report || ($report['assigned_staff_id'] != $staffId && !Auth::isAdmin())) {
            Response::redirect('/staff/dashboard', 'คุณไม่มีสิทธิ์แก้ไขงานนี้', 'danger');
        }

        $newStatus = trim(Request::input('status', ''));
        $note = trim(Request::input('note', ''));

        $allowed = ['รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ'];
        if (!in_array($newStatus, $allowed)) {
            Response::redirect("/staff/jobs/{$id}", 'สถานะที่เลือกไม่ถูกต้อง', 'danger');
        }

        WasteReport::updateStatus($id, $newStatus, $staffId, $note);
        ActivityLogger::log('staff_update_status', "เจ้าหน้าที่อัปเดตสถานะ {$report['report_number']} เป็น '{$newStatus}'", $staffId);

        Response::redirect("/staff/jobs/{$id}", "อัปเดตสถานะเป็น '{$newStatus}' เรียบร้อยแล้ว", 'success');
    }

    public function complete(int $id): void {
        $staffId = Auth::id();
        $report = WasteReport::findById($id);

        if (!$report || ($report['assigned_staff_id'] != $staffId && !Auth::isAdmin())) {
            Response::redirect('/staff/dashboard', 'คุณไม่มีสิทธิ์แก้ไขงานนี้', 'danger');
        }

        $actualWeight = (float)Request::input('actual_weight', 0);
        $note = trim(Request::input('note', ''));

        if ($actualWeight <= 0) {
            Response::redirect("/staff/jobs/{$id}", 'กรุณาระบุน้ำหนักขยะจริงที่จัดเก็บได้ (กก.)', 'danger');
        }

        // Handle After Image Upload
        $file = Request::file('after_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $maxSize = 10 * 1024 * 1024; // 10MB

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $uploadDir = BASE_PATH . '/public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = 'report_after_' . $id . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    WasteReport::addImage($id, 'uploads/' . $newFilename, 'after');
                }
            }
        }

        // Complete Job
        WasteReport::completeJob($id, $actualWeight, $note, $staffId);

        // Notify Admins that staff has completed the job
        $staffName = Auth::user()['name'] ?? 'เจ้าหน้าที่';
        NotificationService::notifyAdmins(
            'job_completed',
            "เจ้าหน้าที่จัดเก็บขยะเรียบร้อยแล้ว [{$report['report_number']}]",
            "{$staffName} ดำเนินการจัดเก็บเสร็จสิ้นที่ {$report['address']} (น้ำหนักจริง {$actualWeight} กก.)",
            'waste_report',
            $id
        );

        ActivityLogger::log('staff_complete_job', "เจ้าหน้าที่ปิดงาน {$report['report_number']} น้ำหนักจริง {$actualWeight} กก.", $staffId);

        Response::redirect("/staff/jobs/{$id}", "บันทึกการจัดเก็บขยะและปิดงานเรียบร้อยแล้ว!", 'success');
    }
}
