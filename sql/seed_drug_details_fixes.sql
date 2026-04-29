-- =============================================
-- MEDIC - Correções e adições
-- 1. Classe Urologia
-- 2. Correção de classe de Tolterodina e Tansulosina
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- Adicionar classe Urologia
INSERT IGNORE INTO `pgx_drug_classes` (`code`, `name`, `description`, `icon`, `color`, `sort_order`) VALUES
('urologia', 'Urologia', 'Medicamentos para bexiga hiperativa, hiperplasia prostática, incontinência urinária e outros distúrbios urológicos.', 'bi-droplet-half', '#17a2b8', 14);

-- Corrigir classe da Tolterodina e Tansulosina e Mirabegrona
UPDATE `pgx_drug_details` SET class_id = (SELECT id FROM pgx_drug_classes WHERE code='urologia' LIMIT 1) WHERE drug_name IN ('Tolterodina', 'Tansulosina', 'Mirabegrona');
UPDATE `pgx_drug_genes` SET drug_class = 'Urologia' WHERE drug_name IN ('Tolterodina', 'Tansulosina', 'Mirabegrona');