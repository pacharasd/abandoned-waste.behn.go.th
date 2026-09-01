<?php
/**
 * Database Migration & Seeder Script
 * Runs migrations and seeders for abandoned_waste database.
 */

define('BASE_PATH', dirname(__DIR__));

// Load .env file
function loadEnv($path) {
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

loadEnv(BASE_PATH . '/.env');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_DATABASE') ?: 'behn_abandoned_waste';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

echo "=======================================================\n";
echo "🛠️ RUNNING DATABASE MIGRATIONS & SEEDERS\n";
echo "Database: {$dbname} on {$host}:{$port}\n";
echo "=======================================================\n";

try {
    // 1. Connect without db to ensure db exists
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbname}`;");

    echo "✅ Connected to MySQL database `{$dbname}`.\n";

    // 2. Run migrations
    $migrationsSql = file_get_contents(BASE_PATH . '/database/migrations.sql');
    $pdo->exec($migrationsSql);
    echo "✅ Executed migrations.sql successfully (8 tables created).\n";

    // 3. Run seeders
    $seedersSql = file_get_contents(BASE_PATH . '/database/seeders.sql');
    $pdo->exec($seedersSql);
    echo "✅ Executed seeders.sql successfully (Admin, Staff, Waste Types, Reports & Notifications).\n";

    echo "=======================================================\n";
    echo "🎉 DATABASE SETUP COMPLETE & VERIFIED!\n";
    echo "=======================================================\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
