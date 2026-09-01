<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class WasteReport {
    public static function generateReportNumber(): string {
        $db = Database::connect();
        $year = date('Y');
        $prefix = "WB-{$year}-";

        $stmt = $db->prepare("
            SELECT report_number FROM waste_reports 
            WHERE report_number LIKE ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute(["{$prefix}%"]);
        $lastReport = $stmt->fetch();

        if ($lastReport) {
            $lastNum = (int)substr($lastReport['report_number'], strlen($prefix));
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    public static function getItems(int $reportId): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT wri.*, wt.name as waste_type_name, wt.icon as waste_type_icon
            FROM waste_report_items wri
            JOIN waste_types wt ON wt.id = wri.waste_type_id
            WHERE wri.waste_report_id = ?
            ORDER BY wri.id ASC
        ");
        $stmt->execute([$reportId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::connect();
        $reportNumber = self::generateReportNumber();

        // Process items
        $items = $data['items'] ?? [];
        $totalEstimated = 0.0;
        $primaryTypeId = $data['waste_type_id'] ?? 1;

        if (!empty($items)) {
            foreach ($items as $item) {
                $totalEstimated += (float)($item['estimated_weight'] ?? 0);
            }
            if (!empty($items[0]['waste_type_id'])) {
                $primaryTypeId = (int)$items[0]['waste_type_id'];
            }
        } else {
            $totalEstimated = (float)($data['estimated_weight'] ?? 0.0);
            $items = [
                ['waste_type_id' => $primaryTypeId, 'estimated_weight' => $totalEstimated]
            ];
        }

        // Determine collection schedule if not provided
        $scheduleId = !empty($data['collection_schedule_id']) ? (int)$data['collection_schedule_id'] : null;
        if (!$scheduleId) {
            $activeSched = CollectionSchedule::getActiveOrNext();
            if ($activeSched) {
                $scheduleId = (int)$activeSched['id'];
            }
        }

        $stmt = $db->prepare("
            INSERT INTO waste_reports (
                report_number, reporter_name, reporter_phone, address, 
                latitude, longitude, waste_type_id, collection_schedule_id, estimated_weight, 
                description, status, submitted_at, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'รอรับเรื่อง', NOW(), NOW(), NOW())
        ");

        $stmt->execute([
            $reportNumber,
            $data['reporter_name'],
            $data['reporter_phone'],
            $data['address'],
            $data['latitude'],
            $data['longitude'],
            $primaryTypeId,
            $scheduleId,
            $totalEstimated,
            $data['description'] ?? null
        ]);

        $reportId = (int)$db->lastInsertId();

        // Insert each waste item into waste_report_items
        $stmtItem = $db->prepare("
            INSERT INTO waste_report_items (waste_report_id, waste_type_id, estimated_weight, actual_weight, created_at, updated_at)
            VALUES (?, ?, ?, NULL, NOW(), NOW())
        ");

        foreach ($items as $item) {
            $stmtItem->execute([
                $reportId,
                (int)$item['waste_type_id'],
                (float)($item['estimated_weight'] ?? 0.0)
            ]);
        }

        // Automatically record initial status history
        $stmtHist = $db->prepare("
            INSERT INTO status_histories (waste_report_id, old_status, new_status, changed_by, note, created_at)
            VALUES (?, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านระบบ', NOW())
        ");
        $stmtHist->execute([$reportId]);

        return $reportId;
    }

    public static function findById(int $id): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT r.*, 
                   wt.name as waste_type_name, wt.icon as waste_type_icon,
                   cs.title as schedule_title, cs.collection_date as schedule_date, cs.start_time as schedule_start_time, cs.end_time as schedule_end_time,
                   u.name as staff_name, u.phone as staff_phone, u.email as staff_email
            FROM waste_reports r
            LEFT JOIN waste_types wt ON wt.id = r.waste_type_id
            LEFT JOIN collection_schedules cs ON cs.id = r.collection_schedule_id
            LEFT JOIN users u ON u.id = r.assigned_staff_id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $report = $stmt->fetch();

        if ($report) {
            $report['items'] = self::getItems($id);
            $report['images'] = self::getImages($id);
            $report['history'] = self::getStatusHistory($id);
            $report['assignments'] = self::getAssignments($id);
        }

        return $report ?: null;
    }

    public static function findByReportNumber(string $reportNumber): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT r.*, 
                   wt.name as waste_type_name, wt.icon as waste_type_icon,
                   cs.title as schedule_title, cs.collection_date as schedule_date, cs.start_time as schedule_start_time, cs.end_time as schedule_end_time,
                   u.name as staff_name, u.phone as staff_phone
            FROM waste_reports r
            LEFT JOIN waste_types wt ON wt.id = r.waste_type_id
            LEFT JOIN collection_schedules cs ON cs.id = r.collection_schedule_id
            LEFT JOIN users u ON u.id = r.assigned_staff_id
            WHERE r.report_number = ?
        ");
        $stmt->execute([trim($reportNumber)]);
        $report = $stmt->fetch();

        if ($report) {
            $report['items'] = self::getItems((int)$report['id']);
            $report['images'] = self::getImages((int)$report['id']);
            $report['history'] = self::getStatusHistory((int)$report['id']);
        }

        return $report ?: null;
    }


    public static function searchByPhone(string $phone): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT r.*, wt.name as waste_type_name, u.name as staff_name
            FROM waste_reports r
            LEFT JOIN waste_types wt ON wt.id = r.waste_type_id
            LEFT JOIN users u ON u.id = r.assigned_staff_id
            WHERE r.reporter_phone LIKE ?
            ORDER BY r.created_at DESC
        ");
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $stmt->execute(["%{$cleanPhone}%"]);
        return $stmt->fetchAll();
    }

    public static function getFiltered(array $filters = [], ?int $limit = null, int $offset = 0): array {
        $db = Database::connect();
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(r.report_number LIKE ? OR r.reporter_name LIKE ? OR r.reporter_phone LIKE ? OR r.address LIKE ?)";
            $term = "%{$filters['search']}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['status'])) {
            $conditions[] = "r.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['waste_type_id'])) {
            $conditions[] = "r.waste_type_id = ?";
            $params[] = $filters['waste_type_id'];
        }

        if (!empty($filters['assigned_staff_id'])) {
            $conditions[] = "r.assigned_staff_id = ?";
            $params[] = $filters['assigned_staff_id'];
        }

        if (!empty($filters['collection_schedule_id'])) {
            $conditions[] = "r.collection_schedule_id = ?";
            $params[] = $filters['collection_schedule_id'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(r.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(r.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql = "
            SELECT r.*, 
                   wt.name as waste_type_name, wt.icon as waste_type_icon,
                   u.name as staff_name, u.phone as staff_phone
            FROM waste_reports r
            LEFT JOIN waste_types wt ON wt.id = r.waste_type_id
            LEFT JOIN users u ON u.id = r.assigned_staff_id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY r.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        self::attachImagesBatch($results);

        return $results;
    }

    public static function countFiltered(array $filters = []): int {
        $db = Database::connect();
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(r.report_number LIKE ? OR r.reporter_name LIKE ? OR r.reporter_phone LIKE ? OR r.address LIKE ?)";
            $term = "%{$filters['search']}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['status'])) {
            $conditions[] = "r.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['waste_type_id'])) {
            $conditions[] = "r.waste_type_id = ?";
            $params[] = $filters['waste_type_id'];
        }

        if (!empty($filters['assigned_staff_id'])) {
            $conditions[] = "r.assigned_staff_id = ?";
            $params[] = $filters['assigned_staff_id'];
        }

        if (!empty($filters['collection_schedule_id'])) {
            $conditions[] = "r.collection_schedule_id = ?";
            $params[] = $filters['collection_schedule_id'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(r.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(r.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT COUNT(*) FROM waste_reports r WHERE " . implode(' AND ', $conditions);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getStaffJobs(int $staffId, ?string $statusFilter = null): array {
        $db = Database::connect();
        $sql = "
            SELECT r.*, wt.name as waste_type_name, wt.icon as waste_type_icon
            FROM waste_reports r
            LEFT JOIN waste_types wt ON wt.id = r.waste_type_id
            WHERE r.assigned_staff_id = ?
        ";
        $params = [$staffId];

        if ($statusFilter) {
            $sql .= " AND r.status = ?";
            $params[] = $statusFilter;
        } else {
            // Default active jobs first, completed last
            $sql .= " ORDER BY CASE 
                        WHEN r.status = 'กำลังดำเนินการ' THEN 1
                        WHEN r.status = 'กำลังเดินทาง' THEN 2
                        WHEN r.status = 'รับงานแล้ว' THEN 3
                        WHEN r.status = 'มอบหมายงานแล้ว' THEN 4
                        ELSE 5 END, r.created_at DESC";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll();

        self::attachImagesBatch($jobs);

        return $jobs;
    }

    /**
     * Batch-fetch images for multiple reports in a single query to eliminate N+1 problem
     */
    public static function attachImagesBatch(array &$reports): void {
        if (empty($reports)) return;
        $ids = array_filter(array_column($reports, 'id'));
        if (empty($ids)) return;

        $db = Database::connect();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT * FROM waste_report_images WHERE waste_report_id IN ({$placeholders}) ORDER BY id ASC");
        $stmt->execute(array_values($ids));
        $allImages = $stmt->fetchAll();

        $imagesByReport = [];
        foreach ($allImages as $img) {
            $imagesByReport[$img['waste_report_id']][] = $img;
        }

        foreach ($reports as &$r) {
            $r['images'] = $imagesByReport[$r['id']] ?? [];
        }
    }

    public static function getImages(int $reportId): array {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM waste_report_images WHERE waste_report_id = ? ORDER BY id ASC");
        $stmt->execute([$reportId]);
        return $stmt->fetchAll();
    }

    public static function getStatusHistory(int $reportId): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT h.*, u.name as changed_by_name, u.role as changed_by_role
            FROM status_histories h
            LEFT JOIN users u ON u.id = h.changed_by
            WHERE h.waste_report_id = ?
            ORDER BY h.created_at ASC
        ");
        $stmt->execute([$reportId]);
        return $stmt->fetchAll();
    }

    public static function getAssignments(int $reportId): array {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT a.*, 
                   by_u.name as assigned_by_name,
                   to_u.name as assigned_to_name, to_u.phone as assigned_to_phone
            FROM assignments a
            LEFT JOIN users by_u ON by_u.id = a.assigned_by
            LEFT JOIN users to_u ON to_u.id = a.assigned_to
            WHERE a.waste_report_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$reportId]);
        return $stmt->fetchAll();
    }

    public static function updateItemActualWeights(int $reportId, array $itemActualWeights): void {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE waste_report_items SET actual_weight = ?, updated_at = NOW() WHERE id = ? AND waste_report_id = ?");
        $totalActual = 0.0;
        $hasActual = false;

        foreach ($itemActualWeights as $itemId => $weight) {
            $val = ($weight !== '' && $weight !== null) ? (float)$weight : null;
            $stmt->execute([$val, (int)$itemId, $reportId]);
            if ($val !== null) {
                $totalActual += $val;
                $hasActual = true;
            }
        }

        if ($hasActual) {
            $stmtRep = $db->prepare("UPDATE waste_reports SET actual_weight = ?, updated_at = NOW() WHERE id = ?");
            $stmtRep->execute([$totalActual, $reportId]);
        }
    }


    public static function updateStatus(int $reportId, string $newStatus, ?int $userId = null, ?string $note = null): bool {
        $db = Database::connect();
        
        $current = self::findById($reportId);
        if (!$current) return false;

        $oldStatus = $current['status'];

        $sql = "UPDATE waste_reports SET status = ?, updated_at = NOW()";
        $params = [$newStatus];

        if ($newStatus === 'จัดเก็บเรียบร้อยแล้ว') {
            $sql .= ", completed_at = NOW()";
        }

        $sql .= " WHERE id = ?";
        $params[] = $reportId;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Record history
        $stmtHist = $db->prepare("
            INSERT INTO status_histories (waste_report_id, old_status, new_status, changed_by, note, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmtHist->execute([$reportId, $oldStatus, $newStatus, $userId, $note]);

        return true;
    }

    public static function assignStaff(int $reportId, int $assignedTo, int $assignedBy, ?string $note = null): bool {
        $db = Database::connect();
        
        $report = self::findById($reportId);
        if (!$report) return false;

        // Update report
        $stmt = $db->prepare("
            UPDATE waste_reports 
            SET assigned_staff_id = ?, status = 'มอบหมายงานแล้ว', updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$assignedTo, $reportId]);

        // Insert into assignments
        $stmtAssign = $db->prepare("
            INSERT INTO assignments (waste_report_id, assigned_by, assigned_to, assigned_at, note, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), ?, NOW(), NOW())
        ");
        $stmtAssign->execute([$reportId, $assignedBy, $assignedTo, $note]);

        // Record history
        $staffUser = User::findById($assignedTo);
        $staffName = $staffUser['name'] ?? "เจ้าหน้าที่ ID: {$assignedTo}";
        $histNote = "มอบหมายงานให้ {$staffName}" . ($note ? " (หมายเหตุ: {$note})" : "");

        $stmtHist = $db->prepare("
            INSERT INTO status_histories (waste_report_id, old_status, new_status, changed_by, note, created_at)
            VALUES (?, ?, 'มอบหมายงานแล้ว', ?, ?, NOW())
        ");
        $stmtHist->execute([$reportId, $report['status'], $assignedBy, $histNote]);

        return true;
    }

    public static function completeJob(int $reportId, float $actualWeight, ?string $note, ?int $staffId = null): bool {
        $db = Database::connect();
        
        $stmt = $db->prepare("
            UPDATE waste_reports 
            SET actual_weight = ?, status = 'จัดเก็บเรียบร้อยแล้ว', completed_at = NOW(), updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$actualWeight, $reportId]);

        // Record history
        $histNote = "จัดเก็บเรียบร้อยแล้ว น้ำหนักจริง {$actualWeight} กก." . ($note ? " (หมายเหตุ: {$note})" : "");
        $stmtHist = $db->prepare("
            INSERT INTO status_histories (waste_report_id, old_status, new_status, changed_by, note, created_at)
            VALUES (?, 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', ?, ?, NOW())
        ");
        $stmtHist->execute([$reportId, $staffId, $histNote]);

        return true;
    }

    public static function addImage(int $reportId, string $imagePath, string $imageType = 'before'): int {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO waste_report_images (waste_report_id, image_path, image_type, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$reportId, $imagePath, $imageType]);
        return (int)$db->lastInsertId();
    }

    public static function getDashboardMetrics(): array {
        $db = Database::connect();

        $metrics = [
            'total' => 0,
            'pending' => 0,
            'reviewing' => 0,
            'assigned' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'estimated_weight_total' => 0.0,
            'actual_weight_total' => 0.0
        ];

        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'รอรับเรื่อง' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'กำลังตรวจสอบ' THEN 1 END) as reviewing,
                COUNT(CASE WHEN status = 'มอบหมายงานแล้ว' THEN 1 END) as assigned,
                COUNT(CASE WHEN status IN ('รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ') THEN 1 END) as in_progress,
                COUNT(CASE WHEN status = 'จัดเก็บเรียบร้อยแล้ว' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'ยกเลิก' THEN 1 END) as cancelled,
                COALESCE(SUM(estimated_weight), 0) as estimated_weight_total,
                COALESCE(SUM(actual_weight), 0) as actual_weight_total
            FROM waste_reports
        ");
        $row = $stmt->fetch();
        if ($row) {
            $metrics = array_merge($metrics, $row);
        }

        return $metrics;
    }

    public static function getWasteTypeStats(): array {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT wt.name, wt.icon, COUNT(r.id) as count, COALESCE(SUM(r.actual_weight), SUM(r.estimated_weight), 0) as total_weight
            FROM waste_types wt
            LEFT JOIN waste_reports r ON r.waste_type_id = wt.id
            GROUP BY wt.id
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    public static function getMonthlyTrend(): array {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key,
                   DATE_FORMAT(created_at, '%M %Y') as month_name,
                   COUNT(*) as total_reports,
                   COUNT(CASE WHEN status = 'จัดเก็บเรียบร้อยแล้ว' THEN 1 END) as completed_reports
            FROM waste_reports
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month_key
            ORDER BY month_key ASC
        ");
        return $stmt->fetchAll();
    }
}
