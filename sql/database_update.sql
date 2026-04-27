-- =============================================
-- MEDIC - Atualização do banco de dados
-- Adicionar colunas faltantes e tabela de permissões
-- Execute este script no phpMyAdmin
-- =============================================

SET NAMES utf8mb4;

-- Adicionar colunas faltantes na tabela patients
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `cpf` VARCHAR(20) NULL AFTER `gender`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `relationship` VARCHAR(100) NULL AFTER `blood_type`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) NULL AFTER `relationship`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) NULL AFTER `phone`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `address` VARCHAR(500) NULL AFTER `email`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `medications` TEXT NULL AFTER `chronic_conditions`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `health_insurance` VARCHAR(255) NULL AFTER `medications`;
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `insurance_number` VARCHAR(100) NULL AFTER `health_insurance`;

-- Colunas adicionais para prontuários (medical_records)
ALTER TABLE `medical_records` ADD COLUMN IF NOT EXISTS `specialty` VARCHAR(255) NULL AFTER `doctor_name`;
ALTER TABLE `medical_records` ADD COLUMN IF NOT EXISTS `diagnosis` TEXT NULL AFTER `description`;
ALTER TABLE `medical_records` ADD COLUMN IF NOT EXISTS `symptoms` TEXT NULL AFTER `diagnosis`;
ALTER TABLE `medical_records` ADD COLUMN IF NOT EXISTS `prescription` TEXT NULL AFTER `symptoms`;

-- Adicionar original_name em record_files se não existir
ALTER TABLE `record_files` MODIFY COLUMN `original_name` VARCHAR(255) NULL;

-- Tabela de associação usuário <-> pacientes (para controle de acesso)
CREATE TABLE IF NOT EXISTS `user_patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_user_patient` (`user_id`, `patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS idx_user_patients_user ON user_patients(user_id);
CREATE INDEX IF NOT EXISTS idx_user_patients_patient ON user_patients(patient_id);

-- Tabela de Medicamentos
CREATE TABLE IF NOT EXISTS `medications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nome do medicamento',
    `active_ingredient` VARCHAR(255) NULL COMMENT 'Princípio ativo',
    `dosage` VARCHAR(100) NULL COMMENT 'Dosagem (ex: 500mg)',
    `frequency` VARCHAR(100) NULL COMMENT 'Frequência (ex: 8/8h)',
    `route` VARCHAR(50) NULL COMMENT 'Via (oral, intravenosa, tópica, etc.)',
    `start_date` DATE NULL COMMENT 'Data de início',
    `end_date` DATE NULL COMMENT 'Data de término (NULL = uso contínuo)',
    `prescriber` VARCHAR(255) NULL COMMENT 'Médico prescritor',
    `specialty` VARCHAR(100) NULL COMMENT 'Especialidade do prescritor',
    `reason` TEXT NULL COMMENT 'Indicação / motivo da prescrição',
    `instructions` TEXT NULL COMMENT 'Instruções especiais',
    `side_effects` TEXT NULL COMMENT 'Efeitos colaterais observados',
    `is_continuous` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = uso contínuo',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = em uso atual',
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS idx_medications_patient ON medications(patient_id);
CREATE INDEX IF NOT EXISTS idx_medications_active ON medications(is_active);
CREATE INDEX IF NOT EXISTS idx_medications_continuous ON medications(is_continuous);

-- Ampliar coluna file_type para suportar MIME types longos (ex: DOCX = 71 chars)
ALTER TABLE `exam_files` MODIFY COLUMN `file_type` VARCHAR(255) NULL;
ALTER TABLE `record_files` MODIFY COLUMN `file_type` VARCHAR(255) NULL;

-- Garantir que o primeiro usuário é admin
UPDATE `users` SET `role` = 'admin' WHERE `id` = 1;
