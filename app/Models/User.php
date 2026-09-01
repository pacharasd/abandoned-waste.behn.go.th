<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    public static function findById(int $id): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function getAllStaff(): array {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT u.*, 
                   COUNT(CASE WHEN r.status IN ('มอบหมายงานแล้ว', 'รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ') THEN 1 END) as active_jobs_count,
                   COUNT(CASE WHEN r.status = 'จัดเก็บเรียบร้อยแล้ว' THEN 1 END) as completed_jobs_count
            FROM users u
            LEFT JOIN waste_reports r ON r.assigned_staff_id = u.id
            WHERE u.role = 'staff'
            GROUP BY u.id
            ORDER BY u.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function getStaffMembers(): array {
        return self::getAllStaff();
    }

    public static function create(array $data): int {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO users (name, email, phone, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $hashed,
            $data['role'] ?? 'staff'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::connect();
        $fields = ["name = ?", "email = ?", "phone = ?", "role = ?", "updated_at = NOW()"];
        $params = [$data['name'], $data['email'], $data['phone'] ?? null, $data['role'] ?? 'staff'];

        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        return $stmt->execute([$id]);
    }
}
