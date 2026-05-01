-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 24)
-- Medicamentos faltantes da Genera: Metformina, Nicotina, Aspirina, Bisfosfonatos
-- =============================================

SET NAMES utf8mb4;

-- PARTE A: pgx_drug_genes (faz aparecer no dashboard)
INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Metformina', 'Antidiabéticos', 'ATM', 'rs11212617', 'response', 'ATM rs11212617 associado à resposta ao tratamento com metformina', 'Menor resposta (genótipo AA)', 'Maior resposta ao tratamento', 'Maior resposta ao tratamento', '2B', 'PharmGKB', 1),
('Nicotina (reposição)', 'Terapia antitabagismo', 'COMT', 'rs4680', 'response', 'COMT Val158Met associado à eficácia da reposição de nicotina', 'Sem predisposição para parar com reposição', 'Sem predisposição para parar com reposição', 'Predisposição para abandonar hábito', '2B', 'PharmGKB', 1),
('Ácido Acetilsalicílico (Aspirina)', 'Anti-inflamatórios / Antiagregantes', 'LTC4S', 'rs730012', 'adverse', 'LTC4S rs730012 associado a urticária com AAS', 'Sem predisposição para urticária', 'Predisposição para urticária', 'Predisposição para urticária', '2B', 'PharmGKB', 1),
('Bisfosfonatos', 'Medicamentos para ossos', 'FDPS', 'rs2297480', 'response', 'FDPS rs2297480 associado à resposta ao tratamento com bisfosfonatos', 'Maior resposta ao tratamento (genótipo TT)', 'Maior resposta ao tratamento', 'Menor resposta ao tratamento (genótipo GG)', '2B', 'PharmGKB', 1);

-- PARTE B: pgx_drug_details (textos para página de detalhe)
INSERT INTO `pgx_drug_details`
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Metformina',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Glifage®, Formyn®, Glicefor®, Glicomet®, Metformed®',
    'A metformina é um medicamento usado no tratamento da diabetes tipo 2, melhorando os níveis de açúcar no sangue em jejum e diminuindo a produção de glicose pelo fígado. Também é usada no tratamento da síndrome do ovário policístico.',
    'O gene ATM (cromossomo 11) apresenta o marcador rs11212617 que está relacionado à resposta ao tratamento com metformina. Pessoas com o alelo C respondem melhor ao tratamento. Eric possui genótipo AA (sem o alelo C), indicando predisposição para menor resposta.',
    'rs11212617',
    '11',
    'ATM',
    'Europeia e norte-americana',
    '{"AA": "Menor resposta ao tratamento", "AC": "Maior resposta ao tratamento", "CC": "Maior resposta ao tratamento"}',
    'Se algum dia precisar de metformina, pode ser necessário ajuste de dose ou combinação com outros medicamentos. Eric NÃO tem diabetes - informação preventiva.',
    'Este é um teste de triagem e não diagnóstico. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE description=VALUES(description), understanding_result=VALUES(understanding_result);

INSERT INTO `pgx_drug_details`
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Nicotina (reposição)',
    (SELECT id FROM pgx_drug_classes WHERE code='neuro' LIMIT 1),
    'Adesivos de nicotina, Gomas de nicotina, NiQuitin®, Nicorette®',
    'A nicotina é a substância viciante do cigarro. A terapia de reposição de nicotina (adesivos e gomas) reduz gradualmente a dependência, ajudando a parar de fumar.',
    'O gene COMT (cromossomo 22) com o marcador rs4680 (Val158Met) está relacionado à eficiência da reposição de nicotina. Pessoas com genótipo AA respondem melhor. Eric possui AG (heterozigoto) - sem predisposição especial. Este é o MESMO gene que afeta a resposta a opioides.',
    'rs4680',
    '22',
    'COMT',
    'Asiática, europeia e norte-americana',
    '{"AA": "Predisposição para abandonar o hábito", "AG": "Sem predisposição especial", "GG": "Sem predisposição especial"}',
    'Eric NÃO fuma - informação preventiva. Se precisar parar de fumar no futuro, outras opções (vareniclina, bupropiona) podem ser mais eficazes.',
    'Este é um teste de triagem e não diagnóstico. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE description=VALUES(description), understanding_result=VALUES(understanding_result);

INSERT INTO `pgx_drug_details`
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ácido Acetilsalicílico (Aspirina)',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Aspirina®, Coristina D®, Doril®, Engov®, Melhoral®, Sonrisal®',
    'O ácido acetilsalicílico (AAS) é usado para tratar dor, febre, inflamações e prevenir coágulos no sangue. Está presente em muitos medicamentos conhecidos.',
    'O gene LTC4S (cromossomo 5) produz uma enzima envolvida em reações alérgicas. O marcador rs730012 está associado ao risco de urticária (coceira, vergões na pele) com uso de aspirina. Eric possui AA (sem o alelo C de risco) = pode usar com segurança.',
    'rs730012',
    '5',
    'LTC4S',
    'Americana e europeia',
    '{"AA": "Sem predisposição para urticária", "AC": "Predisposição para urticária", "CC": "Predisposição para urticária"}',
    'Pode usar aspirina e medicamentos com AAS sem risco aumentado de alergia na pele por esta via genética.',
    'Este é um teste de triagem e não diagnóstico. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE description=VALUES(description), understanding_result=VALUES(understanding_result);

INSERT INTO `pgx_drug_details`
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Bisfosfonatos',
    (SELECT id FROM pgx_drug_classes WHERE code='musculo' LIMIT 1),
    'Alendronato de sódio, Risedronato sódico, Raloxifeno (Evista®)',
    'Os bisfosfonatos são medicamentos para tratar fragilidade nos ossos (osteoporose) e alguns tipos de câncer ósseo. Funcionam aumentando a massa óssea e diminuindo o risco de fraturas.',
    'O gene FDPS (cromossomo 1) produz uma enzima relacionada à reabsorção óssea. O marcador rs2297480 está associado à resposta ao tratamento. Eric possui TT (sem o alelo G de risco) = responde BEM ao tratamento.',
    'rs2297480',
    '1',
    'FDPS',
    'Europeia',
    '{"GG": "Menor resposta ao tratamento", "GT": "Maior resposta ao tratamento", "TT": "Maior resposta ao tratamento"}',
    'Se algum dia precisar de bisfosfonatos para fortalecer os ossos, teria boa resposta. Irrelevante para adolescente saudável.',
    'Este é um teste de triagem e não diagnóstico. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE description=VALUES(description), understanding_result=VALUES(understanding_result);