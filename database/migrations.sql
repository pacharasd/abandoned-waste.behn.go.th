-- Database Migration: abandoned_waste
-- Comprehensive relational schema for Homeless Waste Collection Request System

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `status_histories`;
DROP TABLE IF EXISTS `assignments`;
DROP TABLE IF EXISTS `waste_report_images`;
DROP TABLE IF EXISTS `waste_report_items`;
DROP TABLE IF EXISTS `waste_reports`;
DROP TABLE IF EXISTS `collection_schedules`;
DROP TABLE IF EXISTS `waste_types`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users table (Admin, Staff)
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(50) NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Waste Types table
CREATE TABLE `waste_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `image` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.1 Collection Schedules table (Monthly Collection Cycles)
CREATE TABLE `collection_schedules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `collection_date` DATE NOT NULL,
    `start_time` TIME NOT NULL DEFAULT '09:00:00',
    `end_time` TIME NOT NULL DEFAULT '16:00:00',
    `area_zone` VARCHAR(255) NOT NULL DEFAULT 'ครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี',
    `cutoff_date` DATETIME NULL,
    `description` TEXT NULL,
    `status` ENUM('upcoming', 'active', 'collecting', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_collection_schedules_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_schedule_date` (`collection_date`),
    INDEX `idx_schedule_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Waste Reports table (Core)
CREATE TABLE `waste_reports` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `report_number` VARCHAR(50) NOT NULL UNIQUE,
    `reporter_name` VARCHAR(255) NOT NULL,
    `reporter_phone` VARCHAR(50) NOT NULL,
    `address` TEXT NOT NULL,
    `latitude` DECIMAL(10, 7) NOT NULL,
    `longitude` DECIMAL(10, 7) NOT NULL,
    `waste_type_id` BIGINT UNSIGNED NOT NULL,
    `collection_schedule_id` BIGINT UNSIGNED NULL,
    `estimated_weight` DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
    `actual_weight` DECIMAL(8, 2) NULL,
    `description` TEXT NULL,
    `status` ENUM('รอรับเรื่อง', 'กำลังตรวจสอบ', 'มอบหมายงานแล้ว', 'รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', 'ยกเลิก') NOT NULL DEFAULT 'รอรับเรื่อง',
    `assigned_staff_id` BIGINT UNSIGNED NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_waste_reports_waste_type` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_waste_reports_schedule` FOREIGN KEY (`collection_schedule_id`) REFERENCES `collection_schedules` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_waste_reports_staff` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_report_status` (`status`),
    INDEX `idx_reporter_phone` (`reporter_phone`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_report_schedule` (`collection_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Waste Report Items table (Multiple Waste Types & Weights per Report)
CREATE TABLE `waste_report_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `waste_report_id` BIGINT UNSIGNED NOT NULL,
    `waste_type_id` BIGINT UNSIGNED NOT NULL,
    `estimated_weight` DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
    `actual_weight` DECIMAL(8, 2) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_waste_items_report` FOREIGN KEY (`waste_report_id`) REFERENCES `waste_reports` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_waste_items_type` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`) ON DELETE RESTRICT,
    INDEX `idx_report_item_report_id` (`waste_report_id`),
    INDEX `idx_report_item_type_id` (`waste_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Waste Report Images table (Before / After photos)
CREATE TABLE `waste_report_images` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `waste_report_id` BIGINT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `image_type` ENUM('before', 'after') NOT NULL DEFAULT 'before',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_waste_images_report` FOREIGN KEY (`waste_report_id`) REFERENCES `waste_reports` (`id`) ON DELETE CASCADE,
    INDEX `idx_report_image_type` (`waste_report_id`, `image_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5. Assignments table
CREATE TABLE `assignments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `waste_report_id` BIGINT UNSIGNED NOT NULL,
    `assigned_by` BIGINT UNSIGNED NOT NULL,
    `assigned_to` BIGINT UNSIGNED NOT NULL,
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_assignments_report` FOREIGN KEY (`waste_report_id`) REFERENCES `waste_reports` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_assignments_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_assignments_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Status Histories table (Chronological State Tracking)
CREATE TABLE `status_histories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `waste_report_id` BIGINT UNSIGNED NOT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_status_histories_report` FOREIGN KEY (`waste_report_id`) REFERENCES `waste_reports` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_status_histories_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_history_report` (`waste_report_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Notifications table (Internal In-App Alerts for Admins & Staff)
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `related_type` VARCHAR(100) NULL,
    `related_id` BIGINT UNSIGNED NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_user_unread` (`user_id`, `is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Activity Logs table
CREATE TABLE `activity_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_activity_user` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
