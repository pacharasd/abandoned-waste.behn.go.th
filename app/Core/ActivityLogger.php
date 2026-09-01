<?php
namespace App\Core;

class ActivityLogger {
    public static function log(string $action, string $description, ?int $userId = null): void {
        $db = Database::connect();
        $ip = Request::ip();
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }

        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $action, $description, $ip]);
    }
}
