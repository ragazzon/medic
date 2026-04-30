-- =====================================================
-- BATCH 23: Medicamentos 221-230 (FINAIS!)
-- Amoxapina, Amitriptilina, Amissulprida, Amiodarona,
-- Alprazolam, Alopurinol, Alfentanila, Agomelatina,
-- Ácido valproico, Ácido nalidíxico
-- =====================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Amoxapina', 'Antidepressivos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza amoxapina', 'Dose padrão', 'Metabolismo reduzido', 'Usar alternativa', '1A', 'CPIC', 1),
('Amitriptilina', 'Antidepressivos Tricíclicos', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17 aumenta conversão em nortriptilina', 'Dose padrão', 'Conversão aumentada', 'Considerar dose menor', '1A', 'CPIC', 1),
('Amitriptilina', 'Antidepressivos Tricíclicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza nortriptilina (metabólito ativo)', 'Dose padrão', 'Metabolismo reduzido', 'Usar alternativa', '1A', 'CPIC', 1),
('Amissulprida', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R afeta risco de ganho de peso com antipsicóticos', 'Risco padrão', 'Risco moderado', 'Risco aumentado', '2B', 'PharmGKB', 1),
('Amissulprida', 'Antipsicóticos', 'HTR2C', 'rs3813929', 'risk', 'HTR2C afeta risco de ganho de peso com antipsicóticos', 'Risco padrão', 'Risco moderado', 'Risco aumentado', '2B', 'PharmGKB', 1),
('Amiodarona', 'Antiarrítmicos', 'NOS1AP', 'rs12143842', 'risk', 'NOS1AP afeta intervalo QTc - risco de arritmia', 'Risco padrão QTc', 'Risco moderado QTc prolongado', 'Alto risco QTc', '2A', 'PharmGKB', 1),
('Alprazolam', 'Ansiolíticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza alprazolam', 'Dose padrão', 'Metabolismo reduzido', 'Meia-vida prolongada', '2A', 'PharmGKB', 1),
('Alopurinol', 'Antigotosos', 'ABCG2', 'rs2231142', 'transporter', 'ABCG2 Q141K reduz excreção de urato', 'Dose padrão', 'Resposta variável', 'Pode necessitar dose maior', '2A', 'PharmGKB', 1),
('Alfentanila', 'Analgésicos Opioides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza alfentanila', 'Dose padrão', 'Metabolismo reduzido', 'Duração prolongada', '2A', 'PharmGKB', 1),
('Alfentanila', 'Analgésicos Opioides', 'OPRM1', 'rs1799971', 'target', 'OPRM1 A118G afeta resposta a opioides', 'Resposta normal', 'Resposta reduzida', 'Dose maior necessária', '2B', 'PharmGKB', 1),
('Agomelatina', 'Antidepressivos', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2*1F ultra-rápido reduz níveis de agomelatina', 'Dose padrão', 'Níveis reduzidos', 'Eficácia possivelmente reduzida', '2A', 'PharmGKB', 1),
('Ácido valproico', 'Anticonvulsivantes', 'ANKK1', 'rs1800497', 'target', 'ANKK1/DRD2 modula resposta ao valproato', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Ácido nalidíxico', 'Antibióticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise', 'Sem risco', 'Cautela', 'Monitorar hemólise', '2B', 'PharmGKB', 1);