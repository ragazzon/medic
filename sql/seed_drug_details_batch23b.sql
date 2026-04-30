-- BATCH 23b: Últimos 2 medicamentos (231-232)
-- Ácido acetilsalicílico, Acenocumarol
-- COMPLETOS 232 MEDICAMENTOS!

SET NAMES utf8mb4;

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Ácido acetilsalicílico', 'Antitrombóticos/Anticoagulantes', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco hemólise com AAS em doses altas', 'Sem risco', 'Cautela em doses altas', 'Monitorar hemólise', '2B', 'PharmGKB', 1),
('Acenocumarol', 'Antitrombóticos/Anticoagulantes', 'VKORC1', 'rs9923231', 'target', 'VKORC1 TT - muito sensível (mesmo perfil da varfarina)', 'Dose padrão', 'Dose reduzida 25-50%', 'Dose muito reduzida', '1A', 'CPIC', 1),
('Acenocumarol', 'Antitrombóticos/Anticoagulantes', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza acenocumarol', 'Dose padrão', 'Metabolismo reduzido', 'Dose reduzida', '1A', 'CPIC', 1);