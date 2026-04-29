-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 7 de N)
-- 10 medicamentos: Pitavastatina → Paclitaxel
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Pitavastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta pitavastatina - impacto moderado', 'Dose padrão', 'Níveis moderadamente aumentados', 'Monitorar CPK', '2A', 'CPIC', 1),
('Piroxicam', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza piroxicam - meia-vida muito longa', 'Dose padrão', 'Meia-vida prolongada - sangramento', 'Risco alto de sangramento GI', '1A', 'DPWG', 1),
('Pimozida', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza pimozida - risco QTc em lentos', 'Dose padrão com ECG', 'Monitorar QTc', 'CONTRAINDICADO se QTc basal alto', '1A', 'FDA', 1),
('Perindopril', 'Inibidores da ECA', 'AGTR1', 'rs5186', 'target', 'AGTR1 modula resposta ao sistema renina-angiotensina', 'Resposta padrão', 'Resposta variável', 'Pode necessitar dose maior', '2B', 'PharmGKB', 1),
('Perindopril', 'Inibidores da ECA', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco teórico com perindopril', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Perfenazina', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza perfenazina', 'Dose padrão', 'Monitorar EPS', 'Reduzir dose 50%', '1A', 'DPWG', 1),
('Paroxetina', 'Antidepressivos (ISRS)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza paroxetina', 'Dose padrão', 'Monitorar', 'Considerar alternativa', '1A', 'CPIC', 1),
('Paroxetina', 'Antidepressivos (ISRS)', 'HTR1A', 'rs6295', 'target', 'HTR1A C-1019G modula resposta a ISRS', 'Resposta normal', 'Resposta reduzida ~30%', 'Resposta muito reduzida', '2B', 'PharmGKB', 1),
('Paracetamol', 'Analgésicos', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - paracetamol pode causar hemólise em doses altas', 'Sem risco em doses terapêuticas', 'Cautela em doses altas', 'Monitorar', '3', 'PharmGKB', 1),
('Pantoprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza pantoprazol - moderadamente dependente', 'Dose padrão', 'Eficácia moderadamente reduzida', 'Considerar rabeprazol', '2A', 'CPIC', 1),
('Paliperidona', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula ganho de peso', 'Sem risco aumentado', 'Risco moderado', 'Risco alto de obesidade', '2B', 'PharmGKB', 1),
('Paliperidona', 'Antipsicóticos', 'HTR2C', 'rs3813929', 'risk', 'HTR2C modula ganho de peso com antipsicóticos', 'Risco padrão', 'Proteção parcial', 'Proteção', '2B', 'PharmGKB', 1),
('Paclitaxel', 'Antineoplásicos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza paclitaxel', 'Dose padrão', 'Metabolismo reduzido - toxicidade', 'Risco de toxicidade aumentada', '2A', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. PITAVASTATINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Pitavastatina', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Livalo®, Ebatz®, Lester®, Pivast®, SPivax®',
    'A pitavastatina é uma estatina sintética com perfil farmacogenético favorável — é menos dependente do CYP450 para metabolismo (não depende do CYP3A4). É parcialmente transportada pelo SLCO1B1. Efeitos adversos incluem mialgia (baixa incidência), elevação de transaminases e rabdomiólise (rara).',
    'SLCO1B1 (rs4149056 TC) tem impacto moderado na pitavastatina. Níveis podem estar levemente aumentados mas com menor risco de miopatia que sinvastatina.',
    'rs4149056', '12', 'SLCO1B1', 'Europeia',
    'SLCO1B1 rs4149056 para Pitavastatina:\n- TT = Normal\n- TC = Níveis moderadamente aumentados - monitorar\n- CC = Considerar dose menor',
    'SLCO1B1 TC. Pitavastatina tem perfil intermediário entre sinvastatina (mais afetada) e pravastatina (menos afetada). Monitorar CPK. É alternativa razoável, mas rosuvastatina ou pravastatina podem ser preferíveis.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. PIROXICAM
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Piroxicam', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Feldene®, Farmoxicam®, Flamostat®, Floxicam®, Pirfel®',
    'O piroxicam é um AINE do grupo dos oxicams com meia-vida extremamente longa (~50h). Utilizado para artrite reumatoide, osteoartrite e dor aguda. É metabolizado pelo CYP2C9. Em metabolizadores lentos, a meia-vida pode ser >100h com alto risco de sangramento GI. O DPWG recomenda evitar em metabolizadores lentos. Efeitos adversos incluem úlcera gástrica, sangramento GI, nefrotoxicidade e reações cutâneas.',
    'CYP2C9 (rs1799853 CC, rs1057910 AA) = *1/*1 = Metabolizador NORMAL. Piroxicam processado em meia-vida padrão (~50h).',
    'rs1799853', '10', 'CYP2C9', 'Europeia',
    'CYP2C9 rs1799853 (*2) + rs1057910 (*3):\n- *1/*1 (CC+AA) = Normal (meia-vida ~50h)\n- *1/*2 (CT+AA) = Intermediário (meia-vida prolongada)\n- *1/*3 ou *2/*2 = Lento → DPWG recomenda EVITAR piroxicam\n- *2/*3 ou *3/*3 = Muito lento → CONTRAINDICADO',
    'CYP2C9 *1/*1 (normal). Piroxicam pode ser usado em dose padrão. Monitorar sintomas GI e função renal conforme protocolo para AINEs de meia-vida longa.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. PIMOZIDA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Pimozida', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Orap®',
    'A pimozida é um antipsicótico típico (difenilbutilpiperidina) utilizado para síndrome de Tourette e psicoses. Tem alto risco de prolongamento do QTc e arritmias. É metabolizada pelo CYP2D6. A FDA exige ECG e limita dose em metabolizadores lentos. Efeitos adversos incluem prolongamento QTc, arritmias, sedação e efeitos extrapiramidais.',
    'CYP2D6 (rs3892097) N/D. A pimozida é um dos medicamentos com MAIOR risco de prolongamento QTc. Sem dados do CYP2D6, ECG rigoroso é obrigatório.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (dose até 10mg com ECG)\n- GA = Intermediário (dose máxima 6mg)\n- AA = Metabolizador lento → Dose máxima 4mg (FDA)',
    'CYP2D6 indeterminado. Pimozida tem ALTO RISCO de QTc prolongado. Se prescrita, ECG obrigatório antes e periodicamente. Sem dados do CYP2D6, tratar como metabolizador desconhecido — limitar dose e monitorar rigorosamente.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. PERINDOPRIL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Perindopril', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Acertanlo®, Acertil®, Triplixam®',
    'O perindopril é um inibidor da ECA utilizado para hipertensão, insuficiência cardíaca e prevenção cardiovascular. O AGTR1 modula resposta ao bloqueio do sistema renina-angiotensina. G6PD tem associação teórica. Efeitos adversos incluem tosse seca, hipotensão, hipercalemia e angioedema.',
    'AGTR1 (rs5186 AC) = heterozigoto para variante A1166C do receptor de angiotensina II tipo 1. Resposta VARIÁVEL a iECAs/BRAs. G6PD (TT) = normal.',
    'rs5186', '3', 'AGTR1', 'Europeia',
    'AGTR1 rs5186 (A1166C):\n- AA = Resposta padrão a iECAs\n- AC = Resposta variável (heterozigoto)\n- CC = Associado a hipertensão e maior resposta a BRAs\n\nG6PD: TT = Normal',
    'AGTR1 AC (heterozigoto). Resposta variável a iECAs. G6PD normal. Monitorar resposta pressórica e ajustar dose conforme necessário.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. PERFENAZINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Perfenazina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Eufor®, Trilafon®',
    'A perfenazina é um antipsicótico típico (fenotiazina) utilizado para esquizofrenia e náuseas/vômitos graves. É metabolizada pelo CYP2D6. O DPWG recomenda redução de dose em metabolizadores lentos pelo risco de efeitos extrapiramidais. Efeitos adversos incluem efeitos extrapiramidais, sedação, hipotensão e discinesia tardia.',
    'CYP2D6 (rs3892097) N/D. O DPWG recomenda redução de 50% em metabolizadores lentos.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário (monitorar EPS)\n- AA = Metabolizador lento (reduzir dose 50% - DPWG)',
    'CYP2D6 indeterminado. Se prescrita, iniciar dose baixa e monitorar efeitos extrapiramidais cuidadosamente.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. PAROXETINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Paroxetina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Paxil®, Aropax®, Pondera®, Roxetin®',
    'A paroxetina é um ISRS utilizado para depressão, TOC, ansiedade generalizada, pânico e fobia social. É metabolizada pelo CYP2D6 E é um INIBIDOR POTENTE do CYP2D6 (autoinibição). O gene HTR1A modula resposta a ISRS. IMPORTANTE: Paroxetina inibe o CYP2D6 — NÃO deve ser usada com tamoxifeno. Efeitos adversos incluem ganho de peso, disfunção sexual, síndrome de descontinuação (severa) e risco teratogênico.',
    'CYP2D6 N/D. HTR1A (rs6295 CG) = RESPOSTA REDUZIDA ~30% a ISRS. A combinação de CYP2D6 desconhecido + HTR1A CG sugere que a paroxetina pode ter eficácia subótima. FKBP5 (CC) = normal. ABCB1 N/D.',
    'rs6295', '5', 'HTR1A', 'Europeia',
    'CYP2D6 rs3892097: N/D\n\nHTR1A rs6295 (C-1019G):\n- CC = Resposta normal a ISRS\n- CG = Resposta REDUZIDA ~30% a ISRS\n- GG = Resposta MUITO reduzida a ISRS\n\nFKBP5 rs1360780: CC = Normal\nABCB1 rs1045642: N/D\nSLC6A4 rs25531: N/D',
    'RELEVANTE PARA AUTISMO: HTR1A CG = resposta a ISRS REDUZIDA em ~30%. Se paroxetina ou outro ISRS for prescrito, pode ser necessário dose maior ou troca por classe diferente (IRSN como duloxetina). A paroxetina especificamente tem problemas adicionais: ganho de peso, descontinuação severa, e inibe o CYP2D6 (impede uso com outros medicamentos). Considerar sertralina (com ajuste por CYP2C19 rápido) ou fluoxetina como alternativas.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. PARACETAMOL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Paracetamol', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Tylenol®, Dorixina®, Sonrisal®, Dorsanol®',
    'O paracetamol (acetaminofeno) é um analgésico/antipirético amplamente utilizado. Em doses terapêuticas é seguro. Em doses altas ou em pacientes com deficiência de G6PD, pode causar estresse oxidativo com hemólise. Hepatotoxicidade em superdosagem. Efeitos adversos são raros em doses terapêuticas; superdosagem causa necrose hepática.',
    'G6PD (rs1050829 TT) = NORMAL. Paracetamol seguro em doses terapêuticas. Risco de hemólise em G6PD deficientes é em doses ALTAS.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT = Normal (paracetamol seguro em doses terapêuticas)\n- Deficientes: cautela em doses altas (>2g/dia)',
    'RELEVANTE PARA CIRURGIA MAXILAR: G6PD normal. Paracetamol pode ser usado com segurança para analgesia pós-operatória em doses terapêuticas (máx 4g/dia em adultos, ajustar para peso em adolescentes). É primeira linha para dor leve-moderada e como adjuvante a opioides.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. PANTOPRAZOL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Pantoprazol', (SELECT id FROM pgx_drug_classes WHERE code='gastro' LIMIT 1),
    'Tecta®, Divena®, Inilok®, Restitue®',
    'O pantoprazol é um IBP moderadamente dependente do CYP2C19. Menos afetado que omeprazol mas mais que rabeprazol. Utilizado para DRGE, úlcera péptica e proteção gástrica. Efeitos adversos similares a outros IBPs.',
    'CYP2C19 *1/*17 (metabolizador rápido) reduz moderadamente a eficácia do pantoprazol. O rabeprazol é alternativa preferencial.',
    'rs12248560', '10', 'CYP2C19', 'Europeia',
    'CYP2C19 e Pantoprazol:\n- *1/*1 = Normal\n- *1/*17 = Eficácia moderadamente reduzida\n- *17/*17 = Considerar rabeprazol\n\nHierarquia de impacto CYP2C19: Omeprazol > Pantoprazol > Rabeprazol',
    'CYP2C19 *1/*17. Pantoprazol pode ter eficácia moderadamente reduzida. Se necessário IBP, RABEPRAZOL é preferencial. Se pantoprazol for escolhido, considerar dose maior (40mg 2x/dia em vez de 1x/dia para situações que exigem supressão ácida potente).',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. PALIPERIDONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Paliperidona', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Invega®, Invega Sustenna®, Invega Trinza®',
    'A paliperidona (9-OH-risperidona) é o metabólito ativo da risperidona, disponível como antipsicótico atípico. Vantagem: NÃO depende do CYP2D6 para metabolismo (já é o metabólito final). Indicada para esquizofrenia e transtorno esquizoafetivo. Efeitos adversos similares à risperidona: ganho de peso, hiperprolactinemia, sedação e efeitos extrapiramidais.',
    'A paliperidona NÃO depende do CYP2D6 (diferente da risperidona). MC4R (TT) = sem risco aumentado de obesidade. HTR2C (CC) = risco padrão de ganho de peso. É alternativa para quando o CYP2D6 é desconhecido.',
    'rs17782313', '18', 'MC4R', 'Europeia',
    'MC4R rs17782313: TT = Sem risco aumentado de obesidade\nHTR2C rs3813929: CC = Risco padrão de ganho de peso\n\nVantagem farmacogenética: NÃO depende do CYP2D6.',
    'RELEVANTE PARA AUTISMO: MC4R e HTR2C normais. A paliperidona é ALTERNATIVA PREFERENCIAL à risperidona quando o CYP2D6 é desconhecido, pois NÃO precisa de CYP2D6 para metabolismo (já é o metabólito ativo). Monitorar peso e prolactina.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. PACLITAXEL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Paclitaxel', (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Taxol®, Akssus®, Paclitax®, Ontax®, Parexel®',
    'O paclitaxel é um quimioterápico taxano utilizado para câncer de mama, ovário, pulmão e Kaposi. Atua estabilizando microtúbulos e impedindo a divisão celular. É metabolizado pelo CYP3A4 (via principal) e CYP2C8. Efeitos adversos incluem neutropenia, neuropatia periférica, alopecia, mialgia, reações de hipersensibilidade e mucosite.',
    'CYP3A4 (rs35599367 GG) = metabolismo NORMAL. Paclitaxel processado adequadamente.',
    'rs35599367', '7', 'CYP3A4', 'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG = Normal (metabolismo adequado do paclitaxel)\n- GA/CT = Metabolismo reduzido (risco de toxicidade aumentada)\n- AA/TT = Metabolismo muito reduzido',
    'CYP3A4 normal (GG). Paclitaxel metabolizado adequadamente. Dose padrão conforme protocolo oncológico. Monitorar neutropenia e neuropatia conforme padrão.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
