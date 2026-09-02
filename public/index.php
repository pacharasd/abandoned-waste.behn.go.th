<?php
/**
 * Front Controller: ระบบแจ้งจัดเก็บขยะไร้บ้าน
 */

define('BASE_PATH', dirname(__DIR__));

// Security: Session Hardening (Configured before session_start)
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if ($isHttps) {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Security: Global HTTP Security Headers
if (!headers_sent()) {
    // HTTP Strict Transport Security (HSTS) - 1 Year with SubDomains and Preload
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(self), camera=(), microphone=()');

    // Strict, Nonce-based Content Security Policy (No 'unsafe-inline' in script-src, object-src 'none')
    \App\Core\CSP::sendHeader();
}

// Global Exception & Error Handler (Zero Information Leakage in Production)
set_exception_handler(function (\Throwable $e) {
    error_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    $isDebug = strtolower(getenv('APP_DEBUG') ?: 'false') === 'true';
    if ($isDebug) {
        http_response_code(500);
        echo "<div style='font-family:sans-serif;padding:20px;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;margin:20px;'>";
        echo "<h2 style='margin-top:0;'>💥 Uncaught Exception (Debug Mode)</h2>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Location:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre style='background:#fff;padding:12px;border-radius:4px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        if (\App\Core\Request::isAjax()) {
            \App\Core\Response::json(['error' => 'Internal server error', 'message' => 'เกิดข้อผิดพลาดของระบบ กรุณาลองใหม่อีกครั้ง'], 500);
        } else {
            \App\Core\View::render('errors.500', ['title' => 'เกิดข้อผิดพลาดของระบบ (500)']);
        }
    }
});

// 2. Load Environment (.env)
function loadEnvFile(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
        }
    }
}

loadEnvFile(BASE_PATH . '/.env');

// Set Timezone to Asia/Bangkok
date_default_timezone_set('Asia/Bangkok');

// 3. Load Routes and Dispatch
require_once BASE_PATH . '/routes/web.php';

\App\Core\Router::dispatch();
