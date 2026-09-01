<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::connect();

echo "Creating waste_report_items table...\n";

$db->exec("
CREATE TABLE IF NOT EXISTS `waste_report_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `waste_report_id` BIGINT UNSIGNED NOT NULL,
    `waste_type_id` BIGINT UNSIGNED NOT NULL,
    `estimated_weight` DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
    `actual_weight` DECIMAL(8, 2) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_report_id` (`waste_report_id`),
    INDEX `idx_type_id` (`waste_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Populate existing reports if table is empty
$count = $db->query("SELECT COUNT(*) FROM waste_report_items")->fetchColumn();
if ($count == 0) {
    echo "Migrating existing reports into items table...\n";
    $db->exec("
        INSERT INTO waste_report_items (waste_report_id, waste_type_id, estimated_weight, actual_weight, created_at, updated_at)
        SELECT id, waste_type_id, estimated_weight, actual_weight, created_at, updated_at
        FROM waste_reports
    ");
}

echo "Migration completed successfully! Items count: " . $db->query("SELECT COUNT(*) FROM waste_report_items")->fetchColumn() . "\n";
