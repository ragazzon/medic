-- =====================================================
-- BATCH 21: Medicamentos 201-210
-- Buprenorfina, Bupivacaína, Bumetanida, Bromocriptina,
-- Brivaracetam, Brexpiprazol, Benazepril, Azitromicina,
-- Azatioprina, Atorvastatina
-- =====================================================

-- =====================================================
-- PARTE A: pgx_drug_genes (faz aparecer no dashboard)
-- =====================================================

INSERT IGNORE INTO pgx_drug_genes (drug_name, gene_name, rsid, effect_allele, effect_description, recommendation, evidence_level)
VALUES
('Buprenorfina', 'OPRD1', 'rs678849', 'T', 'Receptor delta-opioide: variante associada a resposta variável', 'Monitorar resposta analgésica. COMT AG pode exigir dose maior.', '2B'),
('Bupivacaína', 'G6PD', 'rs1050829', 'C', 'Deficiência de G6PD pode causar metemoglobinemia', 'Eric: G6PD TT (normal). Uso seguro.', '1A'),
('Bumetanida', 'GNB3', 'rs5443', 'T', 'Subunidade beta3 da proteína G: afeta resposta a diuréticos', 'Eric: GNB3 CC (normal). Resposta padrão esperada.', '3'),
('Bromocriptina', 'CYP3A4', 'rs35599367', 'A', 'CYP3A4*22: atividade reduzida', 'Eric: CYP3A4 GG (normal). Metabolismo padrão.', '2A'),
('Brivaracetam', 'CYP2C19', 'rs12248560', 'T', 'CYP2C19*17: metabolizador rápido', 'Eric: CYP2C19 *1/*17 (CT). Metabolismo mais rápido - possível duração menor.', '2B'),
('Brexpiprazol', 'CYP2D6', 'rs3892097', 'A', 'CYP2D6*4: metabolizador nulo', 'Eric: CYP2D6 N/D. Sem tipagem - FDA recomenda ajuste conforme fenótipo.', '1A'),
('Benazepril', 'AGT', 'rs699', 'G', 'Angiotensinogênio M235T: afeta resposta a iECAs', 'Eric: AGT AG (heterozigoto). Resposta intermediária a iECAs.', '2B'),
('Azitromicina', 'ABCB1', 'rs1045642', 'T', 'P-glicoproteína: afeta distribuição tecidual', 'Eric: ABCB1 N/D. Impacto clínico limitado para azitromicina.', '3'),
('Azatioprina', 'TPMT', 'rs1800460', 'A', 'TPMT*3B: atividade reduzida - risco mielossupressão', 'Eric: TPMT CC (normal). Dose padrão segura.', '1A'),
('Atorvastatina', 'SLCO1B1', 'rs4149056', 'C', 'SLCO1B1*5: transporte hepático reduzido - risco miopatia', 'Eric: SLCO1B1 TC (heterozigoto). Risco moderado miopatia. Preferir rosuvastatina.', '1A');

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
INSERT INTO pgx_drug_