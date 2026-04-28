-- v4.1: Add lock/manual columns for CRUD protection
ALTER TABLE pgx_rules ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) DEFAULT 0 COMMENT 'Locked rules cannot be overwritten by re-import';
ALTER TABLE pgx_rules ADD COLUMN IF NOT EXISTS is_manual TINYINT(1) DEFAULT 0 COMMENT 'Manually created/edited rules';
ALTER TABLE pgx_rules ADD COLUMN IF NOT EXISTS notes TEXT NULL COMMENT 'Clinical notes added manually';
ALTER TABLE pgx_rules ADD COLUMN IF NOT EXISTS updated_by INT NULL;
ALTER TABLE pgx_rules ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;

ALTER TABLE pgx_drug_genes ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) DEFAULT 0;
ALTER TABLE pgx_drug_genes ADD COLUMN IF NOT EXISTS is_manual TINYINT(1) DEFAULT 0;
ALTER TABLE pgx_drug_genes ADD COLUMN IF NOT EXISTS notes TEXT NULL;

ALTER TABLE patient_pgx_results ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) DEFAULT 0 COMMENT 'Locked results are not overwritten by re-analysis';
ALTER TABLE patient_pgx_results ADD COLUMN IF NOT EXISTS manual_override VARCHAR(20) NULL COMMENT 'Manual status override';
ALTER TABLE patient_pgx_results ADD COLUMN IF NOT EXISTS clinical_notes TEXT NULL COMMENT 'Doctor/family notes';
ALTER TABLE patient_pgx_results ADD COLUMN IF NOT EXISTS updated_by INT NULL;
ALTER TABLE patient_pgx_results ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
