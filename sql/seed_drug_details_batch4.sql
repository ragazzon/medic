-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 4 de N)
-- 10 medicamentos: Sumatriptano → Salmeterol
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes (faz aparecer no dashboard)
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Sumatriptano', 'Triptanos (Enxaqueca)', 'GNB3', 'rs5443', 'target', 'GNB3 C825T modula resposta a triptanos', 'Resposta padrão', 'Resposta variável', 'Melhor resposta possível', '3', 'PharmGKB', 1),
('Sulfassalazina', 'Aminossalicilatos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente pode causar hemólise com sulfassalazina', 'Sem risco', 'Cautela', 'CONTRAINDICADO', '2A', 'CPIC', 1),
('Sulfadiazina', 'Antibióticos (Sulfonamidas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente pode causar hemólise com sulfonamidas', 'Sem risco', 'Cautela', 'CONTRAINDICADO', '1A', 'CPIC', 1),
('Sufentanila', 'Analgésicos Opioides', 'OPRM1', 'rs1799971', 'target', 'OPRM1 modula afinidade do receptor opioide', 'Resposta padrão', 'Afinidade reduzida - dose maior', 'Dose significativamente maior', '2A', 'PharmGKB', 1),
('Sufentanila', 'Analgésicos Opioides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza sufentanila', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Siponimode', 'Moduladores do Receptor S1P', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza siponimode - FDA exige genotipagem', 'Dose padrão', 'Dose de manutenção 1mg (em vez de 2mg)', 'CONTRAINDICADO em *3/*3', '1A', 'FDA', 1),
('Siponimode', 'Moduladores do Receptor S1P', 'CYP2C9', 'rs1057910', 'substrate', 'CYP2C9*3 reduz metabolismo do siponimode', 'Dose padrão 2mg', 'Dose 1mg', 'CONTRAINDICADO', '1A', 'FDA', 1),
('Sinvastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1*5 reduz captação hepática - aumenta risco de miopatia', 'Dose até 80mg', 'Dose máxima 20mg (risco de miopatia)', 'Dose máxima 20mg ou usar alternativa', '1A', 'CPIC', 1),
('Sildenafila', 'Inibidores de Fosfodiesterases', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza sildenafila', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Sertralina', 'Antidepressivos (ISRS)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19*17 aumenta metabolismo da sertralina em 30-40%', 'Dose padrão', 'Níveis 30-40% menores - pode necessitar dose maior', 'Níveis muito reduzidos', '1A', 'CPIC', 1),
('Selegilina', 'Antidepressivos (IMAO-B)', 'CYP2B6', 'rs3211371', 'substrate', 'CYP2B6 metaboliza selegilina', 'Dose padrão', 'Metabolismo alterado', 'Monitorar', '3', 'PharmGKB', 1),
('Salmeterol', 'Antiasmáticos (Beta-2 agonistas)', 'ADRB2', 'rs1042713', 'target', 'ADRB2 Arg16Gly modula resposta a beta-2 agonistas de longa ação', 'Arg/Arg - melhor resposta', 'Arg/Gly - resposta intermediária', 'Gly/Gly - menor resposta ao uso regular', '2A', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. SUMATRIPTANO
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sumatriptano', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Imigran®, Sumatriptan®',
    'O sumatriptano é um agonista seletivo do receptor 5-HT1B/1D (triptano), utilizado para tratamento agudo de crises de enxaqueca. Atua causando vasoconstrição craniana e inibindo a liberação de neuropeptídeos. Efeitos adversos incluem sensação de aperto no peito, parestesias, tontura e fadiga. Contraindicado em doença cardiovascular.',
    'O gene GNB3 (cromossomo 12) modula a transdução de sinal em receptores serotoninérgicos. O genótipo CC (rs5443) indica resposta padrão a triptanos.',
    'rs5443', '12', 'GNB3', 'Europeia',
    'GNB3 rs5443 (C825T):\n- CC = Resposta padrão a triptanos\n- CT = Resposta variável\n- TT = Possivelmente melhor resposta',
    'GNB3 CC (normal). Resposta padrão esperada ao sumatriptano. Uso conforme indicação para enxaqueca.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. SULFASSALAZINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sulfassalazina', (SELECT id FROM pgx_drug_classes WHERE code='gota' LIMIT 1),
    'Azulfidine®, Azulfin®, Salazoprin®',
    'A sulfassalazina é um aminossalicilato utilizado para doença inflamatória intestinal (retocolite ulcerativa, doença de Crohn) e artrite reumatoide. Contém um componente sulfonamida que pode causar hemólise em pacientes com deficiência de G6PD. Efeitos adversos incluem náuseas, cefaleia, erupção cutânea e oligospermia reversível.',
    'O gene G6PD (cromossomo X) — genótipo TT (rs1050829) indica atividade NORMAL. Sem risco de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT (homens) = Normal\n- CC (homens) = Deficiente → Risco de hemólise',
    'G6PD normal (TT). Sulfassalazina pode ser usada com segurança do ponto de vista genético.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. SULFADIAZINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sulfadiazina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Suladrin®',
    'A sulfadiazina é um antibiótico sulfonamida utilizado para toxoplasmose (em combinação com pirimetamina), infecções urinárias e profilaxia de febre reumática. Pode causar hemólise em pacientes com deficiência de G6PD. Efeitos adversos incluem cristalúria, erupção cutânea, febre e, raramente, síndrome de Stevens-Johnson.',
    'G6PD normal (TT em rs1050829). Sem risco de hemólise por deficiência de G6PD.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT = Normal (sem deficiência)\n- Deficientes: risco de anemia hemolítica aguda',
    'G6PD normal. Sulfadiazina pode ser usada com segurança neste aspecto genético.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. SUFENTANILA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sufentanila', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Sufenta®, Fastfen®',
    'A sufentanila é um opioide sintético potente (5-10x mais potente que o fentanil), utilizada em anestesia geral, analgesia epidural/intratecal e dor pós-operatória. NÃO é pró-droga — atua diretamente no receptor opioide mu. É metabolizada pelo CYP3A4. Efeitos adversos incluem depressão respiratória, rigidez muscular, bradicardia e náuseas.',
    'OPRM1 (rs1799971): genótipo AA = receptor opioide mu NORMAL. A sufentanila deve ter resposta analgésica adequada. CYP3A4 (rs35599367 GG) = metabolismo NORMAL. Combinando com COMT AG (Val/Met), pode necessitar doses ligeiramente maiores que pacientes com COMT AA (Met/Met).',
    'rs1799971', '6', 'OPRM1', 'Europeia',
    'OPRM1 rs1799971 (A118G):\n- AA = Afinidade normal do receptor (resposta padrão)\n- AG = Afinidade reduzida (pode necessitar dose 20-30% maior)\n- GG = Afinidade muito reduzida\n\nCYP3A4 rs35599367: GG = Metabolismo normal\n\nCOMT rs4680: AG = Resposta intermediária a opioides',
    'RELEVANTE PARA CIRURGIA MAXILAR: OPRM1 AA = receptor normal. CYP3A4 normal. Sufentanila é opção SEGURA para anestesia/analgesia pós-operatória pois NÃO depende do CYP2D6 (que é desconhecido). COMT AG sugere necessidade possível de doses ligeiramente maiores.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. SIPONIMODE
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Siponimode', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Kiendra®, Mayzent®',
    'O siponimode é um modulador seletivo do receptor de esfingosina-1-fosfato (S1P1/S1P5), utilizado para esclerose múltipla secundária progressiva. É metabolizado pelo CYP2C9. A FDA EXIGE genotipagem do CYP2C9 antes de iniciar e CONTRAINDICA em pacientes *3/*3. Efeitos adversos incluem bradicardia (1ª dose), linfopenia, edema macular, hepatotoxicidade e infecções.',
    'O CYP2C9 metaboliza o siponimode. Genótipos CC (rs1799853/*2) e AA (rs1057910/*3) indicam metabolizador NORMAL (*1/*1). A dose padrão de manutenção (2mg) é apropriada.',
    'rs1799853', '10', 'CYP2C9', 'Europeia',
    'CYP2C9 (FDA exige teste antes de iniciar):\n- *1/*1 (CC + AA) = Normal → Dose 2mg/dia\n- *1/*2 (CT + AA) = Intermediário → Dose 2mg/dia\n- *1/*3 (CC + AC) = Intermediário → Dose 1mg/dia\n- *2/*3 (CT + AC) = Lento → Dose 1mg/dia\n- *3/*3 (CC + CC) = Muito lento → CONTRAINDICADO',
    'CYP2C9 *1/*1 (normal). Siponimode pode ser usado na dose padrão de 2mg/dia. Monitoramento cardíaco na primeira dose é obrigatório independente do genótipo.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. SINVASTATINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sinvastatina', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Zocor®, Sinvastacor®, Sinvthal®, Sinvalip®',
    'A sinvastatina é uma estatina (inibidor da HMG-CoA redutase) utilizada para redução do colesterol LDL e prevenção cardiovascular. É transportada para o fígado pelo SLCO1B1 (OATP1B1). Variantes que reduzem este transporte aumentam os níveis plasmáticos e o RISCO DE MIOPATIA/RABDOMIÓLISE. Efeitos adversos incluem mialgia, elevação de CPK, hepatotoxicidade e, raramente, rabdomiólise.',
    'O gene SLCO1B1 (cromossomo 12) codifica o transportador OATP1B1, que capta a sinvastatina do sangue para o fígado. O genótipo TC em rs4149056 (*5) indica FUNÇÃO REDUZIDA do transportador. Isso resulta em níveis plasmáticos AUMENTADOS de sinvastatina, com RISCO SIGNIFICATIVAMENTE MAIOR de miopatia (4.5x para TC, 17x para CC).',
    'rs4149056', '12', 'SLCO1B1',
    'Europeia (frequência *5: ~15% em europeus)',
    'SLCO1B1 rs4149056 (*5):\n- TT = Normal (transportador funcional) → Dose até 80mg\n- TC = Heterozigoto (transportador reduzido) → DOSE MÁXIMA 20mg (risco 4.5x miopatia)\n- CC = Homozigoto variante → DOSE MÁXIMA 20mg ou USAR ALTERNATIVA (risco 17x miopatia)\n\nAlternativas com menor risco: rosuvastatina, pravastatina (menos dependentes do SLCO1B1)',
    'ACHADO IMPORTANTE: SLCO1B1 TC (*1/*5) = transportador REDUZIDO. A dose de sinvastatina NÃO deve exceder 20mg/dia pelo risco aumentado de miopatia (4.5x). O CPIC recomenda considerar alternativas como rosuvastatina ou pravastatina, que são menos afetadas pelo SLCO1B1. Se usar sinvastatina, monitorar CPK e sintomas musculares.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. SILDENAFILA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sildenafila', (SELECT id FROM pgx_drug_classes WHERE code='pde_inhib' LIMIT 1),
    'Viagra®, Revatio®, Escitan®, Blupill®, Dejavu®, Directus®',
    'A sildenafila é um inibidor da fosfodiesterase tipo 5 (PDE5), utilizada para disfunção erétil e hipertensão arterial pulmonar (Revatio). É metabolizada pelo CYP3A4 (via principal) e CYP2C9 (via secundária). Efeitos adversos incluem cefaleia, rubor, dispepsia, congestão nasal, alterações visuais (visão azulada) e priapismo (raro).',
    'CYP3A4 (rs35599367 GG) = metabolismo NORMAL. A sildenafila é processada adequadamente.',
    'rs35599367', '7', 'CYP3A4', 'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal\n- GA/CT = Metabolismo reduzido (iniciar dose menor)\n- AA/TT = Metabolismo muito reduzido',
    'CYP3A4 normal. Dose padrão de sildenafila apropriada. Evitar uso com nitratos e inibidores potentes do CYP3A4.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. SERTRALINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Sertralina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Zoloft®, Assert®, Serenata®, Trasolin®',
    'A sertralina é um inibidor seletivo da recaptação de serotonina (ISRS), um dos antidepressivos mais prescritos. Indicada para depressão, TOC, transtorno do pânico, TEPT, fobia social e TDPM. É metabolizada pelo CYP2C19 (via principal), CYP2B6 e CYP3A4. O CPIC possui guideline específico para ajuste de dose baseado no CYP2C19. Efeitos adversos incluem náuseas, diarreia, insônia, disfunção sexual e síndrome serotoninérgica (rara).',
    'O CYP2C19 é a principal enzima de metabolização da sertralina. O genótipo CT em rs12248560 (*17) indica METABOLIZADOR RÁPIDO (*1/*17). O CPIC informa que metabolizadores rápidos têm níveis séricos de sertralina 30-40% MENORES que metabolizadores normais. Pode ser necessário dose maior ou considerar alternativa.',
    'rs12248560', '10', 'CYP2C19',
    'Europeia',
    'CYP2C19 e Sertralina (CPIC guideline):\n- *1/*1 (CC) = Normal → Dose padrão\n- *1/*17 (CT) = Metabolizador rápido → Níveis 30-40% menores. Considerar dose maior ou alternativa se sem resposta\n- *17/*17 (TT) = Ultra-rápido → Considerar alternativa (escitalopram ou fluoxetina)\n- *1/*2 ou *2/*2 = Lento → Reduzir dose 50%',
    'RELEVANTE PARA AUTISMO/TDAH: CYP2C19 *1/*17 (metabolizador rápido). Se sertralina for prescrita, os níveis podem ser 30-40% menores que o esperado. Se não houver resposta adequada na dose padrão, considerar: 1) Aumento da dose, 2) Alternativa como fluoxetina (menos dependente do CYP2C19). Monitorar resposta clínica cuidadosamente.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. SELEGILINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Selegilina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Emsam®, Jumex®, Carbex®',
    'A selegilina é um inibidor irreversível da monoamina oxidase B (IMAO-B), utilizada para doença de Parkinson (adjuvante) e depressão (patch transdérmico Emsam). É metabolizada pelo CYP2B6 em metanfetamina e anfetamina. Em doses baixas é seletiva para MAO-B; em doses altas perde seletividade. Efeitos adversos incluem insônia, náuseas, hipotensão ortostática e interações alimentares (tiramina) em doses altas.',
    'O CYP2B6 (cromossomo 19) metaboliza a selegilina. O SNP rs3211371 não está disponível no chip GSA v3.0. A evidência farmacogenética para selegilina é de nível baixo (3).',
    'rs3211371', '19', 'CYP2B6', 'Europeia',
    'CYP2B6 rs3211371:\n- SNP não disponível no chip\n\nNota: Evidência farmacogenética limitada (nível 3) para selegilina.',
    'CYP2B6 indeterminado (SNP não disponível). Evidência farmacogenética limitada. Usar dose padrão com monitoramento clínico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. SALMETEROL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Salmeterol', (SELECT id FROM pgx_drug_classes WHERE code='pneumo' LIMIT 1),
    'Serevent®',
    'O salmeterol é um beta-2 agonista de longa ação (LABA) utilizado para tratamento de asma e DPOC. Deve ser usado SEMPRE em combinação com corticoide inalatório na asma (nunca em monoterapia). Atua relaxando a musculatura brônquica por 12h. O gene ADRB2 modula a resposta ao uso regular. Efeitos adversos incluem tremor, taquicardia, cefaleia e hipocalemia.',
    'O gene ADRB2 (cromossomo 5) codifica o receptor beta-2 adrenérgico. O polimorfismo Arg16Gly (rs1042713) afeta a regulação do receptor com uso crônico. O genótipo GA (Arg/Gly) do paciente indica resposta INTERMEDIÁRIA ao salmeterol com uso regular — o receptor pode sofrer dessensibilização parcial.',
    'rs1042713', '5', 'ADRB2',
    'Europeia',
    'ADRB2 rs1042713 (Arg16Gly):\n- AA (Arg/Arg) = Melhor resposta ao uso regular de LABA\n- GA (Arg/Gly) = Resposta intermediária (dessensibilização parcial)\n- GG (Gly/Gly) = Menor resposta ao uso regular (dessensibilização). Considerar ajuste de terapia\n\nNota: Este efeito é mais relevante para uso REGULAR (crônico), não para resgate.',
    'ADRB2 GA (Arg/Gly) = resposta INTERMEDIÁRIA ao salmeterol com uso regular. Monitorar controle da asma. Se controle insuficiente com LABA, considerar ajuste de dose do corticoide inalatório ou adição de antileucotrieno. A Genera também identificou predisposição para menor resposta ao salbutamol e salmeterol com base neste mesmo marcador.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();


