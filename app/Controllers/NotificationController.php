<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\Database;
use App\Core\Paginator;
use PDO;

class NotificationController {
    public function index(): void {
        $userId = Auth::id();
        $db = Database::connect();
        
        $countStmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $totalItems = (int)$countStmt->fetchColumn();

        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 10));
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute([$userId]);
        $notifications = $stmt->fetchAll();
        $paginator = new Paginator($notifications, $totalItems, $page, $perPage);
        $unreadCount = NotificationService::getUnreadCount($userId);

        View::render('admin.notifications.index', [
            'title' => 'การแจ้งเตือนภายในระบบ | Admin Portal',
            'notifications' => $notifications,
            'paginator' => $paginator,
            'unreadCount' => $unreadCount
        ]);
    }

    public function markAsRead(int $id): void {
        $userId = Auth::id();
        NotificationService::markAsRead($id, $userId);

        $db = Database::connect();
        $stmt = $db->prepare("SELECT related_type, related_id FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        $n = $stmt->fetch();

        if (Request::isAjax()) {
            Response::json(['success' => true]);
        }

        if ($n && $n['related_type'] === 'waste_report' && $n['related_id']) {
            if (Auth::isAdmin()) {
                Response::redirect("/admin/reports/{$n['related_id']}");
            } else {
                Response::redirect("/staff/jobs/{$n['related_id']}");
            }
        }

        Response::redirect('/admin/notifications');
    }

    public function markAllAsRead(): void {
        $userId = Auth::id();
        NotificationService::markAllAsRead($userId);

        if (Request::isAjax()) {
            Response::json(['success' => true]);
        }

        Response::redirect('/admin/notifications', 'ทำเครื่องหมายว่าอ่านทั้งหมดแล้ว', 'success');
    }

    public function unreadCountApi(): void {
        $userId = Auth::id();
        $count = $userId ? NotificationService::getUnreadCount($userId) : 0;
        $recent = $userId ? NotificationService::getRecent($userId, 5) : [];
        Response::json(['count' => $count, 'recent' => $recent]);
    }
}
