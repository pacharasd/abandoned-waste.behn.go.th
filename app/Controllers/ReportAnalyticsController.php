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

class ReportAnalyticsController {
    public function index(): void {
        $metrics = WasteReport::getDashboardMetrics();
        $allWasteTypeStats = WasteReport::getWasteTypeStats();
        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 8));
        $paginator = Paginator::fromArray($allWasteTypeStats, $page, $perPage);
        $wasteTypeStats = $paginator->items;

        $monthlyTrend = WasteReport::getMonthlyTrend();
        $staffList = User::getAllStaff();
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        View::render('admin.analytics.index', [
            'title' => 'รายงานและสถิติการจัดเก็บขยะ | Admin Portal',
            'metrics' => $metrics,
            'wasteTypeStats' => $wasteTypeStats,
            'allWasteTypeStats' => $allWasteTypeStats,
            'paginator' => $paginator,
            'monthlyTrend' => $monthlyTrend,
            'staffList' => $staffList,
            'unreadCount' => $unreadCount
        ]);
    }
}
