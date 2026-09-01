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

    /**
     * Defense-in-Depth Secure Image Upload Validator & Handler
     * - Verifies Magic Bytes via finfo MIME sniffing
     * - Verifies getimagesize()
     * - Whitelists JPEG, PNG, WEBP
     * - Generates cryptographically secure random filename
     * - Returns relative path (e.g. 'uploads/abc123.jpg') or null on failure
     */
    public static function validateAndUploadImage(?array $file, string $targetDir = 'uploads', int $maxSizeBytes = 10485760): ?string {
        if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $maxSizeBytes || $file['size'] <= 0) {
            return null;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        // 1. Verify MIME type using finfo (Magic Bytes)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/x-png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowedMimes)) {
            return null;
        }

        // 2. Extra check with getimagesize
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return null;
        }

        $safeExtension = $allowedMimes[$mime];
        $randomToken = bin2hex(random_bytes(12));
        $secureFilename = $randomToken . '_' . time() . '.' . $safeExtension;

        $targetFullPath = rtrim(BASE_PATH . '/public/' . trim($targetDir, '/'), '/') . '/';
        if (!is_dir($targetFullPath)) {
            mkdir($targetFullPath, 0755, true);
        }

        $destination = $targetFullPath . $secureFilename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return trim($targetDir, '/') . '/' . $secureFilename;
        }

        return null;
    }

    public static function ip(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public static function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }
}
