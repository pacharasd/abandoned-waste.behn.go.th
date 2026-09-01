<?php
namespace App\Core;

use App\Models\User;

class Auth {
    public static function check(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Inactivity timeout (2 hours)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
            self::logout();
            return false;
        }
        $_SESSION['last_activity'] = time();

        // Anti-Session Hijacking Fingerprint
        $currentFingerprint = md5(($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        if (isset($_SESSION['auth_fingerprint']) && $_SESSION['auth_fingerprint'] !== $currentFingerprint) {
            self::logout();
            return false;
        }

        return true;
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int {
        return self::check() ? ($_SESSION['user_id'] ?? null) : null;
    }

    public static function role(): ?string {
        return self::check() ? ($_SESSION['user']['role'] ?? null) : null;
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function isStaff(): bool {
        return self::role() === 'staff';
    }

    public static function login(array $user): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role']
        ];
        $_SESSION['last_activity'] = time();
        $_SESSION['auth_fingerprint'] = md5(($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . ($_SERVER['REMOTE_ADDR'] ?? ''));
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
