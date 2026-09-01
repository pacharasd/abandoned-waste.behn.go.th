<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\Paginator;
use App\Models\WasteReport;
use App\Models\WasteType;
use App\Models\User;

class AdminDashboardController {
    public function index(): void {
        $metrics = WasteReport::getDashboardMetrics();
        $wasteTypes = WasteType::all();
        $allReports = WasteReport::getFiltered();
        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 8));
        $paginator = Paginator::fromArray($allReports, $page, $perPage);
        $recentReports = $paginator->items;

        $staffList = User::getAllStaff();
        $wasteTypeStats = WasteReport::getWasteTypeStats();
        $monthlyTrend = WasteReport::getMonthlyTrend();

        $unreadNotifications = NotificationService::getUnreadCount(Auth::id());
        $recentNotifications = NotificationService::getRecent(Auth::id(), 5);

        View::render('admin.dashboard', [
            'title' => 'Admin Dashboard | ระบบแจ้งจัดเก็บขยะไร้บ้าน',
            'metrics' => $metrics,
            'wasteTypes' => $wasteTypes,
            'recentReports' => $recentReports,
            'paginator' => $paginator,
            'allReports' => $allReports,
            'staffList' => $staffList,
            'wasteTypeStats' => $wasteTypeStats,
            'monthlyTrend' => $monthlyTrend,
            'unreadCount' => $unreadNotifications,
            'recentNotifications' => $recentNotifications
        ]);
    }

    public function statsApi(): void {
        $metrics = WasteReport::getDashboardMetrics();
        $wasteTypeStats = WasteReport::getWasteTypeStats();
        $monthlyTrend = WasteReport::getMonthlyTrend();

        Response::json([
            'metrics' => $metrics,
            'wasteTypeStats' => $wasteTypeStats,
            'monthlyTrend' => $monthlyTrend
        ]);
    }
}
