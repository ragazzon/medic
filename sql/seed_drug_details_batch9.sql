-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 9 de N)
-- 10 medicamentos: Nitrofurantoína → Mirabegrona
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Nitrofurantoína', 'Antibióticos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - nitrofurantoína causa hemólise', 'Sem risco', 'Cautela', 'CONTRAINDICADO', '1A', 'CPIC', 1),
('Nevirapina', 'Antivirais (HIV)', 'CYP2B6', 'rs3211371', 'substrate', 'CYP2B6 metaboliza nevirapina', 'Dose padrão', 'Metabolismo alterado', 'Monitorar níveis', '2A', 'PharmGKB', 1),
('Naproxeno', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza naproxeno', 'Dose padrão', 'Meia-vida prolongada', 'Risco de sangramento GI', '2A', 'DPWG', 1),
('Naltrexona', 'Antagonistas Opioides', 'OPRM1', 'rs1799971', 'target', 'OPRM1 modula eficácia da naltrexona em alcoolismo', 'Resposta padrão', 'Melhor resposta para alcoolismo', 'Melhor resposta para alcoolismo', '2A', 'PharmGKB', 1),
('Naloxona', 'Antagonistas Opioides (Emergência)', 'OPRM1', 'rs1799971', 'target', 'OPRM1 modula resposta à naloxona', 'Resposta padrão', 'Dose pode necessitar ajuste', 'Dose pode necessitar ajuste', '3', 'PharmGKB', 1),
('Moxifloxacina', 'Antibióticos (Fluoroquinolonas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Morfina', 'Analgésicos Opioides', 'COMT', 'rs4680', 'target', 'COMT modula percepção de dor e necessidade de morfina', 'Dose padrão', 'Pode necessitar dose maior', 'Dose significativamente maior', '2A', 'PharmGKB', 1),
('Morfina', 'Analgésicos Opioides', 'OPRM1', 'rs2952768', 'target', 'rs2952768 associado a dose de morfina pós-operatória (GWAS)', 'Dose padrão', 'Pode necessitar dose maior', 'Dose maior necessária', '3', 'GWAS', 1),
('Modafinila', 'Agentes Estimulantes de Vigília', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza parcialmente modafinila', 'Dose padrão', 'Monitorar', 'Cautela', '3', 'PharmGKB', 1),
('Mirtazapina', 'Antidepressivos (NaSSA)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 é via secundária da mirtazapina', 'Dose padrão', 'Monitorar sedação', 'Sedação aumentada', '2B', 'PharmGKB', 1),
('Mirtazapina', 'Antidepressivos (NaSSA)', 'FKBP5', 'rs1360780', 'target', 'FKBP5 modula resposta a antidepressivos', 'Resposta favorável', 'Eixo HPA desregulado', 'Resposta reduzida', '2B', 'PharmGKB', 1),
('Mirabegrona', 'Antiespasmódicos Urinários', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza mirabegrona', 'Dose padrão', 'Monitorar', 'Considerar dose menor', '2B', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. NITROFURANTOÍNA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Nitrofurantoína', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Macrodantina®, Nitrofen®, Trofurim®',
    'A nitrofurantoína é um antibiótico utilizado exclusivamente para infecções urinárias baixas (cistite). CONTRAINDICADA em deficientes de G6PD por causar hemólise. Efeitos adversos incluem náuseas, neuropatia periférica (uso prolongado) e fibrose pulmonar (rara).',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT = Normal → Nitrofurantoína segura\n- Deficientes → CONTRAINDICADO (hemólise)',
    'G6PD normal. Nitrofurantoína pode ser usada com segurança para ITU.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. NEVIRAPINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Nevirapina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Viramune®, Nevirax®',
    'A nevirapina é um antirretroviral ITRNN utilizado no tratamento do HIV. É metabolizada pelo CYP2B6. Em metabolizadores lentos, níveis mais altos com mais hepatotoxicidade e rash. Efeitos adversos incluem rash (pode ser grave - SJS), hepatotoxicidade e síndrome de reconstituição imune.',
    'CYP2B6 (rs3211371) N/D — SNP não disponível. ABCB1 (rs1045642) N/D.',
    'rs3211371', '19', 'CYP2B6', 'Europeia',
    'CYP2B6 rs3211371: N/D\nABCB1 rs1045642: N/D',
    'CYP2B6 e ABCB1 indeterminados. Se prescrita, monitorar hepatotoxicidade e rash nas primeiras semanas. Monitoramento de carga viral e CD4 conforme protocolo HIV.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. NAPROXENO
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Naproxeno', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Naprosyn®, Naprox®, Flanax®, Naxtri®',
    'O naproxeno é um AINE com meia-vida longa (~14h), utilizado para dor, artrite e dismenorreia. É metabolizado pelo CYP2C9. Em metabolizadores lentos, meia-vida prolongada com risco de sangramento. Efeitos adversos incluem úlcera gástrica, sangramento GI, nefrotoxicidade e risco cardiovascular.',
    'CYP2C9 (rs1799853 CC) = *1/*1 = Metabolizador NORMAL. Naproxeno processado adequadamente.',
    'rs1799853', '10', 'CYP2C9', 'Europeia',
    'CYP2C9 rs1799853 (*2):\n- CC = Normal\n- CT = Intermediário (meia-vida prolongada)\n- TT = Lento (risco de sangramento)',
    'CYP2C9 normal. Naproxeno em dose padrão. Pode ser usado como alternativa ao ibuprofeno para dor pós-operatória (meia-vida mais longa = menos tomadas/dia).',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. NALTREXONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Naltrexona', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Revia®, Contrave®, Uninaltrex®',
    'A naltrexona é um antagonista opioide utilizado para dependência de álcool e opioides. Bloqueia o receptor mu-opioide. Pacientes com alelo G no OPRM1 (rs1799971) tendem a ter MELHOR resposta para alcoolismo. Efeitos adversos incluem náuseas, cefaleia, hepatotoxicidade e precipitação de abstinência em dependentes de opioides.',
    'OPRM1 (rs1799971 AA) = receptor normal. Para ALCOOLISMO: genótipo AA indica resposta PADRÃO (não a resposta aumentada que portadores do alelo G teriam). Nota: naltrexona em baixa dose (LDN) é usada off-label em autismo/autoimune.',
    'rs1799971', '6', 'OPRM1', 'Europeia',
    'OPRM1 rs1799971 e Naltrexona:\n- AA = Resposta padrão para alcoolismo\n- AG = Melhor resposta para dependência de álcool\n- GG = Melhor resposta (mais benefício)',
    'OPRM1 AA = resposta padrão à naltrexona. Se usada para dependência de álcool, monitorar resposta. Se usada em dose baixa (LDN) para autismo/autoimune, a evidência é limitada mas o perfil de segurança é bom.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. NALOXONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Naloxona', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Narcan®',
    'A naloxona é um antagonista opioide de emergência utilizado para reverter overdose de opioides. Atua bloqueando competitivamente o receptor mu-opioide. Efeitos adversos incluem precipitação de abstinência aguda (em dependentes) e taquicardia.',
    'OPRM1 (rs1799971 AA) = receptor normal. Naloxona deve funcionar adequadamente como antídoto.',
    'rs1799971', '6', 'OPRM1', 'Europeia',
    'OPRM1 rs1799971:\n- AA = Receptor normal (naloxona eficaz como antídoto)\n- AG/GG = Pode necessitar dose maior de naloxona',
    'OPRM1 AA = receptor normal. Naloxona deve funcionar em dose padrão como antídoto de emergência.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. MOXIFLOXACINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Moxifloxacina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Avalox®, Hypomoxatyl®, Moflocil®, Moxof®, Praiva®',
    'A moxifloxacina é um antibiótico fluoroquinolona respiratória utilizado para pneumonia, sinusite e infecções cutâneas. Prolonga o QTc. Efeitos adversos incluem prolongamento QTc, tendinopatia, fotossensibilidade e neuropatia.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829: TT = Normal',
    'G6PD normal. Moxifloxacina pode ser usada. ATENÇÃO: prolonga QTc — evitar associação com outros medicamentos que prolongam QTc. Fluoroquinolonas restringidas em adolescentes.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. MORFINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Morfina', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Dimorf®, Dolo Moff®',
    'A morfina é o opioide de referência para dor intensa. NÃO é pró-droga — atua diretamente nos receptores opioides mu. NÃO depende do CYP2D6 para efeito (metabolizada por glucuronidação). O COMT e OPRM1 modulam a resposta. rs2952768 é um achado de GWAS associado a doses pós-operatórias. Efeitos adversos incluem constipação, náuseas, sedação, depressão respiratória e dependência.',
    'OPRM1 (rs1799971 AA) = receptor normal. COMT (rs4680 AG = Val/Met) = resposta INTERMEDIÁRIA a opioides (pode necessitar dose ligeiramente maior). rs2952768 (TC) = heterozigoto para variante associada a doses maiores de morfina pós-operatória (evidência nível 3/GWAS).',
    'rs4680', '22', 'COMT', 'Europeia',
    'COMT rs4680 (Val158Met) e Morfina:\n- AA (Met/Met) = Maior sensibilidade à dor, MELHOR resposta a morfina\n- AG (Val/Met) = Intermediário (pode necessitar dose um pouco maior)\n- GG (Val/Val) = Menor sensibilidade, pode necessitar dose significativamente maior\n\nOPRM1 rs1799971: AA = Receptor normal\nrs2952768: TC = Heterozigoto (GWAS - pode necessitar dose maior)',
    'RELEVANTE PARA CIRURGIA MAXILAR: Morfina é opção SEGURA (NÃO depende do CYP2D6). OPRM1 AA = receptor normal. COMT AG + rs2952768 TC = pode necessitar dose ligeiramente MAIOR que pacientes COMT AA. Monitorar resposta e titular dose. A morfina IV é padrão-ouro para analgesia pós-operatória.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. MODAFINILA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Modafinila', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Modavigil®, Stavigile®, Provigil®',
    'A modafinila é um agente promotor de vigília utilizado para narcolepsia, apneia do sono e sonolência excessiva. Também usada off-label para TDAH e fadiga. É parcialmente metabolizada pelo CYP2D6. Efeitos adversos incluem cefaleia, náuseas, insônia, ansiedade e, raramente, reações cutâneas graves (SJS).',
    'CYP2D6 (rs3892097) N/D. A dependência do CYP2D6 é parcial — a modafinila tem múltiplas vias metabólicas. Evidência farmacogenética limitada (nível 3).',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 e Modafinila:\n- Dependência parcial do CYP2D6\n- Múltiplas vias metabólicas (impacto menor)\n- Evidência nível 3',
    'RELEVANTE PARA TDAH/AUTISMO: CYP2D6 indeterminado mas impacto é limitado. Modafinila é usada off-label para TDAH com perfil de segurança razoável. Dose padrão provavelmente adequada. Monitorar resposta clínica.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. MIRTAZAPINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Mirtazapina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Remeron®, Menelat®, Razapina®',
    'A mirtazapina é um antidepressivo noradrenérgico e serotoninérgico específico (NaSSA), utilizado para depressão, ansiedade e insônia. Possui forte efeito sedativo em doses baixas (15mg) e estimula apetite. É metabolizada pelo CYP2D6 (via secundária), CYP1A2 e CYP3A4. FKBP5 modula resposta. Efeitos adversos incluem sedação, ganho de peso, boca seca e aumento de colesterol.',
    'CYP2D6 N/D (via secundária — impacto menor). FKBP5 (rs1360780 CC) = eixo HPA NORMAL — favorável para resposta a antidepressivos. CYP1A2 (CA = ultra-rápido) pode aumentar metabolismo parcialmente.',
    'rs1360780', '6', 'FKBP5', 'Europeia',
    'FKBP5 rs1360780:\n- CC = Normal (eixo HPA regulado - favorável)\n- CT = Eixo HPA desregulado\n- TT = Muito desregulado\n\nCYP2D6: N/D (via secundária)\nCYP1A2: CA (ultra-rápido - via parcial)',
    'RELEVANTE PARA AUTISMO/INSÔNIA: FKBP5 CC = favorável. Mirtazapina é opção para depressão + insônia + apetite reduzido em autistas. Forte efeito sedativo em 15mg (paradoxalmente MENOS sedativo em 30mg). CYP2D6 é via secundária — impacto menor que em tricíclicos. Monitorar peso.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. MIRABEGRONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Mirabegrona', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Myrbetriq®',
    'A mirabegrona é um agonista beta-3 adrenérgico utilizado para bexiga hiperativa. Diferente dos antimuscarínicos, não causa boca seca. É metabolizada pelo CYP2D6 e CYP3A4. Efeitos adversos incluem hipertensão, ITU, cefaleia e nasofaringite.',
    'CYP2D6 (rs3892097) N/D. A mirabegrona também é metabolizada pelo CYP3A4 (GG = normal). Em metabolizadores lentos do CYP2D6, pode haver acúmulo moderado.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097:\n- N/D\n\nCYP3A4 rs35599367: GG = Normal (via alternativa funcional)',
    'CYP2D6 indeterminado mas CYP3A4 normal (via alternativa). Mirabegrona provavelmente adequada em dose padrão. Monitorar PA.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
