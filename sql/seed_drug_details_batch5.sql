-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 5 de N)
-- 10 medicamentos: Salbutamol → Rasagilina
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Salbutamol', 'Antiasmáticos (Beta-2 agonistas)', 'ADRB2', 'rs1042713', 'target', 'ADRB2 Arg16Gly modula resposta a beta-2 agonistas', 'Arg/Arg - melhor resposta', 'Arg/Gly - resposta intermediária', 'Gly/Gly - menor resposta', '2A', 'PharmGKB', 1),
('Sacubitril', 'Antagonistas de Angiotensina II', 'CES1', 'rs2244613', 'substrate', 'CES1 ativa sacubitril (pró-droga)', 'Ativação normal', 'Ativação variável', 'Ativação reduzida', '3', 'PharmGKB', 1),
('Rosuvastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta rosuvastatina - menor impacto que sinvastatina', 'Dose padrão', 'Monitorar - aumento moderado de níveis', 'Considerar dose menor', '2A', 'CPIC', 1),
('Rosuvastatina', 'Estatinas', 'ABCG2', 'rs2231142', 'transporter', 'ABCG2 Q141K afeta efluxo da rosuvastatina', 'Normal', 'Níveis aumentados', 'Níveis muito aumentados', '2A', 'PharmGKB', 1),
('Ropivacaína', 'Anestésicos Locais', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente aumenta risco de metemoglobinemia', 'Sem risco', 'Cautela', 'Risco aumentado', '3', 'PharmGKB', 1),
('Rivaroxabana', 'Anticoagulantes', 'F5', 'rs6025', 'target', 'Fator V Leiden indica necessidade de anticoagulação', 'Sem trombofilia por F5', 'Risco trombótico aumentado - anticoagulação benéfica', 'Alto risco - anticoagulação essencial', '1A', 'CPIC', 1),
('Risperidona', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza risperidona em 9-OH-risperidona', 'Dose padrão', 'Monitorar efeitos', 'Reduzir dose - acúmulo', '1A', 'DPWG', 1),
('Risperidona', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula risco de ganho de peso', 'Sem risco aumentado', 'Risco moderado', 'Risco alto de obesidade', '2B', 'PharmGKB', 1),
('Risperidona', 'Antipsicóticos', 'DRD2', 'rs6277', 'target', 'DRD2 C957T afeta estabilidade mRNA e disponibilidade de receptores D2', 'Normal', 'mRNA instável - resposta variável', 'mRNA muito instável', '3', 'PharmGKB', 1),
('Repaglinida', 'Antidiabéticos', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta repaglinida para fígado', 'Dose padrão', 'Níveis aumentados - risco de hipoglicemia', 'Níveis muito aumentados', '2A', 'PharmGKB', 1),
('Remifentanila', 'Anestésicos/Analgésicos Opioides', 'OPRM1', 'rs1799971', 'target', 'OPRM1 modula resposta a opioides', 'Resposta padrão', 'Pode necessitar dose maior', 'Dose significativamente maior', '2A', 'PharmGKB', 1),
('Rasburicase', 'Profiláticos de Hiperuricemia', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - rasburicase causa hemólise e metemoglobinemia', 'Sem risco', 'Teste quantitativo', 'CONTRAINDICADO', '1A', 'FDA', 1),
('Rasagilina', 'Antiparkinsonianos (IMAO-B)', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza rasagilina', 'Dose padrão', 'Metabolismo aumentado', 'Metabolismo ultra-rápido - eficácia reduzida', '2B', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. SALBUTAMOL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Salbutamol', (SELECT id FROM pgx_drug_classes WHERE code='pneumo' LIMIT 1),
    'Aerolin®, Aerotrar®, Broncofedrin®, Regrair®, Pulmoflux®',
    'O salbutamol (albuterol) é um beta-2 agonista de curta ação (SABA) utilizado como broncodilatador de resgate na asma e DPOC. Atua relaxando a musculatura brônquica em minutos. O gene ADRB2 modula a resposta. Efeitos adversos incluem tremor, taquicardia, cefaleia e hipocalemia.',
    'O ADRB2 (cromossomo 5) codifica o receptor beta-2 adrenérgico. O genótipo GA (Arg/Gly) em rs1042713 indica resposta INTERMEDIÁRIA. A Genera identificou esta mesma predisposição para menor taxa de resposta.',
    'rs1042713', '5', 'ADRB2', 'Europeia',
    'ADRB2 rs1042713 (Arg16Gly):\n- AA (Arg/Arg) = Melhor resposta broncodilatadora\n- GA (Arg/Gly) = Resposta intermediária\n- GG (Gly/Gly) = Menor resposta ao uso regular',
    'ADRB2 GA (Arg/Gly) = resposta INTERMEDIÁRIA ao salbutamol. Confirma achado da Genera. Se controle insuficiente, considerar aumento da terapia de manutenção (corticoide inalatório). Para uso de RESGATE (agudo), o efeito é menos afetado.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. SACUBITRIL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sacubitril', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Entresto® (sacubitril/valsartana)',
    'O sacubitril é uma pró-droga inibidora da neprilisina, sempre usada em combinação com valsartana (Entresto). Indicado para insuficiência cardíaca com fração de ejeção reduzida. É ativado pela carboxilesterase 1 (CES1). Efeitos adversos incluem hipotensão, hipercalemia, insuficiência renal e angioedema.',
    'O gene CES1 (cromossomo 16) codifica a carboxilesterase 1 que ativa o sacubitril. O genótipo TT em rs2244613 indica ativação NORMAL da pró-droga.',
    'rs2244613', '16', 'CES1', 'Europeia',
    'CES1 rs2244613:\n- TT/CC = Normal (ativação adequada)\n- CT = Ativação variável\n- Variantes raras podem reduzir ativação',
    'CES1 normal (TT). Sacubitril/valsartana (Entresto) deve ser ativado adequadamente. Dose padrão apropriada.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. ROSUVASTATINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Rosuvastatina', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Crestor®, Rosuvast®, Plenance®, Rostatin®, Rosulib®',
    'A rosuvastatina é a estatina mais potente disponível, utilizada para redução do colesterol LDL. É MENOS afetada pelo SLCO1B1 que a sinvastatina, sendo alternativa preferencial para pacientes com variante *5. É parcialmente transportada pelo ABCG2. Efeitos adversos incluem mialgia (menos que sinvastatina), hepatotoxicidade e proteinúria (doses altas).',
    'SLCO1B1 (rs4149056 TC) tem impacto MODERADO na rosuvastatina (menor que na sinvastatina). ABCG2 (rs2231142 GG) é NORMAL. A rosuvastatina é uma ALTERNATIVA PREFERENCIAL à sinvastatina para este paciente.',
    'rs4149056', '12', 'SLCO1B1',
    'Europeia',
    'SLCO1B1 rs4149056 para Rosuvastatina:\n- TT = Normal\n- TC = Aumento moderado dos níveis (~65%) - monitorar\n- CC = Aumento significativo - iniciar dose menor\n\nABCG2 rs2231142 (Q141K):\n- GG = Normal (BCRP funcional)\n- GT = BCRP reduzida (níveis ~140% maiores)\n- TT = BCRP muito reduzida',
    'SLCO1B1 TC + ABCG2 GG. A rosuvastatina é ALTERNATIVA PREFERENCIAL à sinvastatina para Eric (que tem SLCO1B1 TC). O impacto do SLCO1B1 na rosuvastatina é menor que na sinvastatina. ABCG2 normal. Monitorar resposta e CPK.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. ROPIVACAÍNA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ropivacaína', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Naropin®, Ovipac®, Ropi®, Pigesic®',
    'A ropivacaína é um anestésico local do tipo amida, utilizada para anestesia regional (epidural, bloqueios nervosos) e analgesia pós-operatória. Possui perfil cardiovascular mais seguro que a bupivacaína. Em pacientes com deficiência de G6PD, há risco teórico de metemoglobinemia. Efeitos adversos incluem hipotensão, bradicardia e toxicidade neurológica/cardíaca em doses altas.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco aumentado de metemoglobinemia.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT = Normal\n- Deficientes: risco teórico de metemoglobinemia com anestésicos locais',
    'RELEVANTE PARA CIRURGIA MAXILAR: G6PD normal. Ropivacaína pode ser usada com segurança para anestesia/analgesia regional na cirurgia. Alternativa segura à bupivacaína com melhor perfil cardiovascular.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. RIVAROXABANA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Rivaroxabana', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Xarelto®, Acog®, Rixantil®, Vynaxa®',
    'A rivaroxabana é um anticoagulante oral direto (DOAC) inibidor do Fator Xa, utilizada para prevenção e tratamento de tromboembolismo venoso, fibrilação atrial e após cirurgias ortopédicas. NÃO depende do VKORC1 (diferente da varfarina). O gene F5 (Fator V Leiden) indica predisposição trombótica e necessidade de anticoagulação. Efeitos adversos incluem sangramento, anemia e hepatotoxicidade.',
    'F5 rs6025 (Fator V Leiden): genótipo CC = SEM mutação Leiden = SEM trombofilia hereditária por este mecanismo. A rivaroxabana não tem ajuste farmacogenético de dose — é uma alternativa à varfarina que NÃO é afetada pelo VKORC1 (relevante pois Eric tem VKORC1 TT).',
    'rs6025', '1', 'F5', 'Europeia',
    'F5 rs6025 (Fator V Leiden):\n- CC = Normal (sem mutação Leiden)\n- CT = Heterozigoto (risco trombótico 5-7x)\n- TT = Homozigoto (risco trombótico 50-80x)\n\nNota: Rivaroxabana é alternativa à varfarina que NÃO depende do VKORC1.',
    'F5 Leiden NEGATIVO (CC). Sem trombofilia por Fator V. A rivaroxabana é ALTERNATIVA PREFERENCIAL à varfarina para Eric, pois NÃO depende do VKORC1 (que está alterado: TT = muito sensível). Se anticoagulação for necessária, preferir DOACs (rivaroxabana, apixabana) em vez de varfarina.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. RISPERIDONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Risperidona', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Risperdal®, Exrisp®, Respidon®, Riss®, Viverdal®',
    'A risperidona é um antipsicótico atípico amplamente utilizado em autismo (irritabilidade, agressividade), esquizofrenia e transtorno bipolar. É metabolizada pelo CYP2D6 em 9-OH-risperidona (paliperidona). Os genes MC4R, DRD2 e HTR2C modulam efeitos colaterais (peso, resposta). Efeitos adversos incluem ganho de peso, hiperprolactinemia, sedação e sintomas extrapiramidais.',
    'CYP2D6 (rs3892097) N/D - não é possível determinar metabolismo. MC4R (TT) = sem risco aumentado de obesidade. DRD2 (rs6277 GA) = mRNA com estabilidade intermediária, resposta variável. HTR2C (CC) = risco padrão de ganho de peso.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097:\n- N/D (não disponível)\n\nMC4R rs17782313: TT = Normal\nDRD2 rs6277 (C957T): GA/CT = mRNA instável (resposta variável)\nHTR2C rs3813929: CC = Risco padrão de ganho de peso',
    'RELEVANTE PARA AUTISMO: CYP2D6 indeterminado. MC4R e HTR2C normais (sem risco genético aumentado de obesidade). DRD2 GA sugere resposta variável — monitorar eficácia. Se risperidona for prescrita, iniciar dose baixa e titular. Monitorar peso, prolactina e glicemia.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. REPAGLINIDA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Repaglinida', (SELECT id FROM pgx_drug_classes WHERE code='diabetes' LIMIT 1),
    'Novonorm®, Diaglinida®, Niglin®, Reglinid®, Repadiab®',
    'A repaglinida é um secretagogo de insulina (meglitinida) de ação rápida e curta, utilizada para diabetes tipo 2. Estimula a secreção de insulina pelas células beta pancreáticas. É transportada pelo SLCO1B1 para o fígado para metabolização. Efeitos adversos incluem hipoglicemia, ganho de peso e dor abdominal.',
    'SLCO1B1 (rs4149056 TC) = transportador REDUZIDO. Isso resulta em níveis plasmáticos AUMENTADOS de repaglinida, com maior risco de hipoglicemia.',
    'rs4149056', '12', 'SLCO1B1', 'Europeia',
    'SLCO1B1 rs4149056 (*5) para Repaglinida:\n- TT = Normal\n- TC = Níveis aumentados (~60-70%) - risco de hipoglicemia\n- CC = Níveis muito aumentados - risco significativo',
    'SLCO1B1 TC = níveis de repaglinida aumentados. Se prescrita para diabetes, iniciar com dose menor e monitorar glicemia cuidadosamente pelo risco aumentado de hipoglicemia.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. REMIFENTANILA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Remifentanila', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Ultiva®, Remifas®',
    'A remifentanila é um opioide sintético ultra-curto utilizado em anestesia geral e procedimentos. Tem meia-vida de 3-10 minutos por ser metabolizada por esterases plasmáticas (não depende do fígado/CYP). NÃO depende do CYP2D6. O OPRM1 modula a resposta. Efeitos adversos incluem rigidez muscular, bradicardia, hipotensão e depressão respiratória.',
    'OPRM1 (rs1799971 AA) = receptor opioide mu NORMAL. Resposta analgésica padrão esperada. Remifentanila NÃO depende de CYP450 para metabolismo — é inativada por esterases inespecíficas do sangue.',
    'rs1799971', '6', 'OPRM1', 'Europeia',
    'OPRM1 rs1799971:\n- AA = Resposta padrão\n- AG = Pode necessitar dose maior\n- GG = Dose significativamente maior\n\nNota: Remifentanila NÃO depende de CYP2D6 nem CYP3A4.',
    'RELEVANTE PARA CIRURGIA MAXILAR: OPRM1 AA = resposta normal. Remifentanila é EXCELENTE opção para anestesia na cirurgia pois: 1) NÃO depende do CYP2D6 (desconhecido), 2) NÃO depende do CYP3A4, 3) Metabolizada por esterases (início/término ultrarrápidos), 4) OPRM1 normal.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. RASBURICASE
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Rasburicase', (SELECT id FROM pgx_drug_classes WHERE code='gota' LIMIT 1),
    'Fasturtek®, Elitek®',
    'A rasburicase é uma urato oxidase recombinante utilizada para profilaxia e tratamento da hiperuricemia associada à síndrome de lise tumoral em quimioterapia. A FDA CONTRAINDICA em pacientes com deficiência de G6PD pois causa hemólise grave e metemoglobinemia potencialmente fatal. Efeitos adversos incluem reações anafiláticas, hemólise e metemoglobinemia.',
    'G6PD (rs1050829 TT) = NORMAL. Sem contraindicação genética.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT (homens) = Normal → Rasburicase pode ser usada\n- Deficientes → CONTRAINDICADO (hemólise + metemoglobinemia fatal)',
    'G6PD normal (TT). Rasburicase pode ser usada com segurança do ponto de vista genético. FDA exige teste de G6PD antes da administração.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. RASAGILINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Rasagilina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Azilect®',
    'A rasagilina é um inibidor irreversível da MAO-B utilizado para doença de Parkinson (monoterapia ou adjuvante). É metabolizada pelo CYP1A2. Em metabolizadores ultra-rápidos do CYP1A2, a rasagilina pode ser eliminada mais rapidamente. Efeitos adversos incluem cefaleia, artralgia, dispepsia e hipotensão ortostática.',
    'O CYP1A2 (cromossomo 15) metaboliza a rasagilina. O genótipo CA em rs762551 (*1F) indica METABOLIZADOR ULTRA-RÁPIDO. O CYP1A2 pode ser induzido por tabagismo, carne grelhada e vegetais crucíferos. O genótipo CA indica uma cópia do alelo de alta indutibilidade.',
    'rs762551', '15', 'CYP1A2',
    'Europeia',
    'CYP1A2 rs762551 (*1F):\n- CC = Metabolizador normal\n- CA = Metabolizador ultra-rápido (heterozigoto)\n- AA = Metabolizador ultra-rápido (homozigoto)\n\nNota: CYP1A2 também metaboliza cafeína e melatonina.',
    'CYP1A2 CA = metabolizador ULTRA-RÁPIDO. Rasagilina pode ser eliminada mais rapidamente. Evidência farmacogenética moderada (2B). Monitorar resposta clínica. Nota: este mesmo genótipo faz com que a cafeína seja metabolizada mais rapidamente e a melatonina tenha efeito mais curto.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
