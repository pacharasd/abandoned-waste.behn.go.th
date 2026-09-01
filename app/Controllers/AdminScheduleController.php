<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Core\Paginator;
use App\Models\CollectionSchedule;
use App\Models\User;

class AdminScheduleController {
    public function index(): void {
        $allSchedules = CollectionSchedule::allWithStats();
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        // Quick metrics
        $totalSchedules = count($allSchedules);
        $activeSchedules = 0;
        $totalReportsInSchedules = 0;
        $totalWeightInSchedules = 0.0;

        foreach ($allSchedules as $s) {
            if ($s['status'] === 'active' || $s['status'] === 'upcoming' || $s['status'] === 'collecting') {
                $activeSchedules++;
            }
            $totalReportsInSchedules += (int)($s['reports_count'] ?? 0);
            $totalWeightInSchedules += (float)($s['total_weight'] ?? 0);
        }

        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 8));
        $paginator = Paginator::fromArray($allSchedules, $page, $perPage);
        $schedules = $paginator->items;

        View::render('admin.schedules.index', [
            'title' => 'จัดการรอบวันจัดเก็บขยะประจำเดือน | Admin Portal',
            'schedules' => $schedules,
            'paginator' => $paginator,
            'unreadCount' => $unreadCount,
            'metrics' => [
                'total' => $totalSchedules,
                'active' => $activeSchedules,
                'total_reports' => $totalReportsInSchedules,
                'total_weight' => $totalWeightInSchedules
            ]
        ]);
    }

    public function store(): void {
        $title = trim(Request::input('title', ''));
        $collectionDate = trim(Request::input('collection_date', ''));
        $startTime = trim(Request::input('start_time', '09:00'));
        $endTime = trim(Request::input('end_time', '16:00'));
        $areaZone = trim(Request::input('area_zone', 'ครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี'));
        $cutoffDate = trim(Request::input('cutoff_date', ''));
        $description = trim(Request::input('description', ''));
        $status = trim(Request::input('status', 'upcoming'));

        if (empty($title) || empty($collectionDate)) {
            Response::redirect('/admin/schedules', 'กรุณาระบุชื่อรอบและวันที่จัดเก็บ', 'danger');
        }

        // Format times
        if (strlen($startTime) === 5) $startTime .= ':00';
        if (strlen($endTime) === 5) $endTime .= ':00';

        $id = CollectionSchedule::create([
            'title' => $title,
            'collection_date' => $collectionDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'area_zone' => $areaZone,
            'cutoff_date' => !empty($cutoffDate) ? $cutoffDate : null,
            'description' => $description,
            'status' => $status,
            'created_by' => Auth::id()
        ]);

        ActivityLogger::log('create_schedule', "Admin สร้างรอบจัดเก็บขยะใหม่: '{$title}' วันที่ {$collectionDate} (ID: {$id})", Auth::id());

        Response::redirect('/admin/schedules', "สร้างรอบจัดเก็บขยะ '{$title}' เรียบร้อยแล้ว", 'success');
    }

    public function show(int $id): void {
        $schedule = CollectionSchedule::findById($id);
        if (!$schedule) {
            Response::redirect('/admin/schedules', 'ไม่พบข้อมูลรอบจัดเก็บที่ระบุ', 'warning');
        }

        $allReports = CollectionSchedule::getReports($id);
        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 10));
        $paginator = Paginator::fromArray($allReports, $page, $perPage);
        $reports = $paginator->items;

        $unreadCount = NotificationService::getUnreadCount(Auth::id());
        $staffMembers = User::getAllStaff();

        View::render('admin.schedules.show', [
            'title' => "{$schedule['title']} | Admin Portal",
            'schedule' => $schedule,
            'reports' => $reports,
            'paginator' => $paginator,
            'allReports' => $allReports,
            'staffMembers' => $staffMembers,
            'unreadCount' => $unreadCount
        ]);
    }

    public function update(int $id): void {
        $schedule = CollectionSchedule::findById($id);
        if (!$schedule) {
            Response::redirect('/admin/schedules', 'ไม่พบข้อมูลรอบจัดเก็บที่ระบุ', 'warning');
        }

        $title = trim(Request::input('title', ''));
        $collectionDate = trim(Request::input('collection_date', ''));
        $startTime = trim(Request::input('start_time', '09:00'));
        $endTime = trim(Request::input('end_time', '16:00'));
        $areaZone = trim(Request::input('area_zone', ''));
        $cutoffDate = trim(Request::input('cutoff_date', ''));
        $description = trim(Request::input('description', ''));
        $status = trim(Request::input('status', 'upcoming'));

        if (empty($title) || empty($collectionDate)) {
            Response::redirect("/admin/schedules/{$id}", 'กรุณาระบุชื่อรอบและวันที่จัดเก็บ', 'danger');
        }

        if (strlen($startTime) === 5) $startTime .= ':00';
        if (strlen($endTime) === 5) $endTime .= ':00';

        CollectionSchedule::update($id, [
            'title' => $title,
            'collection_date' => $collectionDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'area_zone' => $areaZone,
            'cutoff_date' => !empty($cutoffDate) ? $cutoffDate : null,
            'description' => $description,
            'status' => $status
        ]);

        ActivityLogger::log('update_schedule', "Admin แก้ไขรอบจัดเก็บขยะ: '{$title}' (ID: {$id})", Auth::id());

        Response::redirect("/admin/schedules/{$id}", 'บันทึกการแก้ไขรอบจัดเก็บเรียบร้อยแล้ว', 'success');
    }

    public function delete(int $id): void {
        $schedule = CollectionSchedule::findById($id);
        if (!$schedule) {
            Response::redirect('/admin/schedules', 'ไม่พบข้อมูลรอบจัดเก็บที่ระบุ', 'warning');
        }

        CollectionSchedule::delete($id);
        ActivityLogger::log('delete_schedule', "Admin ลบรอบจัดเก็บขยะ: '{$schedule['title']}' (ID: {$id})", Auth::id());

        Response::redirect('/admin/schedules', "ลบรอบจัดเก็บ '{$schedule['title']}' เรียบร้อยแล้ว", 'success');
    }

    public function quickGenerate(): void {
        // Find the latest schedule date or use next month
        $latest = CollectionSchedule::all();
        $baseDate = !empty($latest) ? strtotime($latest[0]['collection_date']) : time();

        // Calculate next month's last Sunday (e.g. 4th Sunday)
        $nextMonth = strtotime("+1 month", $baseDate);
        $year = date('Y', $nextMonth);
        $month = date('m', $nextMonth);
        
        // Find last Sunday of that month
        $lastDay = date('t', $nextMonth);
        $lastSunday = date('Y-m-d', strtotime("last Sunday of {$year}-{$month}-{$lastDay} 23:59:59"));
        if (strtotime($lastSunday) < strtotime("{$year}-{$month}-01")) {
            $lastSunday = date('Y-m-d', strtotime("fourth Sunday of {$year}-{$month}"));
        }

        $monthNamesThai = [
            '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
            '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
            '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
        ];
        $monthName = $monthNamesThai[$month] ?? $month;
        $thaiYear = (int)$year + 543;

        $title = "รอบจัดเก็บขยะไร้บ้านและขยะชิ้นใหญ่ ประจำเดือน{$monthName} {$thaiYear}";
        $cutoff = date('Y-m-d 18:00:00', strtotime("-2 days", strtotime($lastSunday)));

        $id = CollectionSchedule::create([
            'title' => $title,
            'collection_date' => $lastSunday,
            'start_time' => '09:00:00',
            'end_time' => '16:00:00',
            'area_zone' => 'ครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี',
            'cutoff_date' => $cutoff,
            'description' => "รอบจัดเก็บขยะชิ้นใหญ่ประจำเดือน{$monthName} ประชาชนสามารถแจ้งจัดเก็บล่วงหน้าได้ถึงวันที่ " . date('d/m/Y', strtotime($cutoff)),
            'status' => 'upcoming',
            'created_by' => Auth::id()
        ]);

        ActivityLogger::log('quick_generate_schedule', "Admin สร้างรอบจัดเก็บขยะอัตโนมัติ: '{$title}' (ID: {$id})", Auth::id());

        Response::redirect('/admin/schedules', "สร้างรอบจัดเก็บประจำเดือน{$monthName} เรียบร้อยแล้ว", 'success');
    }
}
