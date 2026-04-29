-- =====================================================
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
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_