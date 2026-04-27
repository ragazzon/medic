-- =============================================
-- MEDIC - Sistema de Controle Médico Familiar
-- Script de criação do banco de dados
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Pacientes
CREATE TABLE IF NOT EXISTS `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `birth_date` DATE NULL,
    `gender` ENUM('M', 'F', 'Outro') NULL,
    `cpf` VARCHAR(20) NULL,
    `blood_type` VARCHAR(5) NULL,
    `relationship` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NULL,
    `email` VARCHAR(255) NULL,
    `address` VARCHAR(500) NULL,
    `allergies` TEXT NULL,
    `chronic_conditions` TEXT NULL,
    `medications` TEXT NULL,
    `health_insurance` VARCHAR(255) NULL,
    `insurance_number` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `photo` VARCHAR(500) NULL,
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Prontuários
CREATE TABLE IF NOT EXISTS `medical_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `diagnosis` TEXT NULL,
    `symptoms` TEXT NULL,
    `prescription` TEXT NULL,
    `doctor_name` VARCHAR(255) NULL,
    `specialty` VARCHAR(255) NULL,
    `clinic_hospital` VARCHAR(255) NULL,
    `record_date` DATE NOT NULL,
    `category` VARCHAR(100) NOT NULL DEFAULT 'Consulta',
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Exames
CREATE TABLE IF NOT EXISTS `exams` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `exam_type` VARCHAR(100) NOT NULL DEFAULT 'Outro',
    `exam_date` DATE NOT NULL,
    `lab_clinic` VARCHAR(255) NULL,
    `doctor_name` VARCHAR(255) NULL,
    `results` TEXT NULL,
    `notes` TEXT NULL,
    `status` ENUM('Normal', 'Alterado', 'Aguardando', 'Indefinido') NOT NULL DEFAULT 'Indefinido',
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Arquivos de Exames (imagens e documentos)
CREATE TABLE IF NOT EXISTS `exam_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` INT NOT NULL DEFAULT 0,
    `is_image` TINYINT(1) NOT NULL DEFAULT 0,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Arquivos de Prontuários
CREATE TABLE IF NOT EXISTS `record_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `record_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` INT NOT NULL DEFAULT 0,
    `is_image` TINYINT(1) NOT NULL DEFAULT 0,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`record_id`) REFERENCES `medical_records`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de associação usuário <-> pacientes (controle de acesso)
CREATE TABLE IF NOT EXISTS `user_patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_user_patient` (`user_id`, `patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para performance
CREATE INDEX idx_patients_created_by ON patients(created_by);
CREATE INDEX idx_medical_records_patient ON medical_records(patient_id);
CREATE INDEX idx_medical_records_date ON medical_records(record_date);
CREATE INDEX idx_medical_records_category ON medical_records(category);
CREATE INDEX idx_exams_patient ON exams(patient_id);
CREATE INDEX idx_exams_date ON exams(exam_date);
CREATE INDEX idx_exams_type ON exams(exam_type);
CREATE INDEX idx_exams_status ON exams(status);
CREATE INDEX idx_exam_files_exam ON exam_files(exam_id);
CREATE INDEX idx_record_files_record ON record_files(record_id);
CREATE INDEX idx_user_patients_user ON user_patients(user_id);
CREATE INDEX idx_user_patients_patient ON user_patients(patient_id);

SET FOREIGN_KEY_CHECKS = 1;