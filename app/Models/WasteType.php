<?php
namespace App\Models;

use App\Core\Database;

class WasteType {
    public static function all(): array {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM waste_types WHERE is_active = 1 ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function allWithStats(): array {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT wt.*, 
                   COUNT(wr.id) AS reports_count,
                   COALESCE(SUM(wr.actual_weight), SUM(wr.estimated_weight), 0) AS total_weight
            FROM waste_types wt
            LEFT JOIN waste_reports wr ON wt.id = wr.waste_type_id
            GROUP BY wt.id
            ORDER BY wt.id ASC
        ");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM waste_types WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function create(array $data): int {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO waste_types (name, description, icon, image, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['icon'] ?? 'trash-2',
            $data['image'] ?? null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::connect();
        $fields = ["name = ?", "description = ?", "icon = ?", "is_active = ?", "updated_at = NOW()"];
        $params = [
            $data['name'],
            $data['description'] ?? null,
            $data['icon'] ?? 'trash-2',
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];

        if (array_key_exists('image', $data)) {
            $fields[] = "image = ?";
            $params[] = $data['image'];
        }

        $params[] = $id;

        $stmt = $db->prepare("
            UPDATE waste_types
            SET " . implode(', ', $fields) . "
            WHERE id = ?
        ");
        return $stmt->execute($params);
    }


    public static function delete(int $id): bool {
        $db = Database::connect();
        // Check if referenced in reports
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM waste_reports WHERE waste_type_id = ?");
        $stmtCheck->execute([$id]);
        $count = (int)$stmtCheck->fetchColumn();

        if ($count > 0) {
            // Soft deactivate if reports exist
            $stmt = $db->prepare("UPDATE waste_types SET is_active = 0, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        }

        // Hard delete if no reports exist
        $stmt = $db->prepare("DELETE FROM waste_types WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
