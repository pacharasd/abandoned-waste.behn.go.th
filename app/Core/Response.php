<?php
namespace App\Core;

class Response {
    public static function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function redirect(string $url, ?string $flashMessage = null, string $flashType = 'success'): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($flashMessage) {
            $_SESSION['flash'] = [
                'type' => $flashType,
                'message' => $flashMessage
            ];
        }
        // If relative URL, prefix with base
        if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
            $base = self::baseUrl();
            $url = $base . $url;
        }
        header("Location: {$url}");
        exit;
    }

    public static function baseUrl(): string {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = str_replace('\\', '/', dirname($scriptName));
        return ($base === '/' || $base === '\\' || $base === '.') ? '' : $base;
    }
}
