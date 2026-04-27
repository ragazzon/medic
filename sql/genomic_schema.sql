-- =============================================
-- MEDIC GENOMIC MODULE - Schema
-- Tabelas para análise farmacogenômica e genômica
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Painéis de análise genômica
CREATE TABLE IF NOT EXISTS `pgx_panels` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'bi-dna',
    `color` VARCHAR(20) DEFAULT '#3182ce',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catálogo de genes
CREATE TABLE IF NOT EXISTS `pgx_genes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `symbol` VARCHAR(50) NOT NULL,
    `name` VARCHAR(500) NULL,
    `chromosome` VARCHAR(5) NULL,
    `description` TEXT NULL,
    `clinical_relevance` TEXT NULL,
    `panel_id` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pgx_genes_symbol` (`symbol`),
    INDEX `idx_pgx_genes_panel` (`panel_id`),
    FOREIGN KEY (`panel_id`) REFERENCES `pgx_panels`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Regras de interpretação genômica (CORE ENGINE)
CREATE TABLE IF NOT EXISTS `pgx_rules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT NULL,
    `gene_symbol` VARCHAR(50) NOT NULL,
    `rsid` VARCHAR(30) NOT NULL,
    `variant_name` VARCHAR(100) NULL COMMENT 'Ex: *2, C677T, Val158Met',
    `ref_genotype` VARCHAR(10) NULL COMMENT 'Genótipo referência (normal)',
    `risk_genotypes` VARCHAR(100) NULL COMMENT 'Genótipos de risco separados por vírgula',
    `het_genotypes` VARCHAR(100) NULL COMMENT 'Genótipos heterozigoto',
    `phenotype_normal` VARCHAR(255) NULL,
    `phenotype_het` VARCHAR(255) NULL,
    `phenotype_risk` VARCHAR(255) NULL,
    `clinical_significance` ENUM('high','moderate','low','informational') DEFAULT 'moderate',
    `evidence_level` ENUM('1A','1B','2A','2B','3','4') DEFAULT '3' COMMENT 'PharmGKB evidence',
    `description_technical` TEXT NULL COMMENT 'Para médicos',
    `description_practical` TEXT NULL COMMENT 'Para pacientes/familiares',
    `recommendations` TEXT NULL,
    `references_pmid` VARCHAR(500) NULL COMMENT 'PMIDs separados por vírgula',
    `source` VARCHAR(100) DEFAULT 'curated' COMMENT 'PharmGKB, ClinVar, GWAS, curated',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pgx_rules_rsid` (`rsid`),
    INDEX `idx_pgx_rules_gene` (`gene_symbol`),
    INDEX `idx_pgx_rules_panel` (`panel_id`),
    FOREIGN KEY (`panel_id`) REFERENCES `pgx_panels`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Interações medicamento <-> gene
CREATE TABLE IF NOT EXISTS `pgx_drug_genes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `drug_name` VARCHAR(255) NOT NULL,
    `drug_class` VARCHAR(255) NULL,
    `gene_symbol` VARCHAR(50) NOT NULL,
    `rsid` VARCHAR(30) NULL,
    `interaction_type` ENUM('substrate','inhibitor','inducer','target','transporter','risk') NOT NULL,
    `effect_description` TEXT NULL,
    `recommendation_normal` TEXT NULL,
    `recommendation_het` TEXT NULL,
    `recommendation_risk` TEXT NULL,
    `evidence_level` ENUM('1A','1B','2A','2B','3','4') DEFAULT '3',
    `source` VARCHAR(100) DEFAULT 'curated',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pgx_dg_drug` (`drug_name`),
    INDEX `idx_pgx_dg_gene` (`gene_symbol`),
    INDEX `idx_pgx_dg_rsid` (`rsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Genótipos do paciente (TODOS os SNPs do CSV)
CREATE TABLE IF NOT EXISTS `patient_genotypes` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `rsid` VARCHAR(30) NOT NULL,
    `chromosome` VARCHAR(5) NULL,
    `position` INT UNSIGNED NULL,
    `genotype` VARCHAR(10) NOT NULL,
    `imported_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_patient_rsid` (`patient_id`, `rsid`),
    INDEX `idx_pg_rsid` (`rsid`),
    INDEX `idx_pg_patient` (`patient_id`),
    INDEX `idx_pg_chr_pos` (`chromosome`, `position`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Importações genômicas (log)
CREATE TABLE IF NOT EXISTS `genomic_imports` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_format` VARCHAR(50) NULL COMMENT 'genera, 23andme, ancestry, vcf',
    `genome_build` VARCHAR(20) NULL COMMENT 'GRCh37, GRCh38',
    `total_snps` INT DEFAULT 0,
    `imported_snps` INT DEFAULT 0,
    `status` ENUM('pending','processing','completed','error') DEFAULT 'pending',
    `error_message` TEXT NULL,
    `imported_by` INT NULL,
    `imported_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`imported_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resultados de análise por paciente (cache)
CREATE TABLE IF NOT EXISTS `patient_pgx_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `rule_id` INT NOT NULL,
    `rsid` VARCHAR(30) NOT NULL,
    `patient_genotype` VARCHAR(10) NOT NULL,
    `status` ENUM('normal','attention','risk','info','unknown') NOT NULL,
    `phenotype` VARCHAR(255) NULL,
    `interpretation` TEXT NULL,
    `practical_note` TEXT NULL,
    `analyzed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_patient_rule` (`patient_id`, `rule_id`),
    INDEX `idx_ppr_patient` (`patient_id`),
    INDEX `idx_ppr_status` (`status`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rule_id`) REFERENCES `pgx_rules`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medicamentos do paciente com análise genômica
CREATE TABLE IF NOT EXISTS `patient_drug_analysis` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `medication_id` INT NULL,
    `drug_name` VARCHAR(255) NOT NULL,
    `overall_status` ENUM('safe','caution','warning','contraindicated','unknown') DEFAULT 'unknown',
    `summary` TEXT NULL,
    `details` JSON NULL COMMENT 'Detalhes por gene em JSON',
    `argue_with_doctor` TEXT NULL COMMENT 'Texto para discutir com médico',
    `analyzed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pda_patient` (`patient_id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;