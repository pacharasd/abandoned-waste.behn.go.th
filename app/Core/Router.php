<?php
namespace App\Core;

class Router {
    private static array $routes = [];

    public static function get(string $path, $handler, array $middlewares = []): void {
        self::addRoute('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, $handler, array $middlewares = []): void {
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    private static function addRoute(string $method, string $path, $handler, array $middlewares): void {
        self::$routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch(): void {
        $method = Request::method();
        $uri = Request::uri();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Pattern match with named parameters: /admin/reports/{id}
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                // Run Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    self::runMiddleware($middleware);
                }

                // Check CSRF for POST requests if not skipped
                if ($method === 'POST' && !in_array('no_csrf', $route['middlewares'])) {
                    $token = Request::input('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                    if (!CSRF::validate($token)) {
                        if (Request::isAjax()) {
                            Response::json(['success' => false, 'message' => 'CSRF token mismatch. กรุณารีเฟรชหน้าเว็บ'], 419);
                        } else {
                            Response::redirect('/', 'เกิดข้อผิดพลาดด้านความปลอดภัย (CSRF Token Mismatch) กรุณาลองใหม่อีกครั้ง', 'danger');
                        }
                    }
                }

                // Call Handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func_array($handler, array_values($params));
                    return;
                }

                if (is_string($handler) && strpos($handler, '@') !== false) {
                    list($controllerName, $action) = explode('@', $handler);
                    $fullController = "App\\Controllers\\{$controllerName}";

                    if (!class_exists($fullController)) {
                        die("Controller not found: {$fullController}");
                    }

                    $controller = new $fullController();
                    if (!method_exists($controller, $action)) {
                        die("Action {$action} not found in {$fullController}");
                    }

                    call_user_func_array([$controller, $action], array_values($params));
                    return;
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        if (Request::isAjax()) {
            Response::json(['error' => 'Route not found'], 404);
        } else {
            View::render('errors.404', ['title' => 'ไม่พบหน้าที่ต้องการ (404 Not Found)']);
        }
    }

    private static function runMiddleware(string $name): void {
        if ($name === 'auth') {
            if (!Auth::check()) {
                Response::redirect('/login', 'กรุณาเข้าสู่ระบบก่อนใช้งาน', 'warning');
            }
        } elseif ($name === 'auth:admin') {
            if (!Auth::check() || !Auth::isAdmin()) {
                Response::redirect('/login', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะ Admin)', 'danger');
            }
        } elseif ($name === 'auth:staff') {
            if (!Auth::check() || (!Auth::isStaff() && !Auth::isAdmin())) {
                Response::redirect('/login', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะเจ้าหน้าที่)', 'danger');
            }
        } elseif (strpos($name, 'throttle:') === 0) {
            list(, $config) = explode(':', $name, 2);
            $parts = explode(',', $config);
            $maxAttempts = max(1, (int)($parts[0] ?? 60));
            $decaySeconds = max(1, (int)($parts[1] ?? 60));
            $ip = Request::ip();
            $action = 'route_' . md5(Request::uri());

            if (!RateLimiter::checkAndHit($action, $ip, $maxAttempts, $decaySeconds)) {
                $waitText = RateLimiter::getWaitTimeText($action, $ip);
                http_response_code(429);
                if (Request::isAjax()) {
                    Response::json([
                        'success' => false,
                        'message' => "คุณส่งคำขอถี่เกินกำหนด กรุณารอ{$waitText}ก่อนลองใหม่อีกครั้ง"
                    ], 429);
                } else {
                    View::render('errors.429', [
                        'title' => 'คำขอมากเกินกำหนด (429 Too Many Requests)',
                        'waitText' => $waitText
                    ]);
                }
                exit;
            }
        }
    }
}
