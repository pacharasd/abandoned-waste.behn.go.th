<?php
namespace App\Core;

class View {
    public static function render(string $view, array $data = []): void {
        extract($data);

        // Flash message helper
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // Current authenticated user
        $authUser = Auth::user();

        // Base URL helper
        $baseUrl = Response::baseUrl();

        $viewFile = BASE_PATH . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$view} ({$viewFile})");
        }

        require $viewFile;
    }
}
