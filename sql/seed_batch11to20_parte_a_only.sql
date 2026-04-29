-- =====================================================
-- BATCHES 11-20: SOMENTE PARTE A (pgx_drug_genes)
-- Faz os medicamentos 111-200 APARECEREM no dashboard
-- A PARTE B (textos detalhados) dos batches 11-20 tinha
-- problemas de truncamento. Este arquivo substitui a PARTE A.
-- Rode ESTE arquivo ao invés dos batch11-batch20 individuais.
-- =====================================================

SET NAMES utf8mb4;

-- BATCH 11: Maraviroque → Lidocaína/Prilocaína
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Maraviroque', 'Antivirais (HIV)', 'CYP3A5', 'rs776746', 'substrate', 'CYP3A5 metaboliza maraviroque', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2B', 'PharmGKB', 1),
('Mafenida', 'Antibióticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Lurasidona', 'Antipsicóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza lurasidona', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Lovastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta lovastatina', 'Dose padrão', 'Dose máxima limitada', 'Risco de miopatia', '1A', 'CPIC', 1),
('Losartana', 'Antagonistas de Angiotensina II', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 converte losartana em metabólito ativo', 'Dose padrão', 'Conversão intermediária', 'Conversão reduzida', '2A', 'PharmGKB', 1),
('Lorazepam', 'Ansiolíticos', 'UGT2B15', 'rs1902023', 'substrate', 'UGT2B15 metaboliza lorazepam - NÃO depende CYP', 'Dose padrão', 'Metabolismo reduzido', 'Meia-vida prolongada', '2B', 'PharmGKB', 1),
('Lítio', 'Estabilizadores de Humor', 'GSK3B', 'rs6438552', 'target', 'GSK3B modula resposta ao lítio', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Lisdexanfetamina', 'Psicoestimulantes', 'DRD1', 'rs4532', 'target', 'DRD1 modula resposta a estimulantes', 'Normal', 'Resposta variável', 'Resposta alterada', '3', 'PharmGKB', 1),
('Lidocaína-Tetracaína', 'Anestésicos Locais', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de metemoglobinemia', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Lidocaína-Prilocaína', 'Anestésicos Locais', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - prilocaína causa metemoglobinemia', 'Sem risco', 'Cautela', 'Monitorar', '2B', 'PharmGKB', 1);

-- BATCH 12: Levodopa → Hidroxicloroquina
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Levodopa', 'Antiparkinsonianos', 'COMT', 'rs4680', 'target', 'COMT degrada levodopa - Val/Met afeta duração', 'Duração normal', 'Duração intermediária', 'Duração curta', '2A', 'PharmGKB', 1),
('Levodopa-Carbidopa-Entacapona', 'Antiparkinsonianos', 'COMT', 'rs4680', 'target', 'COMT inibida pela entacapona', 'Benefício padrão', 'Benefício intermediário', 'Maior benefício', '2A', 'PharmGKB', 1),
('Lansoprazol', 'IBP', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17 metaboliza lansoprazol mais rápido', 'Dose padrão', 'Eficácia reduzida', 'Eficácia muito reduzida', '1A', 'CPIC', 1),
('Lamotrigina', 'Anticonvulsivantes', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 risco de SJS/TEN', 'Sem risco', 'Avaliar', 'Contraindicado', '1A', 'CPIC', 1),
('Irbesartana', 'Antagonistas de Angiotensina II', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza irbesartana', 'Dose padrão', 'Metabolismo reduzido', 'Níveis aumentados', '2A', 'PharmGKB', 1),
('Imipramina', 'Antidepressivos Tricíclicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza imipramina', 'Dose padrão', 'Dose reduzida', 'Usar alternativa', '1A', 'CPIC', 1),
('Imatinibe', 'Antineoplásicos', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 ultra-rápido pode reduzir níveis', 'Dose padrão', 'Monitorar níveis', 'Dose maior possível', '2B', 'PharmGKB', 1),
('Iloperidona', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza iloperidona', 'Dose padrão', 'Titular cuidadosamente', 'Dose reduzida', '1A', 'CPIC', 1),
('Ibuprofeno', 'AINEs', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza ibuprofeno', 'Dose padrão', 'Meia-vida prolongada', 'Risco GI aumentado', '2A', 'PharmGKB', 1),
('Hidroxicloroquina', 'Antiparasitários', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar', '2B', 'PharmGKB', 1);

-- BATCH 13: Hidrocodona → Furosemida
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Hidrocodona', 'Analgésicos Opioides', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 ativa hidrocodona parcialmente', 'Normal', 'Ativação parcial', 'Ativação reduzida', '1A', 'CPIC', 1),
('Hidroclorotiazida', 'Diuréticos', 'NEDD4L', 'rs4149601', 'target', 'NEDD4L afeta resposta a tiazídicos', 'Resposta padrão', 'Resposta variável', 'Resposta reduzida', '3', 'PharmGKB', 1),
('Haloperidol', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza haloperidol', 'Dose padrão', 'Metabolismo reduzido', 'Risco EPS', '1A', 'CPIC', 1),
('Guanfacina', 'Não-estimulantes TDAH', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza guanfacina', 'Dose padrão', 'Metabolismo reduzido', 'Níveis aumentados', '2A', 'PharmGKB', 1),
('Glimepirida', 'Antidiabéticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Gliclazida', 'Antidiabéticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Glibenclamida', 'Antidiabéticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Gefitinibe', 'Antineoplásicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza gefitinibe', 'Dose padrão', 'Monitorar', 'Monitorar', '2B', 'PharmGKB', 1),
('Galantamina', 'Inibidores da Colinesterase', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza galantamina', 'Dose padrão', 'Metabolismo reduzido', 'Dose reduzida', '2A', 'PharmGKB', 1),
('Furosemida', 'Diuréticos', 'GNB3', 'rs5443', 'target', 'GNB3 afeta resposta a diuréticos de alça', 'Resposta padrão', 'Resposta variável', 'Resposta reduzida', '3', 'PharmGKB', 1);

-- BATCH 14: Fosfenitoína → Fenofibrato
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Fosfenitoína', 'Anticonvulsivantes', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza fenitoína (forma ativa)', 'Dose padrão', 'Níveis aumentados', 'Risco toxicidade', '1A', 'CPIC', 1),
('Fluvoxamina', 'Antidepressivos ISRS', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza fluvoxamina parcialmente', 'Dose padrão', 'Monitorar', 'Dose reduzida', '2A', 'PharmGKB', 1),
('Flutamida', 'Antineoplásicos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Flurbiprofeno', 'AINEs', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza flurbiprofeno', 'Dose padrão', 'Meia-vida prolongada', 'Risco GI', '2A', 'PharmGKB', 1),
('Fluoxetina', 'Antidepressivos ISRS', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza fluoxetina', 'Dose padrão', 'Dose reduzida', 'Usar alternativa', '1A', 'CPIC', 1),
('Fluoruracila', 'Antineoplásicos', 'DPYD', 'rs3918290', 'substrate', 'DPYD metaboliza 5-FU - deficiência é FATAL', 'Dose padrão', 'Dose reduzida 50%', 'CONTRAINDICADO', '1A', 'CPIC', 1),
('Flufenazina', 'Antipsicóticos', 'CY