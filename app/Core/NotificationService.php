<?php
namespace App\Core;

use PDO;

class NotificationService {
    public static function send(int $userId, string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): void {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, type, title, message, related_type, related_id, is_read, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
        ");
        $stmt->execute([$userId, $type, $title, $message, $relatedType, $relatedId]);
    }

    public static function notifyAdmins(string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): void {
        $db = Database::connect();
        $stmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
        $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($adminIds as $adminId) {
            self::send((int)$adminId, $type, $title, $message, $relatedType, $relatedId);
        }
    }

    public static function getUnreadCount(int $userId): int {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public static function getRecent(int $userId, int $limit = 5): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function markAsRead(int $notificationId, int $userId): bool {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    public static function markAllAsRead(int $userId): bool {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0");
        return $stmt->execute([$userId]);
    }
}
