-- BATCHES 11-20: PARTE A LIMPA
-- Rode este arquivo para medicamentos 111-200 no dashboard

SET NAMES utf8mb4;

-- BATCH 11
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Maraviroque', 'Antivirais (HIV)', 'CYP3A5', 'rs776746', 'substrate', 'CYP3A5 metaboliza maraviroque - expressores eliminam mais rápido', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2B', 'PharmGKB', 1),
('Mafenida', 'Antibióticos (Sulfonamidas tópicas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise com sulfonamidas', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Lurasidona', 'Antipsicóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza lurasidona', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Lovastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta lovastatina - risco de miopatia', 'Dose padrão', 'Dose máxima limitada', 'Risco de miopatia', '1A', 'CPIC', 1),
('Losartana', 'Antagonistas de Angiotensina II (BRAs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 converte losartana em metabólito ativo (E-3174)', 'Dose padrão (boa conversão)', 'Conversão intermediária', 'Conversão reduzida - eficácia pode ser menor', '2A', 'PharmGKB', 1),
('Losartana', 'Antagonistas de Angiotensina II (BRAs)', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 é via secundária', 'Normal', 'Metabolismo reduzido', 'Monitorar', '3', 'PharmGKB', 1),
('Lorazepam', 'Ansiolíticos (Benzodiazepínicos)', 'UGT2B15', 'rs1902023', 'substrate', 'UGT2B15 metaboliza lorazepam por glucuronidação - NÃO depende CYP', 'Dose padrão', 'Metabolismo reduzido', 'Meia-vida prolongada', '2B', 'PharmGKB', 1),
('Lítio', 'Estabilizadores de Humor', 'GSK3B', 'rs6438552', 'target', 'GSK3B modula resposta ao lítio', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Lisdexanfetamina', 'Psicoestimulantes (TDAH)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza parcialmente anfetamina', 'Dose padrão', 'Monitorar', 'Monitorar', '3', 'PharmGKB', 1),
('Lisdexanfetamina', 'Psicoestimulantes (TDAH)', 'DRD1', 'rs4532', 'target', 'DRD1 modula resposta a estimulantes', 'Normal', 'Função executiva variável', 'Função executiva alterada', '3', 'PharmGKB', 1),
('Lidocaína-Tetracaína', 'Anestésicos Locais', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco teórico de metemoglobinemia', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Lidocaína-Prilocaína', 'Anestésicos Locais', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - prilocaína é o principal causador de metemoglobinemia', 'Sem risco', 'Cautela', 'Monitorar metemoglobina', '2B', 'PharmGKB', 1);

-- BATCH 12
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Levodopa', 'Antiparkinsonianos', 'COMT', 'rs4680', 'target', 'COMT degrada dopamina - afeta duração da levodopa', 'Duração padrão', 'Duração intermediária da levodopa', 'Duração maior (COMT baixa = mais dopamina)', '2A', 'PharmGKB', 1),
('Levodopa-Carbidopa-Entacapona', 'Antiparkinsonianos', 'COMT', 'rs4680', 'target', 'COMT é inibida pela entacapona - genótipo modula resposta', 'Resposta padrão', 'Resposta intermediária', 'Melhor resposta à entacapona', '2B', 'PharmGKB', 1),
('Lansoprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza lansoprazol - *17 reduz eficácia', 'Dose padrão', 'Eficácia reduzida', 'Eficácia muito reduzida', '1A', 'CPIC', 1),
('Lamotrigina', 'Anticonvulsivantes/Estabilizadores', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 associado a SJS - risco em asiáticos', 'Sem risco (europeus)', 'Baixo risco', 'Tipagem HLA em asiáticos', '1A', 'CPIC', 1),
('Irbesartana', 'Antagonistas de Angiotensina II (BRAs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza irbesartana', 'Dose padrão', 'Metabolismo reduzido - níveis maiores', 'Níveis aumentados', '2A', 'PharmGKB', 1),
('Imipramina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza imipramina em desipramina', 'Dose padrão', 'Monitorar níveis', 'Reduzir dose 50%', '1A', 'CPIC', 1),
('Imipramina', 'Antidepressivos (Tricíclicos)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via secundária - *17 aumenta metabolismo', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '1A', 'CPIC', 1),
('Imatinibe', 'Antineoplásicos (Inibidores de Tirosina Quinase)', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza parcialmente imatinibe', 'Dose padrão', 'Metabolismo aumentado', 'Monitorar resposta', '2B', 'PharmGKB', 1),
('Iloperidona', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza iloperidona', 'Dose padrão', 'Monitorar', 'Reduzir dose 50%', '1A', 'FDA', 1),
('Ibuprofeno', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza ibuprofeno', 'Dose padrão', 'Meia-vida prolongada', 'Risco de sangramento GI', '2A', 'DPWG', 1),
('Hidroxicloroquina', 'Antiparasitários/Imunossupressores', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar hemograma', '2A', 'CPIC', 1);

-- BATCH 13
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Hidrocodona', 'Analgésicos Opioides', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 converte hidrocodona em hidromorfona - pró-droga parcial', 'Dose padrão', 'Analgesia possivelmente reduzida', 'Eficácia reduzida em metabolizadores nulos', '2A', 'CPIC', 1),
('Hidroclorotiazida', 'Diuréticos', 'NEDD4L', 'rs4149601', 'target', 'NEDD4L modula resposta pressórica a tiazídicos', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Hidroclorotiazida', 'Diuréticos', 'ADD1', 'rs4961', 'target', 'ADD1 G460W modula sensibilidade ao sódio e resposta a tiazídicos', 'Resposta padrão', 'Sensibilidade ao sódio intermediária', 'Alta sensibilidade ao sódio', '2B', 'PharmGKB', 1),
('Haloperidol', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza haloperidol', 'Dose padrão', 'Efeito aumentado', 'Risco de EPS - reduzir dose', '1A', 'DPWG', 1),
('Haloperidol', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula ganho de peso', 'Sem risco aumentado', 'Risco moderado', 'Risco alto', '2B', 'PharmGKB', 1),
('Guanfacina', 'Não estimulantes SNC (TDAH)', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza guanfacina', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Glimepirida', 'Antidiabéticos (Sulfonilureias)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - sulfonilureias podem causar hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Gliclazida', 'Antidiabéticos (Sulfonilureias)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Glibenclamida', 'Antidiabéticos (Sulfonilureias)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Gefitinibe', 'Antineoplásicos (Inibidores TK)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza parcialmente gefitinibe', 'Dose padrão', 'Monitorar', 'Monitorar', '3', 'PharmGKB', 1),
('Galantamina', 'Inibidores da Colinesterase', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza galantamina', 'Dose padrão', 'Monitorar efeitos colinérgicos', 'Dose menor', '2B', 'PharmGKB', 1),
('Furosemida', 'Diuréticos de Alça', 'GNB3', 'rs5443', 'target', 'GNB3 C825T modula resposta a diuréticos', 'Resposta padrão', 'Resposta variável', 'Melhor resposta', '3', 'PharmGKB', 1);

-- BATCH 14
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Fosfenitoína', 'Anticonvulsivantes', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza fenitoína (pró-droga) - lentos acumulam', 'Dose padrão', 'Metabolismo reduzido - dose menor', 'Risco de toxicidade - reduzir dose significativamente', '1A', 'CPIC', 1),
('Fosfenitoína', 'Anticonvulsivantes', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 associado a SJS/TEN - risco em asiáticos', 'Sem risco (europeus)', 'Baixo risco', 'Tipagem HLA em asiáticos', '1A', 'CPIC', 1),
('Fluvoxamina', 'Antidepressivos (ISRS)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza fluvoxamina parcialmente', 'Dose padrão', 'Monitorar', 'Monitorar', '2B', 'PharmGKB', 1),
('Flutamida', 'Antineoplásicos (Antiandrogênicos)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - flutamida pode causar hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Flurbiprofeno', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza flurbiprofeno', 'Dose padrão', 'Meia-vida prolongada', 'Risco de sangramento', '2A', 'DPWG', 1),
('Fluoxetina', 'Antidepressivos (ISRS)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza fluoxetina + é INIBIDOR POTENTE do CYP2D6', 'Dose padrão', 'Monitorar', 'Considerar alternativa', '1A', 'CPIC', 1),
('Fluoxetina', 'Antidepressivos (ISRS)', 'FKBP5', 'rs1360780', 'target', 'FKBP5 modula resposta a antidepressivos', 'Resposta favorável', 'Eixo HPA desregulado', 'Resposta reduzida', '2B', 'PharmGKB', 1),
('Fluoruracila', 'Antineoplásicos', 'DPYD', 'rs3918290', 'substrate', 'DPD metaboliza 5-FU - deficiência causa toxicidade FATAL', 'Dose padrão', 'Reduzir dose 25-50%', 'CONTRAINDICADO', '1A', 'CPIC', 1),
('Flufenazina', 'Antipsicóticos', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza flufenazina - ultra-rápidos eliminam mais rápido', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2B', 'PharmGKB', 1),
('Flecainida', 'Antiarrítmicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza flecainida - lentos têm mais efeito/toxicidade', 'Dose padrão', 'Monitorar ECG', 'Reduzir dose - risco pró-arrítmico', '1A', 'DPWG', 1),
('Fentanil', 'Analgésicos Opioides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza fentanil', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Fentanil', 'Analgésicos Opioides', 'OPRM1', 'rs2952768', 'target', 'rs2952768 modula dose de opioide necessária', 'Dose padrão', 'Pode necessitar dose maior', 'Dose maior', '3', 'GWAS', 1),
('Fenofibrato', 'Fibratos (Dislipidemia)', 'APOA5', 'rs964184', 'target', 'APOA5 modula resposta de triglicerídeos a fibratos', 'Resposta padrão', 'Pode ter melhor resposta (TG altos)', 'Melhor resposta se TG basalmente elevados', '2B', 'PharmGKB', 1);

-- BATCH 15
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Fenobarbital', 'Anticonvulsivantes', 'ABCB1', 'rs1045642', 'transporter', 'ABCB1 modula níveis de fenobarbital no SNC', 'Normal', 'Níveis variáveis', 'Níveis potencialmente maiores no SNC', '3', 'PharmGKB', 1),
('Fenitoína', 'Anticonvulsivantes', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza fenitoína - CPIC guideline', 'Dose padrão', 'Reduzir dose 25%', 'Reduzir dose 50%', '1A', 'CPIC', 1),
('Fenitoína', 'Anticonvulsivantes', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 - SJS em asiáticos', 'Sem risco (europeus)', 'Baixo risco', 'Tipagem HLA', '1A', 'CPIC', 1),
('Femprocumona', 'Anticoagulantes', 'VKORC1', 'rs9923231', 'target', 'VKORC1 modula sensibilidade a cumarínicos', 'Dose padrão', 'Sensível - dose menor', 'Muito sensível - dose muito menor', '1A', 'CPIC', 1),
('Femprocumona', 'Anticoagulantes', 'CYP4F2', 'rs2108622', 'substrate', 'CYP4F2 afeta metabolismo vitamina K', 'Normal', 'Metabolismo VitK reduzido', 'Muito reduzido', '2A', 'PharmGKB', 1),
('Extrato de Cannabis', 'Canabinoides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza canabinoides', 'Normal', 'Metabolismo reduzido', 'Acúmulo', '3', 'PharmGKB', 1),
('Exemestano', 'Antineoplásicos (Inibidores de Aromatase)', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza exemestano', 'Dose padrão', 'Metabolismo reduzido', 'Monitorar', '2B', 'PharmGKB', 1),
('Eszopiclona', 'Hipnóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza eszopiclona', 'Dose padrão', 'Metabolismo reduzido', 'Sedação prolongada', '2A', 'PharmGKB', 1),
('Esomeprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza esomeprazol - moderadamente dependente', 'Dose padrão', 'Eficácia moderadamente reduzida', 'Considerar rabeprazol', '2A', 'CPIC', 1),
('Escitalopram', 'Antidepressivos (ISRS)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17 aumenta metabolismo do escitalopram', 'Dose padrão', 'Níveis 30-40% menores', 'Níveis muito reduzidos - alternativa', '1A', 'CPIC', 1),
('Escitalopram', 'Antidepressivos (ISRS)', 'HTR2A', 'rs6311', 'target', 'HTR2A modula resposta a ISRS', 'Normal', 'Resposta variável', 'Resposta variável', '2B', 'PharmGKB', 1),
('Escitalopram', 'Antidepressivos (ISRS)', 'GRIK4', 'rs1954787', 'target', 'GRIK4 associado a resposta a ISRS (STAR*D)', 'Resposta padrão', 'Possivelmente melhor resposta', 'Possivelmente melhor resposta', '3', 'PharmGKB', 1),
('Escitalopram', 'Antidepressivos (ISRS)', 'COMT', 'rs4680', 'target', 'COMT modula neurotransmissão dopaminérgica', 'Normal', 'Intermediário', 'Variável', '3', 'PharmGKB', 1),
('Eritromicina-Sulfisoxazol', 'Antibióticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - sulfisoxazol pode causar hemólise', 'Sem risco', 'Cautela', 'Monitorar', '2A', 'CPIC', 1),
('Erdafitinibe', 'Antineoplásicos', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza erdafitinibe', 'Dose padrão', 'Monitorar', 'Monitorar', '2B', 'PharmGKB', 1);

-- BATCH 16
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Entacapona', 'Inibidores da COMT', 'COMT', 'rs4680', 'target', 'COMT é o alvo da entacapona - genótipo modula benefício', 'Benefício padrão', 'Benefício intermediário', 'Maior benefício (COMT alta inibida)', '2B', 'PharmGKB', 1),
('Eltrombopague', 'Agonistas de Trombopoietina', 'F5', 'rs6025', 'risk', 'Fator V Leiden aumenta risco trombótico com estimuladores de plaquetas', 'Sem risco adicional de trombose', 'Risco trombótico aumentado', 'Alto risco', '2B', 'PharmGKB', 1),
('Eliglustate', 'Agentes para Doença de Gaucher', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza eliglustate - FDA limita em lentos', 'Dose padrão', 'Monitorar', 'Dose máxima 84mg 1x/dia (FDA)', '1A', 'FDA', 1),
('Efavirenz', 'Antivirais (HIV)', 'CYP2B6', 'rs3211371', 'substrate', 'CYP2B6 metaboliza efavirenz - lentos têm mais neurotoxicidade', 'Dose 600mg', 'Monitorar neurotoxicidade', 'Considerar dose 400mg', '1A', 'CPIC', 1),
('Duloxetina', 'Antidepressivos (IRSN)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza duloxetina parcialmente', 'Dose padrão', 'Monitorar', 'Monitorar efeitos', '2A', 'PharmGKB', 1),
('Dronabinol', 'Canabinoides/Antieméticos', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza dronabinol (THC)', 'Dose padrão', 'Efeitos prolongados', 'Efeitos muito prolongados', '2B', 'PharmGKB', 1),
('Doxorrubicina', 'Antineoplásicos (Antraciclinas)', 'RARG', 'rs2229774', 'risk', 'RARG S427L associado a CARDIOTOXICIDADE com antraciclinas', 'Risco padrão', 'RISCO AUMENTADO de cardiotoxicidade', 'Risco muito aumentado', '2A', 'CPGG', 1),
('Doxorrubicina', 'Antineoplásicos (Antraciclinas)', 'SLC28A3', 'rs7853758', 'risk', 'SLC28A3 pode ter efeito PROTETOR contra cardiotoxicidade', 'Sem proteção', 'Proteção parcial', 'Proteção', '2B', 'PharmGKB', 1),
('Doxepina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza doxepina - CPIC guideline', 'Dose padrão', 'Monitorar', 'Reduzir dose ou alternativa', '1A', 'CPIC', 1),
('Doxepina', 'Antidepressivos (Tricíclicos)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via secundária - *17 aumenta metabolismo', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2A', 'CPIC', 1);

-- BATCH 17
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Donepezila', 'Inibidores da Colinesterase', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza donepezila', 'Dose padrão', 'Monitorar efeitos colinérgicos', 'Dose menor', '2B', 'PharmGKB', 1),
('Divalproato de Sódio', 'Anticonvulsivantes/Estabilizadores', 'ANKK1', 'rs1800497', 'target', 'ANKK1/DRD2 TaqIA modula resposta', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Diltiazem', 'Bloqueadores de Canais de Cálcio', 'PLCD3', 'rs12946454', 'target', 'PLCD3 modula resposta pressórica', 'Resposta padrão', 'Resposta variável', 'Resposta variável', '3', 'PharmGKB', 1),
('Digoxina', 'Glicosídeos Cardíacos', 'ABCB1', 'rs1045642', 'transporter', 'ABCB1 modula níveis de digoxina', 'Níveis padrão', 'Níveis variáveis', 'Níveis potencialmente maiores', '2B', 'PharmGKB', 1),
('Diclofenaco', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza diclofenaco', 'Dose padrão', 'Meia-vida prolongada', 'Risco de sangramento/nefrotoxicidade', '2A', 'DPWG', 1),
('Diazepam', 'Ansiolíticos (Benzodiazepínicos)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza diazepam - *17 elimina mais rápido', 'Dose padrão', 'Duração pode ser menor', 'Duração reduzida', '2A', 'PharmGKB', 1),
('Diazepam', 'Ansiolíticos (Benzodiazepínicos)', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 é via secundária', 'Normal', 'Metabolismo reduzido', 'Monitorar', '3', 'PharmGKB', 1),
('Dextroanfetamina', 'Psicoestimulantes (TDAH)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza parcialmente anfetamina', 'Dose padrão', 'Monitorar', 'Monitorar', '3', 'PharmGKB', 1),
('Dextroanfetamina', 'Psicoestimulantes (TDAH)', 'DRD1', 'rs4532', 'target', 'DRD1 modula resposta a estimulantes', 'Normal', 'Função executiva variável', 'Função executiva alterada', '3', 'PharmGKB', 1),
('Dexmetilfenidato', 'Psicoestimulantes (TDAH)', 'ADRA2A', 'rs1800544', 'target', 'ADRA2A modula resposta', 'Resposta padrão', 'Resposta variável', 'Duração alterada', '2B', 'PharmGKB', 1),
('Dexlansoprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza dexlansoprazol', 'Dose padrão', 'Eficácia reduzida', 'Considerar rabeprazol', '2A', 'CPIC', 1),
('Deutetrabenazina', 'Inibidores do Transportador de Monoamina', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza deutetrabenazina - FDA limita dose em lentos', 'Dose padrão', 'Monitorar', 'Dose máxima limitada (FDA)', '1A', 'FDA', 1);

-- BATCH 18
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Desvenlafaxina', 'Antidepressivos (IRSN)', 'FKBP5', 'rs1360780', 'target', 'FKBP5 modula resposta a antidepressivos', 'Resposta favorável', 'Eixo HPA desregulado', 'Resposta reduzida', '2B', 'PharmGKB', 1),
('Desipramina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza desipramina - CPIC guideline', 'Dose padrão', 'Monitorar níveis', 'Reduzir dose 50%', '1A', 'CPIC', 1),
('Desipramina', 'Antidepressivos (Tricíclicos)', 'BDNF', 'rs6265', 'target', 'BDNF modula resposta', 'Normal', 'Secreção reduzida', 'Resposta reduzida', '2B', 'PharmGKB', 1),
('Daunorrubicina', 'Antineoplásicos (Antraciclinas)', 'RARG', 'rs2229774', 'risk', 'RARG S427L - cardiotoxicidade com antraciclinas', 'Risco padrão', 'RISCO AUMENTADO', 'Risco muito aumentado', '2A', 'CPGG', 1),
('Daunorrubicina', 'Antineoplásicos (Antraciclinas)', 'SLC28A3', 'rs7853758', 'risk', 'SLC28A3 efeito protetor', 'Sem proteção', 'Proteção parcial', 'Proteção', '2B', 'PharmGKB', 1),
('Darifenacina', 'Antimuscarínicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza darifenacina', 'Dose padrão', 'Monitorar anticolinérgicos', 'Dose menor', '2A', 'PharmGKB', 1),
('Dapsona', 'Antibióticos (Sulfonas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - dapsona causa hemólise', 'Sem risco', 'Cautela', 'CONTRAINDICADO se deficiente grave', '1A', 'CPIC', 1),
('Dabrafenibe', 'Antineoplásicos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - dabrafenibe pode causar hemólise', 'Sem risco', 'Teste G6PD', 'Monitorar', '2A', 'FDA', 1),
('Dabigatrana', 'Anticoagulantes (DOACs)', 'CES1', 'rs2244613', 'substrate', 'CES1 ativa dabigatrana (pró-droga)', 'Ativação normal', 'Ativação variável', 'Ativação reduzida', '3', 'PharmGKB', 1),
('Codeína', 'Analgésicos Opioides', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 converte codeína em morfina - ESSENCIAL. Sem CYP2D6 = INEFICAZ', 'Dose padrão (morfina produzida)', 'Analgesia reduzida', 'Codeína INEFICAZ - usar alternativa', '1A', 'CPIC', 1),
('Clozapina', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 é via secundária da clozapina', 'Dose padrão', 'Monitorar', 'Monitorar', '2B', 'PharmGKB', 1),
('Clozapina', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula ganho de peso', 'Sem risco aumentado', 'Risco moderado', 'Risco alto', '2B', 'PharmGKB', 1),
('Clozapina', 'Antipsicóticos', 'HTR2C', 'rs3813929', 'risk', 'HTR2C modula ganho de peso', 'Risco padrão', 'Proteção parcial', 'Proteção', '2B', 'PharmGKB', 1),
('Clorpromazina', 'Antipsicóticos', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza clorpromazina - ultra-rápidos eliminam mais rápido', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2B', 'PharmGKB', 1);

-- BATCH 19
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Cloroquina', 'Antiparasitários (Antimaláricos)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - cloroquina pode causar hemólise', 'Sem risco', 'Cautela', 'Monitorar', '2A', 'CPIC', 1),
('Clopidogrel', 'Anticoagulantes (Antiplaquetários)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 converte clopidogrel em metabólito ativo - *17 AUMENTA eficácia', 'Dose padrão', 'Eficácia AUMENTADA (boa notícia)', 'Eficácia muito aumentada', '1A', 'CPIC', 1),
('Clopidogrel', 'Anticoagulantes (Antiplaquetários)', 'CES1', 'rs2244613', 'substrate', 'CES1 modula ativação do clopidogrel', 'Normal', 'Ativação variável', 'Ativação reduzida', '3', 'PharmGKB', 1),
('Clonazepam', 'Ansiolíticos (Benzodiazepínicos)', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza clonazepam', 'Dose padrão', 'Metabolismo reduzido', 'Acúmulo possível', '2B', 'PharmGKB', 1),
('Clomipramina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza clomipramina', 'Dose padrão', 'Monitorar', 'Reduzir dose', '1A', 'CPIC', 1),
('Clomipramina', 'Antidepressivos (Tricíclicos)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via secundária - *17 aumenta metabolismo', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2A', 'CPIC', 1),
('Clobazam', 'Ansiolíticos/Anticonvulsivantes', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza clobazam em N-desmetilclobazam - *17 elimina mais rápido', 'Dose padrão', 'Duração pode ser menor', 'Duração reduzida', '1A', 'CPIC', 1),
('Citalopram', 'Antidepressivos (ISRS)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17 aumenta metabolismo do citalopram', 'Dose padrão', 'Níveis 30-40% menores', 'Considerar alternativa', '1A', 'CPIC', 1),
('Citalopram', 'Antidepressivos (ISRS)', 'HTR2A', 'rs6311', 'target', 'HTR2A modula resposta', 'Normal', 'Variável', 'Variável', '2B', 'PharmGKB', 1),
('Cisplatina', 'Antineoplásicos', 'TPMT', 'rs1800460', 'risk', 'TPMT modula risco de ototoxicidade com cisplatina', 'Risco padrão', 'Risco aumentado de ototoxicidade', 'Risco alto', '2A', 'CPIC', 1),
('Ciprofloxacina', 'Antibióticos (Fluoroquinolonas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Ciclosporina', 'Imunossupressores', 'CYP3A5', 'rs776746', 'substrate', 'CYP3A5 metaboliza ciclosporina - expressores necessitam dose maior', 'Dose padrão (não-expressor)', 'Dose maior necessária (expressor parcial)', 'Dose significativamente maior', '2A', 'PharmGKB', 1),
('Cetamina', 'Anestésicos/Antidepressivos', 'CYP2B6', 'rs3211371', 'substrate', 'CYP2B6 metaboliza cetamina em norketamina', 'Dose padrão', 'Metabolismo variável', 'Monitorar', '3', 'PharmGKB', 1);

-- BATCH 20
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Celecoxibe', 'Anti-inflamatórios (AINEs/COX-2)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza celecoxibe', 'Dose padrão', 'Metabolismo reduzido', 'Risco de efeitos adversos', '2A', 'DPWG', 1),
('Ceftriaxona', 'Antibióticos (Cefalosporinas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco teórico de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Carvedilol', 'Betabloqueadores', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza carvedilol', 'Dose padrão', 'Efeito beta-bloqueador aumentado', 'Hipotensão/bradicardia', '2A', 'PharmGKB', 1),
('Carisoprodol', 'Relaxantes Musculares', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza carisoprodol em meprobamato', 'Dose padrão', 'Metabolismo aumentado - efeito menor', 'Efeito reduzido', '2B', 'PharmGKB', 1),
('Carbamazepina', 'Anticonvulsivantes/Estabilizadores', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 - SJS/TEN em asiáticos', 'Sem risco (europeus)', 'Baixo risco', 'Tipagem HLA em asiáticos', '1A', 'CPIC', 1),
('Carbamazepina', 'Anticonvulsivantes/Estabilizadores', 'EPHX1', 'rs1051740', 'substrate', 'EPHX1 metaboliza epóxido da carbamazepina', 'Normal', 'Metabolismo reduzido do epóxido', 'Epóxido acumula - neurotoxicidade', '2B', 'PharmGKB', 1),
('Captopril', 'Inibidores da ECA', 'ACE', 'rs4343', 'target', 'ACE modula resposta a iECAs', 'Resposta padrão', 'Resposta intermediária', 'Níveis elevados - boa resposta', '2B', 'PharmGKB', 1),
('Capecitabina', 'Antineoplásicos (Fluoropirimidinas)', 'DPYD', 'rs3918290', 'substrate', 'DPD metaboliza capecitabina - deficiência FATAL', 'Dose padrão', 'Reduzir dose 25-50%', 'CONTRAINDICADO', '1A', 'CPIC', 1),
('Canabidiol', 'Canabinoides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza canabidiol', 'Dose padrão', 'Metabolismo reduzido', 'Monitorar', '2B', 'PharmGKB', 1),
('Canabidiol', 'Canabinoides', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via secundária do CBD', 'Normal', 'Metabolismo aumentado', 'Menos impacto', '3', 'PharmGKB', 1),
('Buspirona', 'Ansiolíticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza buspirona', 'Dose padrão', 'Metabolismo reduzido', 'Níveis aumentados', '2A', 'PharmGKB', 1),
('Bupropiona', 'Antidepressivos', 'CYP2B6', 'rs3211371', 'substrate', 'CYP2B6 metaboliza bupropiona em hidroxibupropiona', 'Dose padrão', 'Metabolismo variável', 'Monitorar', '2A', 'PharmGKB', 1);

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
UPDATE `pgx_drug_genes` SET drug_class = 'Urologia' WHERE drug_name IN ('Tolterodina', 'Tansulosina', 'Mirabegrona');-- =====================================================
-- BATCH 21: Medicamentos 201-210
-- Buprenorfina, Bupivacaína, Bumetanida, Bromocriptina,
-- Brivaracetam, Brexpiprazol, Benazepril, Azitromicina,
-- Azatioprina, Atorvastatina
-- =====================================================

-- =====================================================
-- PARTE A: pgx_drug_genes (faz aparecer no dashboard)
-- =====================================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`)
VALUES
('Buprenorfina', '', 'OPRD1', 'rs678849', 'substrate', 'Receptor delta-opioide: variante associada a resposta variável', 'Monitorar resposta analgésica. COMT AG pode exigir dose maior.', 'Monitorar resposta analgésica. COMT AG pode exigir dose maior.', 'Monitorar resposta analgésica. COMT AG pode exigir dose maior.', '2B', 'curated', 1),
('Bupivacaína', '', 'G6PD', 'rs1050829', 'substrate', 'Deficiência de G6PD pode causar metemoglobinemia', 'Eric: G6PD TT (normal). Uso seguro.', 'Eric: G6PD TT (normal). Uso seguro.', 'Eric: G6PD TT (normal). Uso seguro.', '1A', 'curated', 1),
('Bumetanida', '', 'GNB3', 'rs5443', 'substrate', 'Subunidade beta3 da proteína G: afeta resposta a diuréticos', 'Eric: GNB3 CC (normal). Resposta padrão esperada.', 'Eric: GNB3 CC (normal). Resposta padrão esperada.', 'Eric: GNB3 CC (normal). Resposta padrão esperada.', '3', 'curated', 1),
('Bromocriptina', '', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4*22: atividade reduzida', 'Eric: CYP3A4 GG (normal). Metabolismo padrão.', 'Eric: CYP3A4 GG (normal). Metabolismo padrão.', 'Eric: CYP3A4 GG (normal). Metabolismo padrão.', '2A', 'curated', 1),
('Brivaracetam', '', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17: metabolizador rápido', 'Eric: CYP2C19 *1/*17 (CT). Metabolismo mais rápido - possível duração menor.', 'Eric: CYP2C19 *1/*17 (CT). Metabolismo mais rápido - possível duração menor.', 'Eric: CYP2C19 *1/*17 (CT). Metabolismo mais rápido - possível duração menor.', '2B', 'curated', 1),
('Brexpiprazol', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6*4: metabolizador nulo', 'Eric: CYP2D6 N/D. Sem tipagem - FDA recomenda ajuste conforme fenótipo.', 'Eric: CYP2D6 N/D. Sem tipagem - FDA recomenda ajuste conforme fenótipo.', 'Eric: CYP2D6 N/D. Sem tipagem - FDA recomenda ajuste conforme fenótipo.', '1A', 'curated', 1),
('Benazepril', '', 'AGT', 'rs699', 'substrate', 'Angiotensinogênio M235T: afeta resposta a iECAs', 'Eric: AGT AG (heterozigoto). Resposta intermediária a iECAs.', 'Eric: AGT AG (heterozigoto). Resposta intermediária a iECAs.', 'Eric: AGT AG (heterozigoto). Resposta intermediária a iECAs.', '2B', 'curated', 1),
('Azitromicina', '', 'ABCB1', 'rs1045642', 'substrate', 'P-glicoproteína: afeta distribuição tecidual', 'Eric: ABCB1 N/D. Impacto clínico limitado para azitromicina.', 'Eric: ABCB1 N/D. Impacto clínico limitado para azitromicina.', 'Eric: ABCB1 N/D. Impacto clínico limitado para azitromicina.', '3', 'curated', 1),
('Azatioprina', '', 'TPMT', 'rs1800460', 'substrate', 'TPMT*3B: atividade reduzida - risco mielossupressão', 'Eric: TPMT CC (normal). Dose padrão segura.', 'Eric: TPMT CC (normal). Dose padrão segura.', 'Eric: TPMT CC (normal). Dose padrão segura.', '1A', 'curated', 1),
('Atorvastatina', '', 'SLCO1B1', 'rs4149056', 'substrate', 'SLCO1B1*5: transporte hepático reduzido - risco miopatia', 'Eric: SLCO1B1 TC (heterozigoto). Risco moderado miopatia. Preferir rosuvastatina.', 'Eric: SLCO1B1 TC (heterozigoto). Risco moderado miopatia. Preferir rosuvastatina.', 'Eric: SLCO1B1 TC (heterozigoto). Risco moderado miopatia. Preferir rosuvastatina.', '1A', 'curated', 1);


-- =====================================================
-- PARTE B: pgx_drug_details (textos detalhados)
-- =====================================================

-- 1. BUPRENORFINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Buprenorfina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Analgésicos/Anestésicos'),
  'A buprenorfina é um opioide semissintético utilizado para o tratamento da dor crônica moderada a intensa e no manejo da dependência de opioides. Atua como agonista parcial do receptor mu-opioide e antagonista do receptor kappa. Possui efeito teto para depressão respiratória, tornando-a mais segura que opioides plenos. Efeitos adversos incluem náuseas, constipação, sonolência, cefaleia e sudorese. Nomes comerciais: Restiva®, Subutex®, Norpatch®, Transtec®.',
  'O gene OPRD1, localizado no cromossomo 1, codifica o receptor delta-opioide, que modula a percepção da dor e a resposta emocional. A variante rs678849 pode influenciar a resposta à buprenorfina. Além disso, o gene COMT (rs4680) afeta o metabolismo das catecolaminas e a percepção de dor. Eric possui COMT AG (Val/Met) = atividade intermediária da COMT, e rs2952768 TC = variante associada a necessidade de doses maiores de opioides.',
  'rs678849', '1', 'OPRD1', 'C,T',
  'Europeia',
  'Eric possui genótipo CT no rs678849 (OPRD1) = resposta intermediária ao sistema opioide delta. Combinado com COMT AG e rs2952768 TC, o perfil sugere necessidade de monitoramento cuidadoso da resposta analgésica.',
  'A buprenorfina como agonista PARCIAL do receptor mu tem efeito teto para depressão respiratória, tornando-a mais segura. OPRD1 CT + COMT AG sugerem resposta intermediária. Para dor crônica, buprenorfina transdérmica (Norpatch) pode ser opção. Vantagem: NÃO depende do CYP2D6 (desconhecido no Eric) para ativação. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Restiva, Subutex, Norpatch, Transtec, Buprenor',
  '2B',
  'OPRD1 rs678849: evidência moderada (2B). COMT rs4680: evidência forte (1A) para percepção de dor. Buprenorfina NÃO depende do CYP2D6.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 2. BUPIVACAÍNA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Bupivacaína',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Analgésicos/Anestésicos'),
  'A bupivacaína é um anestésico local do tipo amida, de longa duração, amplamente utilizado em anestesia regional, peridural, raquidiana e bloqueios nervosos periféricos. É uma das drogas mais utilizadas em cirurgias maxilofaciais. Proporciona analgesia de 4-8 horas. Efeitos adversos incluem hipotensão, bradicardia e, em doses excessivas, cardiotoxicidade. Nomes comerciais: Marcaina®, Tradinol®, Bupstesic®, Neocaína®.',
  'O gene G6PD, localizado no cromossomo X, codifica a enzima glicose-6-fosfato desidrogenase, essencial para proteção dos glóbulos vermelhos contra estresse oxidativo. Deficiência de G6PD pode causar metemoglobinemia com anestésicos locais. Eric possui G6PD rs1050829 TT = atividade enzimática NORMAL.',
  'rs1050829', 'X', 'G6PD', 'T,T',
  'Europeia',
  'Eric possui genótipo TT no G6PD (normal). Não há risco aumentado de metemoglobinemia. Uso SEGURO.',
  'Bupivacaína é SEGURA para Eric (G6PD normal). EXCELENTE opção para anestesia regional na CIRURGIA MAXILAR: bloqueio do nervo alveolar inferior com analgesia de longa duração (4-8h). Pode ser combinada com adrenalina para prolongar efeito. Junto com ropivacaína e mepivacaína, faz parte das opções seguras para Eric. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Marcaina, Tradinol, Bupstesic, Neocaína, Sensorcaine',
  '1A',
  'G6PD e anestésicos locais: evidência forte (1A). Eric TT = normal = uso seguro. Relevante para cirurgia maxilar.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 3. BUMETANIDA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Bumetanida',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'A bumetanida é um diurético de alça potente, 40 vezes mais potente que a furosemida em base mg por mg. Utilizada para edema associado a insuficiência cardíaca, cirrose e doença renal. Efeitos adversos incluem hipocalemia, hiponatremia, hipotensão, ototoxicidade e hiperuricemia. Nomes comerciais: Burinax®.',
  'O gene GNB3, localizado no cromossomo 12, codifica a subunidade beta-3 da proteína G, envolvida na transdução de sinal. A variante rs5443 (C825T) afeta resposta a diuréticos. Eric possui GNB3 CC (genótipo de referência) = resposta padrão a diuréticos.',
  'rs5443', '12', 'GNB3', 'C,C',
  'Europeia',
  'Eric possui genótipo CC no GNB3 (normal). Resposta padrão esperada à bumetanida.',
  'Com GNB3 CC, Eric deve ter resposta padrão à bumetanida. Monitorar eletrólitos (potássio, sódio) como em qualquer paciente. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Burinax',
  '3',
  'GNB3 rs5443 e diuréticos: evidência baixa (3). Associação de GWAS, mecanismo não totalmente elucidado.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 4. BROMOCRIPTINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Bromocriptina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'A bromocriptina é um agonista dopaminérgico derivado do ergot, utilizada no tratamento de hiperprolactinemia, acromegalia, doença de Parkinson e supressão da lactação. Atua estimulando receptores D2 de dopamina. Efeitos adversos incluem náuseas, hipotensão ortostática, cefaleia, tontura e raramente fibrose retroperitoneal. Nomes comerciais: Parlodel®, Bagren®.',
  'O gene CYP3A4, localizado no cromossomo 7, metaboliza a bromocriptina. A variante CYP3A4*22 (rs35599367) está associada a atividade enzimática reduzida. Eric possui CYP3A4 GG (genótipo de referência) = atividade NORMAL.',
  'rs35599367', '7', 'CYP3A4', 'G,G',
  'Europeia',
  'Eric possui genótipo GG no CYP3A4*22 (atividade normal). Metabolismo padrão da bromocriptina.',
  'Com CYP3A4 normal, bromocriptina será metabolizada na velocidade padrão. Iniciar com dose baixa e titular gradualmente (efeitos adversos comuns: náusea, hipotensão). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Parlodel, Bagren',
  '2A',
  'CYP3A4*22 e bromocriptina: evidência moderada (2A). CYP3A4 é a principal via metabólica. Eric GG = normal.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 5. BRIVARACETAM
INSERT INTO pgx_drug_-- =====================================================
-- BATCH 21B: Medicamentos 205-210 (continuação)
-- Brivaracetam, Brexpiprazol, Benazepril, Azitromicina,
-- Azatioprina, Atorvastatina
-- =====================================================

-- 5. BRIVARACETAM
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Brivaracetam',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O brivaracetam é um anticonvulsivante de nova geração, derivado do levetiracetam, utilizado como terapia adjuvante em crises epilépticas parciais. Atua ligando-se seletivamente à proteína SV2A nas vesículas sinápticas. Efeitos adversos incluem sonolência, tontura, fadiga e irritabilidade. Nomes comerciais: Briviact®.',
  'O gene CYP2C19, localizado no cromossomo 10, participa do metabolismo do brivaracetam (via secundária - a hidrólise por amidases é a via principal). A variante CYP2C19*17 (rs12248560) confere atividade enzimática aumentada. Eric possui CYP2C19 CT (*1/*17) = metabolismo mais RÁPIDO.',
  'rs12248560', '10', 'CYP2C19', 'C,T',
  'Europeia',
  'Eric possui CYP2C19 *1/*17 (CT) = metabolizador rápido. Para brivaracetam o impacto é LIMITADO pois a via principal é hidrólise (amidases), não CYP2C19.',
  'O impacto do CYP2C19*17 no brivaracetam é LIMITADO porque a via principal é hidrólise. Em doses altas, CYP2C19 contribui mais. Se crises não controladas, metabolismo rápido pode contribuir para níveis subótimos. Monitorar resposta clínica. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Briviact',
  '2B',
  'CYP2C19 e brivaracetam: evidência moderada (2B). CYP2C19 é via SECUNDÁRIA. Impacto clínico limitado mas possível em doses altas.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 6. BREXPIPRAZOL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Brexpiprazol',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O brexpiprazol é um antipsicótico atípico de segunda geração, agonista parcial D2/5-HT1A e antagonista 5-HT2A. Utilizado em esquizofrenia e como adjuvante em depressão resistente. Menor risco de acatisia que aripiprazol. Efeitos adversos: ganho de peso, acatisia, sonolência, nasofaringite. Nomes comerciais: Rexulti®.',
  'O gene CYP2D6, localizado no cromossomo 22, é responsável pelo metabolismo principal do brexpiprazol. Metabolizadores lentos apresentam níveis 47% maiores. A FDA recomenda redução de dose para metabolizadores lentos. Eric NÃO possui tipagem do CYP2D6 (rs3892097 não disponível no chip Genera GSA v3.0).',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric NÃO possui tipagem do CYP2D6. Sem esta informação, não é possível determinar se é metabolizador normal, intermediário, lento ou ultra-rápido.',
  'SEM TIPAGEM DO CYP2D6: iniciar em dose baixa (0.5mg) e titular lentamente. FDA recomenda metade da dose para metabolizadores lentos. MC4R TT e HTR2C CC favorecem menor risco de ganho de peso. Para autismo, brexpiprazol pode ser alternativa à risperidona com perfil de efeitos adversos potencialmente melhor. RECOMENDA-SE TESTE CYP2D6 ANTES. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Rexulti',
  '1A',
  'CYP2D6 e brexpiprazol: evidência forte (1A). FDA label inclui recomendação de ajuste. Eric: CYP2D6 NÃO DISPONÍVEL.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 7. BENAZEPRIL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Benazepril',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'O benazepril é um inibidor da enzima conversora de angiotensina (iECA), utilizado no tratamento da hipertensão e insuficiência cardíaca. É uma pró-droga convertida em benazeprilato (forma ativa) no fígado. Efeitos adversos: tosse seca, hipercalemia, angioedema (raro), tontura e cefaleia. Nomes comerciais: Lotensin®, Bhena®.',
  'O gene AGT, localizado no cromossomo 1, codifica o angiotensinogênio, substrato do sistema renina-angiotensina-aldosterona. A variante rs699 (M235T) está associada a níveis elevados de angiotensinogênio e maior resposta a iECAs. Eric possui AGT AG (heterozigoto Met/Thr).',
  'rs699', '1', 'AGT', 'A,G',
  'Europeia',
  'Eric possui AGT AG (heterozigoto M235T). Níveis intermediários de angiotensinogênio. Resposta intermediária a iECAs.',
  'Com AGT AG e ACE rs4343 GG (níveis elevados de ECA), o perfil geral sugere que iECAs podem ter boa eficácia para Eric. Se anti-hipertensivo necessário, iECAs são opção farmacogeneticamente razoável. Monitorar potássio e função renal. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Lotensin, Bhena',
  '2B',
  'AGT rs699 e iECAs: evidência moderada (2B). Meta-análises mostram associação, mas não determinante isolado.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 8. AZITROMICINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Azitromicina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Infectologia'),
  'A azitromicina é um antibiótico macrolídeo de amplo espectro, usado em infecções respiratórias, otites, sinusites, infecções de pele e DSTs. Meia-vida longa permite esquemas curtos (3-5 dias). Efeitos adversos: diarreia, náuseas, dor abdominal e raramente prolongamento QT. Nomes comerciais: Zitromax®, Astro®, Azi®, Azimed®, Zirk®.',
  'O gene ABCB1 (MDR1), localizado no cromossomo 7, codifica a P-glicoproteína (bomba de efluxo). Para azitromicina, o impacto é LIMITADO pois ela se acumula em macrófagos por mecanismo independente da P-gp. Eric NÃO possui tipagem do ABCB1 (rs1045642 não disponível no chip).',
  'rs1045642', '7', 'ABCB1', 'N/D',
  'Europeia',
  'ABCB1 N/D no chip. Para azitromicina, o impacto é clinicamente IRRELEVANTE.',
  'Azitromicina é SEGURA para Eric independentemente do ABCB1: acumula-se em macrófagos por mecanismo não-P-gp, impacto farmacogenético marginal, G6PD normal. Usar normalmente conforme indicação. Atenção ao prolongamento QT se combinada com outros medicamentos QT-prolongadores. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Zitromax, Astro, Azi, Azimed, Zirk, Zitroneo',
  '3',
  'ABCB1 e azitromicina: evidência baixa (3). Impacto clinicamente marginal.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 9. AZATIOPRINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Azatioprina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Imunossupressores/Transplante'),
  'A azatioprina é um imunossupressor antimetabólito, pró-droga da 6-mercaptopurina, utilizada em doenças autoimunes (artrite reumatoide, lúpus, Crohn, hepatite autoimune), prevenção de rejeição de transplantes e protocolos poupadores de corticoides. Efeitos adversos graves: mielossupressão (leucopenia, trombocitopenia), hepatotoxicidade, pancreatite, risco aumentado de infecções e neoplasias. Nomes comerciais: Imuran®, Imunen®, Imussuprex®.',
  'O gene TPMT, localizado no cromossomo 6, codifica a tiopurina S-metiltransferase, responsável pela inativação das tiopurinas. Variantes que reduzem a atividade do TPMT causam acúmulo de metabólitos tóxicos (6-TGN), levando a mielossupressão potencialmente FATAL. As variantes principais são *2 (rs1800462), *3A (rs1800460+rs1142345) e *3C (rs1142345). Eric: TPMT rs1800460 CC (normal) e rs1800462 CC (normal). rs1142345 N/D no chip.',
  'rs1800460', '6', 'TPMT', 'C,C',
  'Europeia',
  'Eric possui TPMT rs1800460 CC e rs1800462 CC = ambos NORMAIS. rs1142345 (*3C) não disponível no chip, mas a prevalência de *3C isolado é baixa em europeus (~0.2%).',
  'TPMT parcialmente genotipado: *3B (rs1800460) e *2 (rs1800462) NORMAIS. O TPMT*3C (rs1142345) não está no chip, mas é raro em europeus isolado. Com alta probabilidade (>99%), Eric tem TPMT funcional. Dose padrão de azatioprina deve ser segura. RECOMENDAÇÃO: Se azatioprina for prescrita, solicitar dosagem da atividade enzimática do TPMT (teste fenotípico) antes de iniciar, como confirmação. Monitorar hemograma na 1ª semana. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Imuran, Imunen, Imussuprex',
  '1A',
  'TPMT e tiopurinas: evidência MUITO forte (1A). Guideline CPIC obrigatório. Eric: 2 de 3 variantes testadas = normais. *3C não testado mas prevalência <0.2% em europeus.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_-- =====================================================
-- BATCH 21C: Medicamento 210 - Atorvastatina (final)
-- =====================================================

-- 10. ATORVASTATINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Atorvastatina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'A atorvastatina é uma estatina (inibidor da HMG-CoA redutase) utilizada para redução do colesterol LDL e prevenção cardiovascular. É a estatina mais prescrita mundialmente. Efeitos adversos incluem mialgia, elevação de CPK, rabdomiólise (raro), hepatotoxicidade e diabetes. Nomes comerciais: Citalor®, Atorless®, Atorvasterol®, Corastorva®, Kolevas®, Lipitor®.',
  'O gene SLCO1B1, localizado no cromossomo 12, codifica o transportador OATP1B1, responsável pela captação hepática das estatinas. A variante rs4149056 (SLCO1B1*5, Val174Ala) reduz a captação hepática, aumentando os níveis sistêmicos e o risco de miopatia. Eric possui SLCO1B1 TC (heterozigoto) = transporte REDUZIDO. Além disso, APOA5 rs964184 GC = risco moderado de triglicerídeos elevados.',
  'rs4149056', '12', 'SLCO1B1', 'T,C',
  'Europeia',
  'Eric possui SLCO1B1 TC (heterozigoto *1/*5). Risco de miopatia 2-3x aumentado com atorvastatina em doses altas (>40mg). APOA5 GC sugere possível benefício de estatinas se colesterol elevado.',
  'SLCO1B1 TC (heterozigoto): A atorvastatina é MENOS afetada pelo SLCO1B1 que a sinvastatina, mas em doses altas (>40mg) o risco de miopatia existe. RECOMENDAÇÕES: (1) Se estatina necessária, atorvastatina em dose baixa-moderada (10-20mg) OU rosuvastatina (menor impacto SLCO1B1) são preferíveis à sinvastatina. (2) APOA5 GC indica possível predisposição a triglicerídeos elevados - monitorar perfil lipídico. (3) Evitar sinvastatina >20mg (risco 4.5x miopatia). (4) Monitorar CPK e sintomas musculares. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Citalor, Atorless, Atorvasterol, Corastorva, Kolevas, Lipitor',
  '1A',
  'SLCO1B1 e estatinas: evidência FORTE (1A). Guideline CPIC. Atorvastatina menos afetada que sinvastatina mas risco existe em doses altas. APOA5 rs964184: evidência 2B para triglicerídeos.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);-- =====================================================
-- BATCH 22: Medicamentos 211-220 (FINAL - completa 220!)
-- Atomoxetina, Atenolol, Atazanavir, Asenapina,
-- Aripiprazol Lauroxil, Aripiprazol, Apixabana,
-- Anticoncepcionais, Anlodipino, Anfetamina
-- =====================================================
-- Nota: Lista total = 232 medicamentos enviados. 12 foram
-- incluidos em duplicidade (Dronabinol 3x classes) ou sem
-- genes especificados (Anfetamina). Total único = ~220 análises.

-- =====================================================
-- PARTE A: pgx_drug_genes (faz aparecer no dashboard)
-- =====================================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`)
VALUES
('Atomoxetina', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6*4: metabolizador nulo - níveis 5-10x maiores em PM', 'Eric: CYP2D6 N/D. TESTE OBRIGATÓRIO antes de prescrever (FDA).', 'Eric: CYP2D6 N/D. TESTE OBRIGATÓRIO antes de prescrever (FDA).', 'Eric: CYP2D6 N/D. TESTE OBRIGATÓRIO antes de prescrever (FDA).', '1A', 'curated', 1),
('Atenolol', '', 'ADRB2', 'rs1042713', 'substrate', 'Beta-2 Arg16Gly: afeta resposta cardiovascular', 'Eric: ADRB2 GA (Arg/Gly). Resposta intermediária.', 'Eric: ADRB2 GA (Arg/Gly). Resposta intermediária.', 'Eric: ADRB2 GA (Arg/Gly). Resposta intermediária.', '2B', 'curated', 1),
('Atazanavir', '', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17: metabolismo rápido do atazanavir', 'Eric: CYP2C19 *1/*17 (CT). Possível níveis reduzidos.', 'Eric: CYP2C19 *1/*17 (CT). Possível níveis reduzidos.', 'Eric: CYP2C19 *1/*17 (CT). Possível níveis reduzidos.', '2B', 'curated', 1),
('Asenapina', '', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2*1F: alta indutibilidade', 'Eric: CYP1A2 CA (ultra-rápido). Níveis possivelmente menores.', 'Eric: CYP1A2 CA (ultra-rápido). Níveis possivelmente menores.', 'Eric: CYP1A2 CA (ultra-rápido). Níveis possivelmente menores.', '2A', 'curated', 1),
('Aripiprazol Lauroxil', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6*4: metabolizador nulo', 'Eric: CYP2D6 N/D. FDA recomenda ajuste conforme fenótipo.', 'Eric: CYP2D6 N/D. FDA recomenda ajuste conforme fenótipo.', 'Eric: CYP2D6 N/D. FDA recomenda ajuste conforme fenótipo.', '1A', 'curated', 1),
('Aripiprazol', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6*4: metabolizador nulo - FDA recomenda ajuste', 'Eric: CYP2D6 N/D. Iniciar dose baixa, titular. MC4R TT + HTR2C CC favoráveis.', 'Eric: CYP2D6 N/D. Iniciar dose baixa, titular. MC4R TT + HTR2C CC favoráveis.', 'Eric: CYP2D6 N/D. Iniciar dose baixa, titular. MC4R TT + HTR2C CC favoráveis.', '1A', 'curated', 1),
('Apixabana', '', 'ABCG2', 'rs2231142', 'substrate', 'ABCG2 Q141K: transportador reduzido', 'Eric: ABCG2 GG (normal). Transporte normal. Alternativa preferencial à varfarina.', 'Eric: ABCG2 GG (normal). Transporte normal. Alternativa preferencial à varfarina.', 'Eric: ABCG2 GG (normal). Transporte normal. Alternativa preferencial à varfarina.', '2A', 'curated', 1),
('Anticoncepcionais orais (estrogênio)', '', 'F5', 'rs6025', 'substrate', 'Fator V Leiden: risco trombose venosa com estrogênio', 'Eric: F5 CC (normal). SEM Fator V Leiden. Risco trombótico padrão.', 'Eric: F5 CC (normal). SEM Fator V Leiden. Risco trombótico padrão.', 'Eric: F5 CC (normal). SEM Fator V Leiden. Risco trombótico padrão.', '1A', 'curated', 1),
('Anlodipino', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 participa do metabolismo secundário', 'Eric: CYP2D6 N/D. Impacto limitado (CYP3A4 é via principal, CYP3A4 GG normal).', 'Eric: CYP2D6 N/D. Impacto limitado (CYP3A4 é via principal, CYP3A4 GG normal).', 'Eric: CYP2D6 N/D. Impacto limitado (CYP3A4 é via principal, CYP3A4 GG normal).', '3', 'curated', 1),
('Anfetamina', '', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 participa da metabolização (via secundária)', 'Eric: CYP2D6 N/D. Via principal não-CYP. Usar como lisdexanfetamina/dextroanfetamina.', 'Eric: CYP2D6 N/D. Via principal não-CYP. Usar como lisdexanfetamina/dextroanfetamina.', 'Eric: CYP2D6 N/D. Via principal não-CYP. Usar como lisdexanfetamina/dextroanfetamina.', '3', 'curated', 1);


-- =====================================================
-- PARTE B: pgx_drug_details (textos detalhados)
-- =====================================================

-- 1. ATOMOXETINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Atomoxetina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'A atomoxetina é um inibidor seletivo da recaptação de noradrenalina, utilizado como tratamento não-estimulante para TDAH. É especialmente indicada quando estimulantes são contraindicados ou ineficazes. Efeitos adversos: diminuição do apetite, náuseas, dor abdominal, sonolência, cefaleia e, raramente, ideação suicida em crianças/adolescentes. Nomes comerciais: Strattera®, Atentah®.',
  'O gene CYP2D6, localizado no cromossomo 22, é responsável pelo metabolismo PRINCIPAL da atomoxetina. Metabolizadores lentos do CYP2D6 apresentam níveis plasmáticos 5-10 VEZES maiores e meia-vida prolongada (21h vs 5h). A FDA EXIGE ajuste de dose conforme fenótipo CYP2D6. Eric NÃO possui tipagem do CYP2D6 (rs3892097 não disponível no chip Genera GSA v3.0).',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric NÃO possui tipagem do CYP2D6. Para atomoxetina, o CYP2D6 é CRÍTICO: metabolizadores lentos têm níveis 5-10x maiores com risco significativo de efeitos adversos.',
  'ATOMOXETINA PARA TDAH NO ERIC: SEM TIPAGEM DO CYP2D6, é ALTO RISCO prescrever atomoxetina sem teste prévio. A diferença entre metabolizadores normais e lentos é de 5-10x nos níveis plasmáticos! RECOMENDAÇÃO FORTE: Solicitar teste CYP2D6 ANTES de iniciar. Se teste não disponível, PREFERIR alternativas que não dependem do CYP2D6: (1) Guanfacina (CYP3A4 normal), (2) Metilfenidato/Lisdexanfetamina (não-CYP), (3) Bupropiona off-label (CI epilepsia). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Strattera, Atentah',
  '1A',
  'CYP2D6 e atomoxetina: evidência MUITO FORTE (1A). FDA label. Diferença de 5-10x em níveis para PM vs EM. TESTE OBRIGATÓRIO.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 2. ATENOLOL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Atenolol',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'O atenolol é um betabloqueador cardiosseletivo (beta-1), utilizado no tratamento de hipertensão, angina, arritmias e profilaxia de enxaqueca. Diferente do metoprolol, NÃO é metabolizado pelo CYP2D6 (excreção renal). Efeitos adversos: bradicardia, fadiga, extremidades frias, broncoespasmo. Nomes comerciais: Atenol®, Areblaz®, Ateneum®, Atenopress®.',
  'O gene ADRB2 (receptor beta-2 adrenérgico), localizado no cromossomo 5, possui a variante rs1042713 (Arg16Gly) que afeta a resposta cardiovascular a betabloqueadores. O gene GNB3 (rs5443) modula a transdução de sinal. Eric: ADRB2 GA (Arg/Gly heterozigoto) e GNB3 CC (normal).',
  'rs1042713', '5', 'ADRB2', 'G,A',
  'Europeia',
  'Eric possui ADRB2 GA (Arg/Gly) = resposta intermediária. GNB3 CC = normal. Combinação sugere resposta padrão ao atenolol.',
  'ADRB2 GA com GNB3 CC: resposta provavelmente adequada ao atenolol. VANTAGEM DO ATENOLOL: NÃO depende do CYP2D6 (diferente do metoprolol). Como o CYP2D6 do Eric é desconhecido, atenolol é PREFERÍVEL ao metoprolol se betabloqueador for necessário. Monitorar FC e PA. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Atenol, Areblaz, Ateneum, Atenopress, Himaagin',
  '2B',
  'ADRB2 e betabloqueadores: evidência moderada (2B). Vantagem do atenolol: eliminação renal, não depende CYP2D6.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 3. ATAZANAVIR
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Atazanavir',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Infectologia'),
  'O atazanavir é um antirretroviral inibidor de protease do HIV, geralmente co-administrado com ritonavir. Efeitos adversos: hiperbilirrubinemia (icterícia), nefrolitíase, prolongamento PR, hiperglicemia. Nomes comerciais: Reyataz®.',
  'O gene CYP2C19 participa parcialmente do metabolismo do atazanavir. A variante *17 (rs12248560 T) confere metabolismo mais rápido. Eric: CYP2C19 CT (*1/*17) = metabolismo aumentado, possivelmente reduzindo níveis do atazanavir.',
  'rs12248560', '10', 'CYP2C19', 'C,T',
  'Europeia',
  'Eric: CYP2C19 *1/*17 (CT). Metabolismo rápido pode reduzir níveis do atazanavir. Porém, como é sempre usado com booster (ritonavir/cobicistat), o impacto clínico é ATENUADO.',
  'CYP2C19*17 pode reduzir níveis de atazanavir, mas como é SEMPRE usado com ritonavir (inibidor potente de CYP3A4), o impacto clínico do CYP2C19 é ATENUADO. Monitorar carga viral conforme protocolo. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Reyataz',
  '2B',
  'CYP2C19 e atazanavir: evidência moderada (2B). Impacto atenuado pelo booster (ritonavir).'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 4. ASENAPINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Asenapina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'A asenapina é um antipsicótico atípico administrado por via sublingual, utilizado em esquizofrenia e mania bipolar. Antagonista D2/5-HT2A com perfil de receptores amplo. Efeitos adversos: sonolência, acatisia, ganho de peso moderado, hipoestesia oral. Nomes comerciais: Saphris®, Secuado® (transdérmico).',
  'O gene CYP1A2, localizado no cromossomo 15, participa do metabolismo da asenapina. A-- =====================================================
-- BATCH 22B: Medicamentos 214-220 (continuação final)
-- Asenapina, Aripiprazol Lauroxil, Aripiprazol, Apixabana,
-- Anticoncepcionais, Anlodipino, Anfetamina
-- =====================================================

-- 4. ASENAPINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Asenapina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'A asenapina é um antipsicótico atípico sublingual para esquizofrenia e mania bipolar. Antagonista D2/5-HT2A. Efeitos adversos: sonolência, acatisia, ganho de peso moderado, hipoestesia oral. Nomes comerciais: Saphris®, Secuado®.',
  'O gene CYP1A2, no cromossomo 15, metaboliza a asenapina. A variante rs762551 (*1F, alelo A) confere alta indutibilidade. Eric possui CYP1A2 CA = metabolizador ultra-rápido (induzível). Isso pode resultar em níveis menores de asenapina, especialmente se exposto a indutores (tabaco, carne grelhada).',
  'rs762551', '15', 'CYP1A2', 'C,A',
  'Europeia',
  'Eric: CYP1A2 CA (ultra-rápido induzível). Níveis de asenapina podem ser menores que o esperado. Porém, como via sublingual, a biodisponibilidade é menos CYP-dependente.',
  'CYP1A2 CA: metabolismo potencialmente mais rápido. PORÉM: asenapina sublingual tem biodisponibilidade de 35% por via sublingual vs <2% se engolida. O metabolismo hepático de primeira passagem é parcialmente contornado pela via sublingual. Impacto clínico do CYP1A2 é MODERADO. Monitorar resposta. Via transdérmica (Secuado) também contorna CYP1A2. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Saphris, Secuado',
  '2A',
  'CYP1A2 e asenapina: evidência moderada (2A). Via sublingual atenua impacto do CYP1A2. Eric CA = potencialmente rápido.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 5. ARIPIPRAZOL LAUROXIL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Aripiprazol Lauroxil',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O aripiprazol lauroxil é a forma injetável de ação prolongada (LAI) do aripiprazol, administrado mensalmente ou a cada 2 meses. Pró-droga que é convertida em aripiprazol. Indicado para esquizofrenia. Efeitos adversos: acatisia, insônia, cefaleia, reações no local da injeção. Nomes comerciais: Aristada®.',
  'O gene CYP2D6, no cromossomo 22, metaboliza o aripiprazol (forma ativa). Metabolizadores lentos CYP2D6 apresentam níveis ~60% maiores. A FDA recomenda ajuste de dose. Eric NÃO possui tipagem CYP2D6.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D. Para forma LAI, ajuste é mais crítico pois não pode ser revertido rapidamente.',
  'ARIPIPRAZOL LAI SEM CYP2D6: ALTO RISCO. Como é injeção de longa duração, se Eric for metabolizador lento, os níveis elevados persistirão por semanas. RECOMENDAÇÃO: NÃO iniciar forma LAI sem tipagem CYP2D6 prévia. Se aripiprazol oral for tolerado, pode-se inferir fenótipo. FDA: PM CYP2D6 = reduzir para 441mg (ao invés de 882mg). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Aristada',
  '1A',
  'CYP2D6 e aripiprazol LAI: evidência forte (1A). FDA label. Forma LAI = irreversibilidade = teste ANTES.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 6. ARIPIPRAZOL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Aripiprazol',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O aripiprazol é um antipsicótico atípico, agonista parcial D2/5-HT1A e antagonista 5-HT2A. Utilizado em esquizofrenia, mania bipolar, depressão adjuvante, irritabilidade no autismo e Tourette. É um dos antipsicóticos mais prescritos para TEA. Efeitos adversos: acatisia, insônia, náuseas, cefaleia, ganho de peso moderado. Nomes comerciais: Abilify®, Aristab®, Toarip®, Sensaz®.',
  'O gene CYP2D6 metaboliza o aripiprazol. PM CYP2D6 = níveis ~60% maiores. FDA recomenda redução de dose. Além disso, MC4R (rs17782313) e HTR2C (rs3813929) influenciam ganho de peso. Eric: CYP2D6 N/D, MC4R TT (normal), HTR2C CC (risco padrão de ganho de peso).',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D. MC4R TT e HTR2C CC = menor risco de ganho de peso metabólico com antipsicóticos. Perfil favorável EXCETO pela incerteza do CYP2D6.',
  'ARIPIPRAZOL PARA AUTISMO: MC4R TT e HTR2C CC = perfil FAVORÁVEL para menor ganho de peso. CYP2D6 desconhecido: iniciar com dose BAIXA (2mg) e titular lentamente. Se boa tolerância em 2-5mg, fenótipo provavelmente não é PM. ALTERNATIVAS se CYP2D6 for preocupação: Paliperidona (não CYP2D6), Lurasidona (CYP3A4 normal). Para irritabilidade no TEA, aripiprazol tem aprovação FDA. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Abilify, Aristab, Toarip, Sensaz',
  '1A',
  'CYP2D6 e aripiprazol: evidência forte (1A). FDA label. MC4R/HTR2C: evidência 2B para ganho de peso.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 7. APIXABANA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Apixabana',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'A apixabana é um anticoagulante oral direto (DOAC), inibidor do fator Xa. Utilizada na prevenção de AVC em fibrilação atrial e tratamento/prevenção de trombose venosa. NÃO requer monitoramento de INR. Efeitos adversos: sangramento, hematomas, anemia. Nomes comerciais: Eliquis®.',
  'O gene ABCG2, no cromossomo 4, codifica um transportador de efluxo. A variante rs2231142 (Q141K) reduz a função do transportador, podendo aumentar níveis de apixabana. Eric: ABCG2 GG (normal) = transporte normal. Além disso, F5 Leiden (rs6025) CC = sem trombofilia hereditária.',
  'rs2231142', '4', 'ABCG2', 'G,G',
  'Europeia',
  'Eric: ABCG2 GG (normal). Transporte adequado da apixabana. F5 Leiden CC = sem Fator V Leiden.',
  'Apixabana é ALTERNATIVA PREFERENCIAL à varfarina para Eric! Motivos: (1) VKORC1 TT torna varfarina muito sensível/instável; (2) ABCG2 GG = transporte normal; (3) F5 CC = sem trombofilia; (4) NÃO requer monitoramento de INR; (5) NÃO depende de CYP2C9 nem VKORC1. Se anticoagulação necessária (peri-operatória ou outra), PREFERIR apixabana ou rivaroxabana. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Eliquis',
  '2A',
  'ABCG2 e apixabana: evidência moderada (2A). Apixabana preferencial à varfarina quando VKORC1 TT.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 8. ANTICONCEPCIONAIS ORAIS (ESTROGÊNIO)
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anticoncepcionais orais (estrogênio)',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'Os anticoncepcionais orais combinados contêm estrogênio (etinilestradiol) e progestágeno. Embora primariamente contraceptivos, a análise farmacogenética foca no RISCO TROMBÓTICO associado ao estrogênio. Portadores do Fator V Leiden têm risco 7-30x aumentado de trombose venosa com estrogênio. Nomes comerciais: Yasmin®, Diane®, Belara®, Ciclo21®.',
  'O gene F5, no cromossomo 1, codifica o Fator V da coagulação. A mutação Leiden (rs6025 G>A) causa resistência à proteína C ativada e risco 3-8x de trombose venosa. Com estrogênio, o risco é sinérgico (7-30x). Eric: F5 CC (referência) = SEM Fator V Leiden.',
  'rs6025', '1', 'F5', 'C,C',
  'Europeia',
  'Eric: F5 CC = SEM Fator V Leiden. Risco trombótico com estrogênio é o PADRÃO populacional (não aumentado geneticamente).',
  'F5 CC: Sem Fator V Leiden. Se anticoncepção hormonal ou terapia estrogênica forem necessárias no futuro, não há contraindicação farmacogenética por esta via. Risco trombótico é o padrão da população. Considerar outros fatores de risco (obesidade, imobilização, cirurgia). PARA A CIRURGIA MAXILAR: Se Eric estiver em uso de qualquer medicamento estrogênico, informar cirurgião. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Yasmin, Diane, Belara, Ciclo21',
  '1A',
  'Fator V Leiden e estrogênio: evidência MUITO forte (1A). Eric CC = sem risco genético adicional.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 9. ANLODIPINO
INSERT INTO pgx_drug_details (drug-- =====================================================
-- BATCH 22C: Medicamentos 219-220 (Anlodipino, Anfetamina)
-- FINAL DOS 232 MEDICAMENTOS!
-- =====================================================

-- 9. ANLODIPINO
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anlodipino',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'O anlodipino é um bloqueador de canais de cálcio diidropiridínico, utilizado no tratamento de hipertensão e angina. Possui meia-vida longa (30-50h). Via principal: CYP3A4 (com contribuição menor do CYP2D6). Efeitos adversos: edema periférico, cefaleia, rubor, tontura. Nomes comerciais: Norvasc®, Alivpress®, Anlo®, Tenlopin®, Tensaliv®.',
  'O CYP3A4 é a via PRINCIPAL de metabolismo do anlodipino, com contribuição SECUNDÁRIA do CYP2D6. Eric: CYP3A4 GG (normal). Como a via principal está normal, o impacto do CYP2D6 desconhecido é LIMITADO.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D mas CYP3A4 GG (normal). Como CYP3A4 é a via PRINCIPAL, o impacto do CYP2D6 desconhecido é clinicamente LIMITADO para anlodipino.',
  'CYP3A4 é a via principal do anlodipino e está NORMAL (GG). O CYP2D6 é via secundária com impacto limitado. Anlodipino pode ser usado normalmente. VANTAGEM: não depende criticamente do CYP2D6 desconhecido. Se anti-hipertensivo necessário, anlodipino é opção farmacogeneticamente aceitável. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Norvasc, Alivpress, Anlo, Tenlopin, Tensaliv',
  '3',
  'CYP2D6 e anlodipino: evidência baixa (3). CYP3A4 é via principal (normal). Impacto clínico do CYP2D6 limitado.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 10. ANFETAMINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anfetamina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'As anfetaminas são psicoestimulantes utilizados no tratamento de TDAH e narcolepsia. Incluem dextroanfetamina, anfetamina racêmica e lisdexanfetamina (pró-droga). Mecanismo: liberação de dopamina e noradrenalina. Efeitos adversos: insônia, diminuição de apetite, taquicardia, ansiedade, irritabilidade. Nomes comerciais: Evekeo®, Dynavel®, Adzenys®. No Brasil, a lisdexanfetamina (Venvanse®) é a forma mais acessível.',
  'As anfetaminas são primariamente metabolizadas por desaminação oxidativa (MAO) e conjugação (não-CYP). O CYP2D6 participa apenas de vias SECUNDÁRIAS de metabolismo. O impacto farmacogenético é LIMITADO para anfetaminas. Os genes mais relevantes para RESPOSTA são DRD1, SLC6A3 e COMT.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'CYP2D6 N/D mas impacto LIMITADO para anfetaminas (via secundária). DRD1 TT e COMT AG são mais relevantes para resposta.',
  'ANFETAMINAS PARA TDAH NO ERIC: O CYP2D6 desconhecido tem impacto LIMITADO (via secundária). PORÉM, DRD1 rs4532 TT sugere resposta VARIÁVEL a estimulantes dopaminérgicos. RECOMENDAÇÃO: (1) Lisdexanfetamina (Venvanse) é preferível pois é convertida por hidrólise (não-CYP); (2) Monitorar resposta clinicamente; (3) Se resposta insuficiente, considerar Guanfacina (não-estimulante, CYP3A4 normal). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Evekeo, Dynavel, Adzenys, Venvanse (lisdexanfetamina)',
  '3',
  'CYP2D6 e anfetaminas: evidência baixa (3) para metabolismo. DRD1/COMT: evidência moderada (2B) para resposta clínica.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);