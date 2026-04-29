-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 1 de N)
-- Usa INSERT ... ON DUPLICATE KEY UPDATE para não perder dados existentes
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- Primeiro, garantir que existe um índice único no drug_name para o ON DUPLICATE KEY funcionar
-- (roda sem erro se já existe)
ALTER TABLE `pgx_drug_details` ADD UNIQUE INDEX IF NOT EXISTS `uk_drug_name` (`drug_name`(191));

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes (faz aparecer no dashboard)
-- Sem isso, o medicamento NÃO aparece na análise genética
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Venlafaxina', 'Antidepressivos (IRSN)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza venlafaxina em desvenlafaxina', 'Dose padrão', 'Monitorar resposta', 'Considerar desvenlafaxina direta', '1A', 'CPIC', 1),
('Venlafaxina', 'Antidepressivos (IRSN)', 'FKBP5', 'rs1360780', 'target', 'FKBP5 regula eixo HPA e resposta a antidepressivos', 'Resposta favorável', 'Eixo HPA desregulado', 'Eixo HPA muito desregulado', '2B', 'DPWG', 1),
('Voriconazol', 'Antifúngicos', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 metaboliza voriconazol - *17 aumenta eliminação', 'Dose padrão', 'Metabolismo rápido - monitorar níveis', 'Ultra-rápido - níveis subterapêuticos', '1A', 'CPIC', 1),
('Voriconazol', 'Antifúngicos', 'CYP2C19', 'rs4244285', 'substrate', 'CYP2C19*2 reduz metabolismo do voriconazol', 'Dose padrão', 'Metabolismo reduzido', 'Risco de toxicidade', '1A', 'CPIC', 1),
('Vincristina', 'Antineoplásicos', 'ABCB1', 'rs1045642', 'transporter', 'P-gp transporta vincristina - variante aumenta neurotoxicidade', 'Efluxo normal', 'P-gp intermediária', 'Risco de neurotoxicidade aumentada', '2A', 'PharmGKB', 1),
('Vilazodona', 'Antidepressivos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza vilazodona', 'Dose padrão', 'Metabolismo reduzido - monitorar', 'Risco de acúmulo', '2A', 'CPIC', 1),
('Vortioxetina', 'Antidepressivos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 é a principal via de metabolização', 'Dose padrão', 'Monitorar resposta', 'Reduzir dose 50%', '1A', 'CPIC', 1),
('Ziprasidona', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula risco de obesidade com antipsicóticos', 'Sem risco aumentado', 'Risco moderado de obesidade', 'Risco alto de obesidade', '2B', 'PharmGKB', 1),
('Ziprasidona', 'Antipsicóticos', 'HTR2C', 'rs3813929', 'risk', 'HTR2C modula ganho de peso com antipsicóticos', 'Risco padrão de ganho de peso', 'Proteção parcial', 'Proteção contra ganho de peso', '2B', 'PharmGKB', 1),
('Zolpidem', 'Hipnóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza zolpidem', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'CPIC', 1),
('Zopiclona', 'Hipnóticos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza zopiclona', 'Dose padrão', 'Metabolismo reduzido', 'Risco de acúmulo', '2A', 'CPIC', 1),
('Zuclopentixol', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza zuclopentixol', 'Dose padrão', 'Risco moderado de efeitos colaterais', 'Risco alto - reduzir dose', '1A', 'DPWG', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos (textos para a página de detalhe)
-- =============================================

-- =============================================
-- 1. VARFARINA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Varfarina',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Marevan®, Marfarin®, Coumadin®',
    'A varfarina é um anticoagulante oral utilizado para prevenir e tratar tromboses venosas profundas, embolias pulmonares e para prevenção de acidente vascular cerebral em pacientes com fibrilação atrial ou próteses valvares cardíacas. Atua inibindo a vitamina K epóxido redutase (VKORC1), essencial para a ativação dos fatores de coagulação II, VII, IX e X. Efeitos adversos incluem sangramento, hematomas e necrose cutânea (rara).',
    'O gene VKORC1 (cromossomo 16) codifica a proteína-alvo da varfarina. O genótipo TT no marcador rs9923231 indica MUITA SENSIBILIDADE à varfarina — pessoas com este genótipo necessitam de doses significativamente menores (25-50% da dose padrão) para atingir o efeito anticoagulante desejado. O CYP2C9 processa a varfarina no fígado, e o CYP4F2 afeta o metabolismo da vitamina K.',
    'rs9923231',
    '16',
    'VKORC1',
    'Europeia',
    'VKORC1 rs9923231:\n- GG = Normal (dose padrão)\n- GA = Sensível (reduzir dose 25%)\n- AA/TT = Muito sensível (reduzir dose 25-50%)\n\nCYP2C9 rs1799853 (*2):\n- CC = Normal\n- CT = Intermediário (reduzir dose)\n- TT = Lento (reduzir dose significativamente)\n\nCYP4F2 rs2108622 (*3):\n- CC = Normal\n- CT = Metabolismo vitamina K reduzido\n- TT = Muito reduzido (pode necessitar dose maior)',
    'O paciente apresenta genótipo TT no VKORC1, indicando sensibilidade muito aumentada à varfarina. Se este medicamento for prescrito, a dose inicial deve ser significativamente reduzida (25-50% menor que a dose padrão). Monitoramento frequente do INR é essencial. Considerar anticoagulantes diretos (DOACs) como alternativa. O CYP4F2 heterozigoto (CT) pode compensar parcialmente.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 2. VENLAFAXINA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Venlafaxina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Efexor XR®, Venlift OD®, Alenthus XR®, Venfax®',
    'A venlafaxina é um antidepressivo inibidor da recaptação de serotonina e noradrenalina (IRSN), utilizado no tratamento de depressão maior, transtorno de ansiedade generalizada, fobia social e transtorno do pânico. É metabolizada principalmente pelo CYP2D6 no fígado em seu metabólito ativo (O-desmetilvenlafaxina/desvenlafaxina). Efeitos adversos incluem náuseas, tontura, insônia, sudorese, hipertensão e síndrome de descontinuação.',
    'O CYP2D6 é a principal enzima de metabolização da venlafaxina. Infelizmente, o principal marcador (*4, rs3892097) não está disponível no chip GSA v3.0 da Genera. O gene FKBP5 (rs1360780) regula o eixo hipotálamo-hipófise-adrenal (HPA), relacionado à resposta ao estresse. O genótipo CC indica regulação normal, favorável para resposta a antidepressivos.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário\n- AA = Metabolizador nulo (sem função)\n\nFKBP5 rs1360780:\n- CC = Normal (eixo HPA regulado)\n- CT = Eixo HPA desregulado\n- TT = Eixo HPA muito desregulado',
    'O SNP principal do CYP2D6 (*4) não está disponível no chip utilizado. O resultado favorável do FKBP5 (CC) sugere boa regulação do eixo de estresse. Para informação completa sobre CYP2D6, considerar teste farmacogenético específico (painel CYP2D6 completo com CNV).',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 3. VORICONAZOL
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Voriconazol',
    (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Vfend®, Veac®, Vori®, Velenaxol®',
    'O voriconazol é um antifúngico triazólico de amplo espectro utilizado no tratamento de infecções fúngicas invasivas graves, incluindo aspergilose invasiva, candidemia e infecções por Scedosporium e Fusarium. É metabolizado principalmente pelo CYP2C19 no fígado. Efeitos adversos incluem distúrbios visuais (fotopsia), hepatotoxicidade, reações cutâneas e neurotoxicidade.',
    'O gene CYP2C19 (cromossomo 10) é a principal enzima responsável pelo metabolismo do voriconazol. O genótipo CT em rs12248560 (*17) indica que o paciente carrega UMA CÓPIA do alelo de ganho de função, classificando-o como METABOLIZADOR RÁPIDO (*1/*17). Ele metaboliza o voriconazol mais rapidamente que o normal, o que pode resultar em níveis plasmáticos 30-40% menores que o esperado.',
    'rs12248560',
    '10',
    'CYP2C19',
    'Europeia (frequência *17: ~21% em europeus)',
    'CYP2C19 rs12248560 (*17):\n- CC = Normal (*1/*1)\n- CT = Metabolizador rápido (*1/*17)\n- TT = Metabolizador ultra-rápido (*17/*17)\n\nCYP2C19 rs4244285 (*2):\n- GG = Normal\n- GA = Intermediário (perda parcial)\n- AA = Lento (perda total)',
    'O paciente é metabolizador rápido do CYP2C19 (*1/*17). O voriconazol pode ser eliminado mais rapidamente, resultando em níveis subterapêuticos. Monitoramento dos níveis séricos (TDM) é fortemente recomendado. Esta mesma característica afeta: omeprazol, clopidogrel, escitalopram e sertralina.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 4. VINCRISTINA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Vincristina',
    (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Tecnocris®, Dabaz®, Fauldvincri®, Vincizina®',
    'A vincristina é um alcaloide da vinca utilizado no tratamento de diversos tipos de câncer, incluindo leucemias linfoblásticas agudas, linfomas de Hodgkin e não-Hodgkin, neuroblastoma e tumor de Wilms. Atua inibindo a formação dos microtúbulos durante a divisão celular. O principal efeito adverso é a neurotoxicidade (neuropatia periférica). Outros efeitos incluem constipação, alopecia e mielossupressão leve.',
    'O gene ABCB1 (cromossomo 7) codifica a glicoproteína-P (P-gp), uma proteína transportadora que expulsa substâncias das células. A variante C3435T (rs1045642) afeta a expressão desta proteína. Infelizmente, este SNP não está disponível no chip GSA v3.0 utilizado pela Genera.',
    'rs1045642',
    '7',
    'ABCB1',
    'Europeia',
    'ABCB1 rs1045642 (C3435T):\n- CC/AA = P-gp normal (efluxo normal)\n- CT/AG = P-gp intermediária\n- TT/GG = P-gp reduzida (risco aumentado de neurotoxicidade)',
    'Não é possível avaliar o risco de neurotoxicidade aumentada à vincristina sem os dados do ABCB1 (não disponível no chip). Se necessário, monitoramento clínico neurológico rigoroso. Para informação completa, considerar teste farmacogenético específico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 5. VILAZODONA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Vilazodona',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Viibryd®',
    'A vilazodona é um antidepressivo com mecanismo dual: inibidor seletivo da recaptação de serotonina (ISRS) e agonista parcial do receptor 5-HT1A. Indicada para transtorno depressivo maior. É metabolizada principalmente pelo CYP3A4 no fígado. Efeitos adversos incluem diarreia, náuseas, tontura, insônia e boca seca.',
    'O gene CYP3A4 (cromossomo 7) é a enzima mais abundante do sistema CYP450, responsável pelo metabolismo de ~50% dos medicamentos. O genótipo GG no rs35599367 (*22) é o genótipo de referência (normal), indicando que a enzima CYP3A4 funciona adequadamente.',
    'rs35599367',
    '7',
    'CYP3A4',
    'Europeia (frequência *22: ~5% em europeus)',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal\n- GA/CT = Metabolismo reduzido\n- AA/TT = Metabolismo muito reduzido',
    'Metabolismo normal pelo CYP3A4. A vilazodona deve ser processada em velocidade adequada. Dose padrão é apropriada.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 6. VORTIOXETINA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Vortioxetina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Brintellix®, Trintellix®',
    'A vortioxetina é um antidepressivo multimodal com ação sobre múltiplos receptores serotoninérgicos, indicado para transtorno depressivo maior. Apresenta benefícios cognitivos adicionais (memória, concentração). É metabolizada principalmente pelo CYP2D6 no fígado. Efeitos adversos incluem náuseas (principal), tontura, diarreia e prurido.',
    'O CYP2D6 é a principal enzima de metabolização da vortioxetina. O CPIC recomenda redução de dose em metabolizadores lentos (50% da dose) e permite aumento em ultra-rápidos. O principal marcador (*4, rs3892097) não está disponível no chip GSA v3.0 da Genera.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Metabolizador intermediário\n- AA = Metabolizador nulo (sem função) → reduzir dose 50%',
    'Sem dados completos do CYP2D6, recomenda-se iniciar com dose padrão e monitorar resposta clínica. Se efeitos colaterais excessivos ou falta de resposta, considerar teste farmacogenético específico para CYP2D6.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 7. ZIPRASIDONA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ziprasidona',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Geodon®, Zisid®, Zipras®',
    'A ziprasidona é um antipsicótico atípico (segunda geração) utilizado no tratamento de esquizofrenia e transtorno bipolar. Atua como antagonista dos receptores D2, 5-HT2A e 5-HT2C. É um dos antipsicóticos com menor risco de ganho de peso. Efeitos adversos incluem sonolência, tontura, prolongamento do QTc, acatisia e sintomas extrapiramidais.',
    'O gene MC4R (cromossomo 18) codifica o receptor de melanocortina 4, envolvido na regulação do apetite e peso. O genótipo TT (referência) indica sem risco aumentado de obesidade. O HTR2C (cromossomo X) codifica o receptor de serotonina 2C — a variante -759C/T está associada à proteção contra ganho de peso. O genótipo CC indica risco padrão.',
    'rs17782313',
    '18',
    'MC4R',
    'Europeia',
    'MC4R rs17782313:\n- TT = Normal (sem risco aumentado de obesidade)\n- CT = Risco moderado de obesidade\n- CC = Risco alto de obesidade\n\nHTR2C rs3813929 (-759C/T):\n- CC = Risco padrão de ganho de peso\n- CT = Proteção parcial contra ganho de peso\n- TT = Proteção contra ganho de peso',
    'O paciente não apresenta predisposição genética aumentada para ganho de peso com antipsicóticos (MC4R TT, HTR2C CC). A ziprasidona já possui menor risco metabólico. Monitorar peso e perfil metabólico conforme protocolo padrão.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 8. ZOLPIDEM
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Zolpidem',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Stilnox®, Hizolp®, Insonox®, Insopidem®, Lebazi®, Sonotrat®',
    'O zolpidem é um hipnótico não-benzodiazepínico (imidazopiridina) para tratamento de curto prazo da insônia. Atua como agonista seletivo do receptor GABA-A (subunidade alfa-1). É metabolizado pelo CYP3A4 no fígado. Efeitos adversos incluem sonolência residual, tontura, cefaleia, amnésia anterógrada e comportamentos complexos durante o sono.',
    'O CYP3A4 (cromossomo 7) metaboliza o zolpidem. O genótipo GG no rs35599367 (*22) é normal — a enzima funciona adequadamente e o zolpidem é processado em velocidade padrão.',
    'rs35599367',
    '7',
    'CYP3A4',
    'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal (metabolismo adequado)\n- GA/CT = Metabolismo reduzido (risco de acúmulo)\n- AA/TT = Metabolismo muito reduzido',
    'Metabolismo normal pelo CYP3A4. Dose padrão apropriada. Nota: para adolescentes (16 anos), hipnóticos geralmente não são recomendados sem avaliação rigorosa.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 9. ZOPICLONA
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Zopiclona',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Imovane®',
    'A zopiclona é um hipnótico da classe das ciclopirrolonas para tratamento de curto prazo da insônia. Atua como agonista do receptor GABA-A. É metabolizada principalmente pelo CYP3A4 no fígado. Efeitos adversos incluem gosto metálico (disgeusia), sonolência, boca seca e tontura.',
    'Mesma enzima do zolpidem. O CYP3A4 funcional com genótipo de referência (GG) indica metabolismo normal da zopiclona.',
    'rs35599367',
    '7',
    'CYP3A4',
    'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal\n- GA/CT = Metabolismo reduzido\n- AA/TT = Metabolismo muito reduzido',
    'Metabolismo normal pelo CYP3A4. Zopiclona seria processada adequadamente. Mesma observação sobre faixa etária.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();

-- =============================================
-- 10. ZUCLOPENTIXOL
-- =============================================
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Zuclopentixol',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Clopixol®, Cisordinol®',
    'O zuclopentixol é um antipsicótico típico (primeira geração) do grupo dos tioxantenos, para tratamento de esquizofrenia e transtornos psicóticos. Existe nas formas oral, depot e acetato IM. É metabolizado principalmente pelo CYP2D6 no fígado. Efeitos adversos incluem sintomas extrapiramidais, sedação, hipotensão ortostática e ganho de peso.',
    'O CYP2D6 é a principal enzima de metabolização do zuclopentixol. Metabolizadores lentos podem ter concentrações elevadas com mais efeitos colaterais. O marcador principal (*4, rs3892097) não está disponível no chip GSA v3.0 da Genera.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Metabolizador intermediário (risco moderado de efeitos colaterais)\n- AA = Metabolizador nulo (risco alto de efeitos colaterais)',
    'Sem dados completos do CYP2D6. Se prescrito, iniciar com dose baixa e titular gradualmente, monitorando efeitos extrapiramidais. Para informação completa, considerar teste farmacogenético específico para CYP2D6.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.',
    1
) ON DUPLICATE KEY UPDATE
    class_id = VALUES(class_id),
    commercial_names = VALUES(commercial_names),
    description = VALUES(description),
    understanding_result = VALUES(understanding_result),
    snp_rsid = VALUES(snp_rsid),
    chromosome = VALUES(chromosome),
    gene_symbol = VALUES(gene_symbol),
    study_population = VALUES(study_population),
    genotype_results = VALUES(genotype_results),
    suggestions = VALUES(suggestions),
    disclaimer = VALUES(disclaimer),
    updated_at = NOW();
