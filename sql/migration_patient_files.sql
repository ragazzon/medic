-- =============================================
-- MEDIC - Migration: Patient Generic Files
-- Adds a table for generic/extra files per patient
-- =============================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `patient_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(100) DEFAULT NULL,
    `file_size` INT DEFAULT 0,
    `is_image` TINYINT(1) DEFAULT 0,
    `comment` TEXT NULL,
    `uploaded_by` INT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_patient_files_patient ON patient_files(patient_id);