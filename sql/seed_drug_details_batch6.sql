-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 6 de N)
-- 10 medicamentos: Ranolazina → Pravastatina
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Ranolazina', 'Antianginosos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza ranolazina parcialmente', 'Dose padrão', 'Monitorar QTc', 'Cautela - risco QTc', '2B', 'PharmGKB', 1),
('Ramelteon', 'Hipnóticos (Agonista Melatonina)', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza ramelteon - ultra-rápidos têm efeito reduzido', 'Dose padrão', 'Metabolismo aumentado - efeito reduzido', 'Eficácia possivelmente reduzida', '2B', 'PharmGKB', 1),
('Rabeprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza rabeprazol - menos dependente que omeprazol', 'Dose padrão', 'Metabolismo levemente aumentado', 'Menos impacto que outros IBPs', '2A', 'CPIC', 1),
('Quinina', 'Antiparasitários (Antimaláricos)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - quinina pode causar hemólise', 'Sem risco', 'Cautela', 'Risco de hemólise', '2A', 'CPIC', 1),
('Quinapril', 'Inibidores da ECA', 'ACE', 'rs4343', 'target', 'ACE A2350G modula resposta a iECAs', 'Resposta padrão', 'Níveis de ACE intermediários', 'Níveis elevados de ACE - boa resposta a iECA', '2B', 'PharmGKB', 1),
('Quetiapina', 'Antipsicóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza quetiapina', 'Dose padrão', 'Metabolismo reduzido - sedação aumentada', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Quetiapina', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula ganho de peso com antipsicóticos', 'Sem risco aumentado', 'Risco moderado', 'Risco alto de obesidade', '2B', 'PharmGKB', 1),
('Protriptilina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza protriptilina', 'Dose padrão', 'Monitorar níveis', 'Reduzir dose 50%', '1A', 'CPIC', 1),
('Propafenona', 'Antiarrítmicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza propafenona - metabolizadores lentos têm mais efeito beta-bloqueador', 'Dose padrão', 'Efeito beta-bloqueador aumentado', 'Efeito beta-bloqueador significativo', '1A', 'DPWG', 1),
('Primaquina', 'Antiparasitários (Antimaláricos)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - primaquina causa hemólise grave', 'Sem risco', 'Teste quantitativo obrigatório', 'CONTRAINDICADO', '1A', 'CPIC/FDA', 1),
('Pravastatina', 'Estatinas', 'SLCO1B1', 'rs4149056', 'transporter', 'SLCO1B1 transporta pravastatina - impacto moderado', 'Dose padrão', 'Níveis levemente aumentados', 'Monitorar', '2A', 'CPIC', 1),
('Pravastatina', 'Estatinas', 'HMGCR', 'rs12916', 'target', 'HMGCR modula resposta a estatinas', 'Resposta padrão', 'Resposta modulada', 'Resposta variável', '3', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. RANOLAZINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ranolazina', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Riscard®',
    'A ranolazina é um antianginoso utilizado para angina crônica estável. Atua inibindo a corrente tardia de sódio, reduzindo a sobrecarga de cálcio intracelular. É metabolizada parcialmente pelo CYP2D6. Em metabolizadores lentos, pode haver prolongamento do QTc. Efeitos adversos incluem tontura, constipação, náuseas e prolongamento do QTc.',
    'CYP2D6 (rs3892097) N/D. A ranolazina pode prolongar o QTc em metabolizadores lentos do CYP2D6. Sem dados disponíveis para este marcador.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário (monitorar ECG)\n- AA = Metabolizador lento (risco QTc prolongado)',
    'CYP2D6 indeterminado. Se prescrita, monitorar ECG (QTc). Evitar associação com outros medicamentos que prolongam QTc.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. RAMELTEON
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ramelteon', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Rozerem®',
    'O ramelteon é um agonista seletivo dos receptores de melatonina (MT1/MT2), indicado para insônia de início do sono. NÃO causa dependência (diferente dos benzodiazepínicos). É metabolizado pelo CYP1A2 (via principal). Em metabolizadores ultra-rápidos, pode ter efeito reduzido. Efeitos adversos incluem sonolência, tontura e fadiga.',
    'CYP1A2 (rs762551 CA) = METABOLIZADOR ULTRA-RÁPIDO. O ramelteon pode ser eliminado mais rapidamente, com eficácia reduzida. Nota: a melatonina endógena e exógena também é metabolizada pelo CYP1A2 — este achado explica por que a Genera identificou predisposição para menor resposta à melatonina.',
    'rs762551', '15', 'CYP1A2', 'Europeia',
    'CYP1A2 rs762551 (*1F):\n- CC = Normal (ramelteon eficaz)\n- CA = Ultra-rápido (eficácia possivelmente reduzida)\n- AA = Ultra-rápido (eficácia provavelmente reduzida)\n\nNota: Mesmo mecanismo que afeta melatonina.',
    'CYP1A2 CA = ultra-rápido. Ramelteon e MELATONINA podem ter efeito REDUZIDO/MAIS CURTO. Isso explica o achado da Genera sobre melatonina. Se usar melatonina para insônia, considerar formulação de liberação prolongada ou doses maiores. Alternativas: trazodona (CYP3A4 normal), higiene do sono rigorosa.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. RABEPRAZOL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Rabeprazol', (SELECT id FROM pgx_drug_classes WHERE code='gastro' LIMIT 1),
    'Pariet®, Iniparet®',
    'O rabeprazol é um inibidor de bomba de prótons (IBP) utilizado para DRGE, úlcera péptica e erradicação do H. pylori. É o IBP MENOS dependente do CYP2C19 — é parcialmente metabolizado por via não-enzimática. Isso o torna alternativa preferencial para metabolizadores rápidos do CYP2C19. Efeitos adversos incluem cefaleia, diarreia e risco de deficiência de B12/magnésio a longo prazo.',
    'O CYP2C19 *1/*17 (metabolizador rápido) reduz a eficácia de IBPs. Porém, o rabeprazol é o IBP MENOS afetado pelo CYP2C19 por ter via alternativa de metabolismo.',
    'rs12248560', '10', 'CYP2C19', 'Europeia',
    'CYP2C19 e IBPs:\n- Omeprazol/Lansoprazol: MUITO dependentes → eficácia reduzida em *1/*17\n- Pantoprazol: Moderadamente dependente\n- Rabeprazol: MENOS dependente → PREFERENCIAL para metabolizadores rápidos\n- Esomeprazol: Moderadamente dependente',
    'CYP2C19 *1/*17 (metabolizador rápido). Para proteção gástrica, o RABEPRAZOL é a melhor escolha entre os IBPs por ser MENOS dependente do CYP2C19. Se usar omeprazol, considerar dose maior ou trocar por rabeprazol. RELEVANTE PARA CIRURGIA: Se precisar de IBP peri-operatório, preferir rabeprazol.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. QUININA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Quinina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Monotrean®, Qualaquin®, Quinamm®',
    'A quinina é um antimalárico alcaloide utilizado para malária falciparum (casos graves/resistentes) e câimbras noturnas (off-label). Pode causar hemólise em pacientes com deficiência de G6PD. Efeitos adversos incluem cinchonismo (tinido, cefaleia, náuseas), hipoglicemia, arritmias e trombocitopenia.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco aumentado de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT = Normal (sem deficiência)\n- Deficientes: risco de hemólise com quinina',
    'G6PD normal. Quinina pode ser usada sem risco de hemólise por deficiência de G6PD.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. QUINAPRIL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Quinapril', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Accupril®',
    'O quinapril é um inibidor da enzima conversora de angiotensina (iECA), utilizado para hipertensão e insuficiência cardíaca. Atua inibindo a conversão de angiotensina I em angiotensina II. O gene ACE modula os níveis basais da ECA e pode influenciar a resposta. Efeitos adversos incluem tosse seca, hipotensão, hipercalemia e angioedema (raro).',
    'ACE (rs4343 GG) = Níveis de ACE ELEVADOS. Paradoxalmente, isso indica que o paciente pode ter BOA RESPOSTA a iECAs, pois há mais enzima para ser inibida.',
    'rs4343', '17', 'ACE', 'Europeia',
    'ACE rs4343 (A2350G):\n- AA = Níveis normais de ACE\n- AG = Níveis intermediários\n- GG = Níveis elevados de ACE → potencialmente BOA resposta a iECAs',
    'ACE GG = níveis elevados. Paradoxalmente favorável para resposta a iECAs. Se prescrito, monitorar função renal e potássio conforme protocolo. Tosse seca é o efeito adverso mais comum dos iECAs.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. QUETIAPINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Quetiapina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Seroquel®, Quetros®, Neuroquel®, Quetiel®, Queropax®',
    'A quetiapina é um antipsicótico atípico utilizado para esquizofrenia, transtorno bipolar, depressão (adjuvante) e insônia (doses baixas). É metabolizada pelo CYP3A4. Em doses baixas (25-100mg) é frequentemente usada off-label como indutor do sono. MC4R modula ganho de peso. Efeitos adversos incluem sonolência, ganho de peso, hipotensão ortostática e síndrome metabólica.',
    'CYP3A4 (rs35599367 GG) = metabolismo NORMAL. Quetiapina processada adequadamente. MC4R (rs17782313 TT) = sem risco aumentado de obesidade.',
    'rs35599367', '7', 'CYP3A4', 'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG = Normal\n- GA/CT = Metabolismo reduzido (sedação aumentada)\n- AA/TT = Metabolismo muito reduzido\n\nMC4R rs17782313:\n- TT = Sem risco aumentado de obesidade\n- CT = Risco moderado\n- CC = Risco alto',
    'CYP3A4 normal + MC4R normal. Quetiapina pode ser usada em dose padrão. Sem predisposição genética aumentada para ganho de peso. Monitorar peso e perfil metabólico conforme protocolo. Útil em doses baixas para insônia em autistas.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. PROTRIPTILINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Protriptilina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Vivacor®, Vivactil®',
    'A protriptilina é um antidepressivo tricíclico com efeito estimulante (diferente da maioria dos tricíclicos que são sedativos). Indicada para depressão com fadiga. É metabolizada pelo CYP2D6. O CPIC recomenda redução de dose em metabolizadores lentos. Efeitos adversos incluem insônia, taquicardia, boca seca, retenção urinária e arritmias.',
    'CYP2D6 (rs3892097) N/D. O CPIC recomenda: dose padrão para normais, redução 50% para metabolizadores lentos.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (dose padrão)\n- GA = Intermediário (monitorar)\n- AA = Metabolizador lento (reduzir dose 50% - CPIC)',
    'CYP2D6 indeterminado. Se prescrita, iniciar dose baixa. Monitorar ECG (QTc) em tricíclicos.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. PROPAFENONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Propafenona', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Ritmonorm®, Brataq®, Fanorm®, Tuntá®',
    'A propafenona é um antiarrítmico classe IC utilizado para fibrilação atrial e taquicardias supraventriculares. É metabolizada pelo CYP2D6. Em metabolizadores lentos, acumula-se o composto original que tem efeito beta-bloqueador significativo. Efeitos adversos incluem bradicardia, hipotensão, distúrbios GI, tontura e pró-arritmia.',
    'CYP2D6 (rs3892097) N/D. Em metabolizadores lentos, a propafenona acumula com efeito beta-bloqueador significativo (bradicardia, hipotensão). O DPWG recomenda monitoramento em metabolizadores lentos.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (efeito IC predominante)\n- GA = Intermediário\n- AA = Metabolizador lento (acúmulo → efeito beta-bloqueador significativo)',
    'CYP2D6 indeterminado. Se prescrita, monitorar FC e PA (efeito beta-bloqueador pode se manifestar). ECG obrigatório.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. PRIMAQUINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Primaquina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Primaquina®',
    'A primaquina é um antimalárico aminoquinolina utilizado para prevenção de recaídas de malária vivax e ovale (elimina hipnozoítos hepáticos). A FDA e CPIC EXIGEM teste de G6PD antes da administração — em deficientes causa hemólise grave e potencialmente fatal. Efeitos adversos incluem anemia hemolítica (G6PD deficientes), metemoglobinemia, náuseas e dor abdominal.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco de hemólise por deficiência de G6PD.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829:\n- TT (homens) = Normal → Primaquina pode ser usada\n- CC (homens) = Deficiente → CONTRAINDICADO (hemólise fatal)\n- CT (mulheres) = Teste quantitativo obrigatório',
    'G6PD normal (TT). Primaquina pode ser usada com segurança. FDA/CPIC exige teste de G6PD antes mesmo em genótipo normal.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. PRAVASTATINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Pravastatina', (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Pravacol®, Colevacol®, Hipovastin®, Vastalia®',
    'A pravastatina é uma estatina hidrofílica utilizada para redução do colesterol LDL. É MENOS afetada pelo SLCO1B1 e NÃO depende do CYP3A4 para metabolismo (diferente de sinvastatina e atorvastatina). É uma das estatinas mais seguras farmacogeneticamente. HMGCR modula a resposta global a estatinas. Efeitos adversos incluem mialgia (baixa incidência), cefaleia e GI.',
    'SLCO1B1 (rs4149056 TC) tem impacto limitado na pravastatina (menor que sinvastatina). HMGCR (rs12916 TC) indica resposta MODULADA a estatinas. A pravastatina é ALTERNATIVA SEGURA à sinvastatina para Eric.',
    'rs4149056', '12', 'SLCO1B1',
    'Europeia',
    'SLCO1B1 rs4149056 para Pravastatina:\n- TT = Normal\n- TC = Impacto limitado (pravastatina é menos afetada)\n- CC = Monitorar\n\nHMGCR rs12916:\n- TT = Resposta padrão a estatinas\n- TC = Resposta modulada\n- CC = Resposta variável',
    'SLCO1B1 TC + HMGCR TC. A pravastatina é ALTERNATIVA SEGURA à sinvastatina junto com a rosuvastatina. É menos potente que rosuvastatina mas tem perfil de segurança excelente e menor dependência do SLCO1B1. Boa opção se rosuvastatina não for tolerada.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
