-- =============================================
-- MEDIC - Migration v2
-- 1. Add visit_reason to medical_records
-- 2. Create exam_specialties junction table
-- =============================================

SET NAMES utf8mb4;

-- 1. Add visit_reason (motivo da consulta) to medical_records
ALTER TABLE `medical_records` ADD COLUMN `visit_reason` TEXT NULL AFTER `title`;

-- 2. Create exam_specialties junction table for multiple specialties per exam
CREATE TABLE IF NOT EXISTS `exam_specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT NOT NULL,
    `specialty_name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_exam_specialty` (`exam_id`, `specialty_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_exam_specialties_exam ON exam_specialties(exam_id);

-- 3. Migrate existing specialty data from exams to exam_specialties
INSERT IGNORE INTO `exam_specialties` (`exam_id`, `specialty_name`)
SELECT `id`, `specialty` FROM `exams` WHERE `specialty` IS NOT NULL AND `specialty` != '';