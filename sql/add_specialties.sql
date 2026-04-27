-- =============================================
-- MEDIC - Adicionar tabela de Especialidades Médicas
-- Execute este script no phpMyAdmin ou via CLI
-- =============================================

SET NAMES utf8mb4;

-- Tabela de Especialidades Médicas
CREATE TABLE IF NOT EXISTS `specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar coluna specialty na tabela exams (se não existir)
ALTER TABLE `exams` ADD COLUMN IF NOT EXISTS `specialty` VARCHAR(255) NULL AFTER `doctor_name`;

-- Índice para performance
CREATE INDEX IF NOT EXISTS idx_specialties_name ON specialties(name);

-- Migrar especialidades existentes de medical_records para a tabela
INSERT IGNORE INTO `specialties` (`name`)
SELECT DISTINCT `specialty` FROM `medical_records` 
WHERE `specialty` IS NOT NULL AND TRIM(`specialty`) != ''
ORDER BY `specialty`;

-- Migrar especialidades existentes de medications para a tabela
INSERT IGNORE INTO `specialties` (`name`)
SELECT DISTINCT `specialty` FROM `medications` 
WHERE `specialty` IS NOT NULL AND TRIM(`specialty`) != ''
ORDER BY `specialty`;

-- Inserir algumas especialidades comuns (serão ignoradas se já existirem)
INSERT IGNORE INTO `specialties` (`name`) VALUES
('Cardiologia'),
('Clínico Geral'),
('Dermatologia'),
('Endocrinologia'),
('Gastroenterologia'),
('Ginecologia'),
('Neurologia'),
('Oftalmologia'),
('Ortopedia'),
('Otorrinolaringologia'),
('Pediatria'),
('Pneumologia'),
('Psiquiatria'),
('Urologia');