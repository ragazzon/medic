-- =====================================================
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

INSERT IGNORE INTO pgx_drug_genes (drug_name, gene_name, rsid, effect_allele, effect_description, recommendation, evidence_level)
VALUES
('Atomoxetina', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6*4: metabolizador nulo - níveis 5-10x maiores em PM', 'Eric: CYP2D6 N/D. TESTE OBRIGATÓRIO antes de prescrever (FDA).', '1A'),
('Atenolol', 'ADRB2', 'rs1042713', 'A', 'Beta-2 Arg16Gly: afeta resposta cardiovascular', 'Eric: ADRB2 GA (Arg/Gly). Resposta intermediária.', '2B'),
('Atazanavir', 'CYP2C19', 'rs12248560', 'T', 'CYP2C19*17: metabolismo rápido do atazanavir', 'Eric: CYP2C19 *1/*17 (CT). Possível níveis reduzidos.', '2B'),
('Asenapina', 'CYP1A2', 'rs762551', 'A', 'CYP1A2*1F: alta indutibilidade', 'Eric: CYP1A2 CA (ultra-rápido). Níveis possivelmente menores.', '2A'),
('Aripiprazol Lauroxil', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6*4: metabolizador nulo', 'Eric: CYP2D6 N/D. FDA recomenda ajuste conforme fenótipo.', '1A'),
('Aripiprazol', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6*4: metabolizador nulo - FDA recomenda ajuste', 'Eric: CYP2D6 N/D. Iniciar dose baixa, titular. MC4R TT + HTR2C CC favoráveis.', '1A'),
('Apixabana', 'ABCG2', 'rs2231142', 'T', 'ABCG2 Q141K: transportador reduzido', 'Eric: ABCG2 GG (normal). Transporte normal. Alternativa preferencial à varfarina.', '2A'),
('Anticoncepcionais orais (estrogênio)', 'F5', 'rs6025', 'A', 'Fator V Leiden: risco trombose venosa com estrogênio', 'Eric: F5 CC (normal). SEM Fator V Leiden. Risco trombótico padrão.', '1A'),
('Anlodipino', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6 participa do metabolismo secundário', 'Eric: CYP2D6 N/D. Impacto limitado (CYP3A4 é via principal, CYP3A4 GG normal).', '3'),
('Anfetamina', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6 participa da metabolização (via secundária)', 'Eric: CYP2D6 N/D. Via principal não-CYP. Usar como lisdexanfetamina/dextroanfetamina.', '3');

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
  'O gene CYP1A2, localizado no cromossomo 15, participa do metabolismo da asenapina. A