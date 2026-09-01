<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$db = Database::connect();

// 1. Add image column to waste_types if not exists
echo "Checking and adding image column to waste_types...\n";
try {
    $db->exec("ALTER TABLE `waste_types` ADD COLUMN `image` VARCHAR(255) NULL AFTER `icon`");
} catch (\Exception $e) {
    echo "Column may already exist: " . $e->getMessage() . "\n";
}

// 2. Prepare destination directory
$targetDir = BASE_PATH . '/public/assets/images/waste_types';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// 3. Find brain directory images
$brainDir = 'C:/Users/bests/.gemini/antigravity-ide/brain/7d8892a4-f92e-4ab6-ab72-c37e3a4a0575';
$images = [
    1 => ['file' => 'waste_general', 'slug' => 'general.jpg', 'default_path' => 'assets/images/waste_types/general.jpg'],
    2 => ['file' => 'waste_organic', 'slug' => 'organic.jpg', 'default_path' => 'assets/images/waste_types/organic.jpg'],
    3 => ['file' => 'waste_recycle', 'slug' => 'recycle.jpg', 'default_path' => 'assets/images/waste_types/recycle.jpg'],
    4 => ['file' => 'waste_hazardous', 'slug' => 'hazardous.jpg', 'default_path' => 'assets/images/waste_types/hazardous.jpg'],
    5 => ['file' => 'waste_bulky', 'slug' => 'bulky.jpg', 'default_path' => 'assets/images/waste_types/bulky.jpg'],
    6 => ['file' => 'waste_ewaste', 'slug' => 'ewaste.jpg', 'default_path' => 'assets/images/waste_types/ewaste.jpg'],
];

foreach ($images as $id => $info) {
    // Look for matching generated file in brain dir
    $pattern = $brainDir . '/' . $info['file'] . '_*.jpg';
    $matches = glob($pattern);
    if (!empty($matches)) {
        $sourceFile = end($matches); // get latest
        $destFile = $targetDir . '/' . $info['slug'];
        copy($sourceFile, $destFile);
        echo "Copied {$sourceFile} to {$destFile}\n";
    }

    // Update database
    $stmt = $db->prepare("UPDATE waste_types SET image = ? WHERE id = ?");
    $stmt->execute([$info['default_path'], $id]);
}

echo "Waste types images updated successfully!\n";
