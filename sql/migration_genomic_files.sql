-- Migração: Adicionar categoria aos arquivos de pacientes
-- para separar documentos genéticos dos demais
ALTER TABLE patient_files ADD COLUMN category VARCHAR(50) DEFAULT 'general' AFTER comment;

-- Índice para busca por categoria
CREATE INDEX idx_patient_files_category ON patient_files(patient_id, category);