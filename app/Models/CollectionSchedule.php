<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CollectionSchedule {
    public static function all(array $filters = []): array {
        $db = Database::connect();
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "s.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['year'])) {
            $conditions[] = "YEAR(s.collection_date) = ?";
            $params[] = $filters['year'];
        }

        $whereSql = implode(' AND ', $conditions);

        $stmt = $db->prepare("
            SELECT s.*, u.name as created_by_name
            FROM collection_schedules s
            LEFT JOIN users u ON u.id = s.created_by
            WHERE {$whereSql}
            ORDER BY s.collection_date DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function allWithStats(): array {
        self::syncStatuses();
        $db = Database::connect();
        $stmt = $db->query("
            SELECT s.*, 
                   u.name as created_by_name,
                   COUNT(wr.id) AS reports_count,
                   COALESCE(SUM(CASE WHEN wr.status = 'จัดเก็บเรียบร้อยแล้ว' THEN 1 ELSE 0 END), 0) AS completed_reports_count,
                   COALESCE(SUM(COALESCE(wr.actual_weight, wr.estimated_weight, 0)), 0) AS total_weight,
                   COALESCE(SUM(CASE WHEN wr.status = 'จัดเก็บเรียบร้อยแล้ว' THEN COALESCE(wr.actual_weight, wr.estimated_weight, 0) ELSE 0 END), 0) AS completed_weight
            FROM collection_schedules s
            LEFT JOIN users u ON u.id = s.created_by
            LEFT JOIN waste_reports wr ON wr.collection_schedule_id = s.id
            GROUP BY s.id
            ORDER BY s.collection_date DESC
        ");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        self::syncStatuses();
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT s.*, 
                   u.name as created_by_name,
                   COUNT(wr.id) AS reports_count,
                   COALESCE(SUM(CASE WHEN wr.status = 'จัดเก็บเรียบร้อยแล้ว' THEN 1 ELSE 0 END), 0) AS completed_reports_count,
                   COALESCE(SUM(COALESCE(wr.actual_weight, wr.estimated_weight, 0)), 0) AS total_weight,
                   COALESCE(SUM(CASE WHEN wr.status = 'จัดเก็บเรียบร้อยแล้ว' THEN COALESCE(wr.actual_weight, wr.estimated_weight, 0) ELSE 0 END), 0) AS completed_weight
            FROM collection_schedules s
            LEFT JOIN users u ON u.id = s.created_by
            LEFT JOIN waste_reports wr ON wr.collection_schedule_id = s.id
            WHERE s.id = ?
            GROUP BY s.id
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Automatically sync status based on cutoff_date and collection_date
     */
    public static function syncStatuses(): void {
        $db = Database::connect();

        // 1. If round was 'active', but cutoff_date has passed (<= NOW()) and collection_date is today -> 'collecting'
        $db->exec("
            UPDATE collection_schedules 
            SET status = 'collecting' 
            WHERE status = 'active' 
              AND cutoff_date IS NOT NULL 
              AND cutoff_date <= NOW() 
              AND collection_date = CURDATE()
        ");

        // 2. If collection_date has passed in the past (< CURDATE()) -> 'completed'
        $db->exec("
            UPDATE collection_schedules 
            SET status = 'completed' 
            WHERE status IN ('active', 'collecting') 
              AND collection_date < CURDATE()
        ");

        // 3. Ensure the earliest future round whose cutoff has NOT passed is promoted to 'active'
        $activeCount = (int)$db->query("
            SELECT COUNT(*) FROM collection_schedules 
            WHERE status = 'active' 
              AND (cutoff_date IS NULL OR cutoff_date > NOW()) 
              AND collection_date >= CURDATE()
        ")->fetchColumn();

        if ($activeCount === 0) {
            $nextUpcomingId = $db->query("
                SELECT id FROM collection_schedules 
                WHERE status = 'upcoming' 
                  AND (cutoff_date IS NULL OR cutoff_date > NOW()) 
                  AND collection_date >= CURDATE()
                ORDER BY collection_date ASC 
                LIMIT 1
            ")->fetchColumn();

            if ($nextUpcomingId) {
                $db->prepare("UPDATE collection_schedules SET status = 'active' WHERE id = ?")->execute([$nextUpcomingId]);
            }
        }
    }

    public static function getActiveOrNext(): ?array {
        self::syncStatuses();
        $db = Database::connect();

        // 1. Find the earliest open round whose cutoff_date has NOT passed yet!
        $stmt = $db->prepare("
            SELECT s.*, 
                   COUNT(wr.id) AS reports_count,
                   COALESCE(SUM(COALESCE(wr.actual_weight, wr.estimated_weight, 0)), 0) AS total_weight
            FROM collection_schedules s
            LEFT JOIN waste_reports wr ON wr.collection_schedule_id = s.id
            WHERE s.status IN ('active', 'upcoming') 
              AND (s.cutoff_date IS NULL OR s.cutoff_date > NOW()) 
              AND s.collection_date >= CURDATE()
            GROUP BY s.id
            ORDER BY 
                CASE WHEN s.status = 'active' THEN 1 ELSE 2 END,
                s.collection_date ASC
            LIMIT 1
        ");
        $stmt->execute();
        $schedule = $stmt->fetch();

        // Fallback: If all future rounds have passed cutoff, find any future round
        if (!$schedule) {
            $stmt = $db->prepare("
                SELECT s.*, 
                       COUNT(wr.id) AS reports_count,
                       COALESCE(SUM(COALESCE(wr.actual_weight, wr.estimated_weight, 0)), 0) AS total_weight
                FROM collection_schedules s
                LEFT JOIN waste_reports wr ON wr.collection_schedule_id = s.id
                WHERE s.status NOT IN ('completed', 'cancelled')
                  AND s.collection_date >= CURDATE()
                GROUP BY s.id
                ORDER BY s.collection_date ASC
                LIMIT 1
            ");
            $stmt->execute();
            $schedule = $stmt->fetch();
        }

        return $schedule ?: null;
    }

    public static function getUpcomingList(int $limit = 6): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT s.*, 
                   COUNT(wr.id) AS reports_count,
                   COALESCE(SUM(COALESCE(wr.actual_weight, wr.estimated_weight, 0)), 0) AS total_weight
            FROM collection_schedules s
            LEFT JOIN waste_reports wr ON wr.collection_schedule_id = s.id
            WHERE s.collection_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            GROUP BY s.id
            ORDER BY s.collection_date ASC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getReports(int $scheduleId): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT wr.*, 
                   wt.name as waste_type_name, wt.icon as waste_type_icon,
                   u.name as staff_name
            FROM waste_reports wr
            LEFT JOIN waste_types wt ON wt.id = wr.waste_type_id
            LEFT JOIN users u ON u.id = wr.assigned_staff_id
            WHERE wr.collection_schedule_id = ?
            ORDER BY wr.id DESC
        ");
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO collection_schedules (
                title, collection_date, start_time, end_time, area_zone, 
                cutoff_date, description, status, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $data['title'],
            $data['collection_date'],
            $data['start_time'] ?? '09:00:00',
            $data['end_time'] ?? '16:00:00',
            $data['area_zone'] ?? 'ครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี',
            !empty($data['cutoff_date']) ? $data['cutoff_date'] : null,
            $data['description'] ?? null,
            $data['status'] ?? 'upcoming',
            $data['created_by'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::connect();
        $fields = [];
        $params = [];

        $allowed = ['title', 'collection_date', 'start_time', 'end_time', 'area_zone', 'cutoff_date', 'description', 'status'];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "`{$f}` = ?";
                $params[] = ($f === 'cutoff_date' && empty($data[$f])) ? null : $data[$f];
            }
        }

        $fields[] = "`updated_at` = NOW()";
        $params[] = $id;

        $stmt = $db->prepare("
            UPDATE collection_schedules
            SET " . implode(', ', $fields) . "
            WHERE id = ?
        ");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool {
        $db = Database::connect();
        // Unlink reports before deletion
        $db->prepare("UPDATE waste_reports SET collection_schedule_id = NULL WHERE collection_schedule_id = ?")->execute([$id]);
        $stmt = $db->prepare("DELETE FROM collection_schedules WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
