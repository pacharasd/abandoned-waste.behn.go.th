<?php
namespace App\Core;

class Request {
    public static function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function uri(): string {
        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Strip query string first
        $pos = strpos($rawUri, '?');
        if ($pos !== false) {
            $rawUri = substr($rawUri, 0, $pos);
        }

        // Decode percent-encoded Unicode/Thai characters
        $uri = rawurldecode($rawUri);

        // Normalize slashes for Windows Apache
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $baseDir = str_replace('\\', '/', dirname($scriptName));
        $parentDir = str_replace('\\', '/', dirname($baseDir));

        // 1. Strip base directory if accessed with /public (e.g. /ขยะไร้บ้าน/public)
        if ($baseDir !== '/' && $baseDir !== '.' && $baseDir !== '' && strpos($uri, $baseDir) === 0) {
            $uri = substr($uri, strlen($baseDir));
        }
        // 2. Strip parent directory if accessed via root rewrite (e.g. /ขยะไร้บ้าน)
        elseif ($parentDir !== '/' && $parentDir !== '.' && $parentDir !== '' && strpos($uri, $parentDir) === 0) {
            $uri = substr($uri, strlen($parentDir));
        }

        // 3. Strip index.php if present
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }

        $cleaned = '/' . trim($uri, '/');
        return $cleaned === '//' ? '/' : $cleaned;
    }

    public static function all(): array {
        return array_merge($_GET, $_POST);
    }

    public static function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function file(string $key): ?array {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            return $_FILES[$key];
        }
        return null;
    }

    public static function ip(): string {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public static function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }
}
