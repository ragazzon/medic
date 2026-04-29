-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 3 de N)
-- 10 medicamentos: Tioguanina → Tacrolimo
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes (faz aparecer no dashboard)
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Tioguanina', 'Antineoplásicos', 'TPMT', 'rs1800460', 'substrate', 'TPMT metaboliza tioguanina - deficiência causa mielossupressão grave', 'Dose padrão', 'Reduzir dose 30-50%', 'Reduzir dose drasticamente ou contraindicar', '1A', 'CPIC', 1),
('Tioguanina', 'Antineoplásicos', 'TPMT', 'rs1800462', 'substrate', 'TPMT*2 - atividade reduzida', 'Dose padrão', 'Reduzir dose', 'Mielossupressão grave', '1A', 'CPIC', 1),
('Ticagrelor', 'Anticoagulantes', 'CYP2C19', 'rs12248560', 'target', 'CYP2C19 NÃO afeta ticagrelor (diferente do clopidogrel)', 'Dose padrão - sem impacto', 'Dose padrão - sem impacto', 'Dose padrão - sem impacto', '1A', 'CPIC', 1),
('THC (Tetrahidrocanabinol)', 'Canabinoides', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza THC - metabolizadores lentos têm efeitos prolongados', 'Metabolismo normal', 'Efeitos mais prolongados', 'Efeitos muito prolongados', '2B', 'PharmGKB', 1),
('THC (Tetrahidrocanabinol)', 'Canabinoides', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 é via secundária de metabolização do THC', 'Normal', 'Metabolismo reduzido', 'Acúmulo possível', '3', 'PharmGKB', 1),
('THC (Tetrahidrocanabinol)', 'Canabinoides', 'COMT', 'rs4680', 'target', 'COMT modula efeitos psicóticos do THC via dopamina', 'Menor risco de psicose', 'Risco intermediário', 'Risco aumentado de psicose com cannabis', '2B', 'PharmGKB', 1),
('Tetrabenazina', 'Inibidores do Transportador de Monoamina', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza tetrabenazina - FDA limita dose em PM lentos', 'Dose até 75mg/dia', 'Monitorar', 'Dose máxima 50mg/dia (FDA)', '1A', 'FDA', 1),
('Tenoxicam', 'Anti-inflamatórios (AINEs)', 'CYP2C9', 'rs1799853', 'substrate', 'CYP2C9 metaboliza tenoxicam', 'Dose padrão', 'Meia-vida prolongada - monitorar', 'Risco de sangramento GI aumentado', '2A', 'DPWG', 1),
('Tegafur', 'Antineoplásicos', 'DPYD', 'rs3918290', 'substrate', 'DPD metaboliza fluoropirimidinas - deficiência causa toxicidade fatal', 'Dose padrão', 'Reduzir dose 25-50%', 'CONTRAINDICADO - toxicidade fatal', '1A', 'CPIC', 1),
('Tegafur', 'Antineoplásicos', 'DPYD', 'rs67376798', 'substrate', 'DPYD D949V - atividade DPD reduzida', 'Normal', 'DPD reduzida - cautela', 'DPD deficiente', '1A', 'CPIC', 1),
('Tansulosina', 'Antagonistas alfa-adrenérgicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza tansulosina', 'Dose padrão', 'Monitorar hipotensão', 'Risco de hipotensão - reduzir dose', '2B', 'PharmGKB', 1),
('Tamoxifeno', 'Antineoplásicos (Hormonioterapia)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 converte tamoxifeno em endoxifeno (metabólito ativo) - ESSENCIAL', 'Eficácia plena', 'Eficácia parcialmente reduzida', 'Tamoxifeno INEFICAZ - usar inibidor de aromatase', '1A', 'CPIC', 1),
('Tafenoquina', 'Antiparasitários', 'G6PD', 'rs1050829', 'risk', 'Deficiência de G6PD causa hemólise grave com tafenoquina', 'Sem risco', 'Teste quantitativo obrigatório', 'CONTRAINDICADO', '1A', 'FDA', 1),
('Tacrolimo', 'Imunossupressores', 'CYP3A5', 'rs776746', 'substrate', 'CYP3A5*3 determina expressão - expressores precisam de dose 1.5-2x maior', 'Dose padrão (não-expressor)', 'Expressor parcial - dose 1.5x', 'Expressor - dose 2x', '1A', 'CPIC', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. TIOGUANINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tioguanina',
    (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Tabloid®, Lanvis®',
    'A tioguanina (6-TG) é um antimetabólito análogo da purina utilizado no tratamento de leucemias agudas (LMA e LLA). Atua inibindo a síntese de DNA. É metabolizada pela enzima TPMT. Em pacientes com deficiência de TPMT, doses padrão causam mielossupressão grave (pancitopenia) potencialmente fatal. Efeitos adversos incluem mielossupressão, hepatotoxicidade (doença veno-oclusiva) e náuseas.',
    'O gene TPMT (cromossomo 6) codifica a tiopurina metiltransferase, que inativa as tiopurinas. O genótipo CC em rs1800460 (*3B) e CC em rs1800462 (*2) indica atividade NORMAL da TPMT. O paciente pode receber doses padrão de tioguanina sem risco aumentado de mielossupressão por este mecanismo.',
    'rs1800460',
    '6',
    'TPMT',
    'Europeia (frequência de deficientes: ~0.3%)',
    'TPMT rs1800460 (*3B):\n- CC = Normal (atividade plena)\n- CT = Intermediário (reduzir dose 30-50%)\n- TT = Deficiente (mielossupressão grave - dose 10% ou contraindicar)\n\nTPMT rs1800462 (*2):\n- CC = Normal\n- CG = Intermediário\n- GG = Deficiente',
    'TPMT normal (CC em *3B e *2). Dose padrão de tioguanina é segura do ponto de vista da TPMT. Monitorar hemograma conforme protocolo oncológico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. TICAGRELOR
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ticagrelor',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Brilique®, Coaly®',
    'O ticagrelor é um antiplaquetário inibidor do receptor P2Y12, utilizado para prevenção de eventos aterotrombóticos em pacientes com síndrome coronariana aguda. Diferente do clopidogrel, o ticagrelor NÃO é uma pró-droga e NÃO depende do CYP2C19 para ativação. Esta é uma vantagem farmacogenética importante. Efeitos adversos incluem sangramento, dispneia e bradicardia.',
    'O CYP2C19 NÃO afeta a eficácia do ticagrelor (diferente do clopidogrel). O ticagrelor é um fármaco ativo que não precisa de bioativação hepática. O CPIC recomenda ticagrelor como alternativa preferencial para pacientes com perda de função do CYP2C19 que necessitam de antiplaquetário.',
    'rs12248560',
    '10',
    'CYP2C19',
    'Europeia',
    'CYP2C19 NÃO afeta ticagrelor:\n- Qualquer genótipo = Ticagrelor funciona normalmente\n\nNota: Se o paciente for metabolizador lento de CYP2C19, o ticagrelor é a ALTERNATIVA PREFERENCIAL ao clopidogrel.',
    'O ticagrelor funciona independentemente do genótipo CYP2C19. É a alternativa recomendada pelo CPIC para pacientes metabolizadores lentos/intermediários do CYP2C19 que não podem usar clopidogrel com eficácia.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. THC (TETRAHIDROCANABINOL)
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'THC (Tetrahidrocanabinol)',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Mevatyl® (THC+CBD), Dronabinol (Marinol®)',
    'O THC (delta-9-tetrahidrocanabinol) é o principal composto psicoativo da cannabis. Usado terapeuticamente para náuseas refratárias, dor crônica, espasticidade e epilepsia (em combinação com CBD). É metabolizado pelo CYP2C9 (via principal) e CYP3A4 (via secundária). O gene COMT influencia o risco de efeitos psicóticos. Efeitos adversos incluem ansiedade, paranoia, taquicardia, boca seca e comprometimento cognitivo.',
    'O CYP2C9 é a via principal de metabolização do THC. Com genótipos normais (CC/*1*1), o THC é processado adequadamente. O CYP3A4 (GG, normal) contribui como via secundária. O COMT (rs4680 AG = Val/Met) indica atividade intermediária — pessoas com este genótipo têm risco INTERMEDIÁRIO de efeitos psicóticos com cannabis comparado ao genótipo GG (Val/Val) que tem maior risco.',
    'rs4680',
    '22',
    'COMT',
    'Europeia',
    'CYP2C9 rs1799853 (*2):\n- CC = Normal (metabolismo padrão do THC)\n- CT = Intermediário (efeitos mais prolongados ~30%)\n- TT = Lento (efeitos muito prolongados)\n\nCOMT rs4680 (Val158Met) e risco psicótico:\n- AA (Met/Met) = Menor risco psicótico com cannabis\n- AG (Val/Met) = Risco intermediário\n- GG (Val/Val) = Maior risco de psicose com cannabis\n\nCYP3A4 rs35599367: GG = Normal',
    'CYP2C9 normal — THC metabolizado adequadamente. COMT AG (Val/Met) — risco INTERMEDIÁRIO de efeitos psicóticos com cannabis. Em adolescentes autistas, o uso de cannabis/THC deve ser avaliado com extrema cautela. O cérebro em desenvolvimento é mais vulnerável aos efeitos do THC.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. TETRABENAZINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tetrabenazina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Xenazine®',
    'A tetrabenazina é um inibidor do transportador vesicular de monoaminas 2 (VMAT2), utilizada para tratamento de coreia na doença de Huntington e discinesias. É metabolizada pelo CYP2D6. A FDA estabelece dose máxima de 50mg/dia para metabolizadores lentos. Efeitos adversos incluem depressão, suicídio, parkinsonismo, sedação e acatisia.',
    'O CYP2D6 metaboliza a tetrabenazina. A FDA exige genotipagem do CYP2D6 antes de doses >50mg/dia. O SNP principal (*4, rs3892097) não está disponível no chip GSA v3.0.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (dose até 75mg/dia)\n- GA = Intermediário\n- AA = Metabolizador lento (dose máxima 50mg/dia - FDA)',
    'CYP2D6 indeterminado. FDA exige teste de CYP2D6 antes de doses >50mg/dia. Iniciar dose baixa e titular.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. TENOXICAM
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tenoxicam',
    (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Tilatil®, Teflan®, Tenoxil®, Tiloxineo®',
    'O tenoxicam é um anti-inflamatório não esteroidal (AINE) do grupo dos oxicams, utilizado para artrite reumatoide, osteoartrite e dor aguda. Possui meia-vida longa (~70h). É metabolizado pelo CYP2C9. Em metabolizadores lentos, a meia-vida é ainda mais prolongada com risco de sangramento GI. Efeitos adversos incluem úlcera gástrica, sangramento, nefrotoxicidade e reações cutâneas.',
    'O CYP2C9 (cromossomo 10) metaboliza o tenoxicam. O genótipo CC em rs1799853 (*2) indica metabolizador NORMAL. O tenoxicam é processado adequadamente.',
    'rs1799853', '10', 'CYP2C9', 'Europeia',
    'CYP2C9 rs1799853 (*2):\n- CC = Normal\n- CT = Intermediário (meia-vida prolongada, monitorar)\n- TT = Lento (risco aumentado de sangramento GI)',
    'CYP2C9 normal (CC). Tenoxicam metabolizado adequadamente. Dose padrão apropriada. Monitorar função renal e sintomas GI conforme protocolo para AINEs.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. TEGAFUR
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tegafur',
    (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Teysuno®, UFT®',
    'O tegafur é uma pró-droga da fluoropirimidina 5-fluorouracil (5-FU), utilizado em quimioterapia de cânceres gastrointestinais, mama e cabeça/pescoço. É convertido em 5-FU no organismo, que é então inativado pela enzima DPD (DPYD). Deficiência de DPD causa toxicidade FATAL (mucosite grave, mielossupressão, neurotoxicidade). O CPIC EXIGE teste de DPYD antes do uso.',
    'O gene DPYD (cromossomo 1) codifica a DPD, que inativa o 5-FU. O genótipo CC em rs3918290 (*2A) e TT em rs67376798 (D949V) indicam atividade NORMAL da DPD. O paciente pode receber fluoropirimidinas com segurança do ponto de vista da DPD.',
    'rs3918290', '1', 'DPYD', 'Europeia (frequência de deficientes heterozigotos: ~3-5%)',
    'DPYD rs3918290 (*2A):\n- CC/GG = Normal (DPD funcional)\n- CT/GA = Heterozigoto (reduzir dose 25-50%)\n- TT/AA = Deficiente → CONTRAINDICADO (toxicidade fatal)\n\nDPYD rs67376798 (D949V):\n- TT/AA = Normal\n- AT = DPD reduzida (cautela)\n- AA/TT variante = DPD deficiente',
    'DPYD normal (*2A CC, D949V TT). Tegafur/5-FU pode ser usado com segurança do ponto de vista da DPD. Monitorar toxicidade conforme protocolo oncológico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. TANSULOSINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tansulosina',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Secotex®, Rettan®, Stub®, Tasulil®',
    'A tansulosina é um antagonista seletivo dos receptores alfa-1A adrenérgicos, utilizada para sintomas do trato urinário inferior associados à hiperplasia prostática benigna. É metabolizada pelo CYP2D6 e CYP3A4. Efeitos adversos incluem hipotensão ortostática, tontura, ejaculação retrógrada e síndrome da íris flácida intraoperatória (IFIS).',
    'O CYP2D6 metaboliza a tansulosina. O SNP principal (*4, rs3892097) não está disponível. Em metabolizadores lentos, pode haver acúmulo com mais hipotensão.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário\n- AA = Metabolizador lento (risco de hipotensão aumentado)',
    'CYP2D6 indeterminado. Se prescrita, monitorar pressão arterial e sintomas ortostáticos. ALERTA CIRÚRGICO: Informar oftalmologista se cirurgia ocular — risco de síndrome IFIS.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. TAMOXIFENO
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tamoxifeno',
    (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Nolvadex®, Festone®, Tacfen®, Taxofen®',
    'O tamoxifeno é um modulador seletivo do receptor de estrogênio (SERM), utilizado na hormonioterapia do câncer de mama receptor de estrogênio positivo (ER+). É uma PRÓ-DROGA que PRECISA ser convertida pelo CYP2D6 em seu metabólito ativo (endoxifeno). Sem CYP2D6 funcional, o tamoxifeno é INEFICAZ e o câncer pode progredir. O CPIC recomenda inibidores de aromatase como alternativa em metabolizadores lentos.',
    'O CYP2D6 converte o tamoxifeno em endoxifeno, que é 100x mais potente. SEM CYP2D6 funcional, o tamoxifeno é INEFICAZ contra o câncer. O SNP principal (*4, rs3892097) não está disponível no chip.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (endoxifeno produzido - tamoxifeno EFICAZ)\n- GA = Intermediário (eficácia parcial - considerar alternativa)\n- AA = Metabolizador nulo → Tamoxifeno INEFICAZ (usar inibidor de aromatase)',
    'CYP2D6 indeterminado. ALERTA ONCOLÓGICO: Se tamoxifeno for prescrito para câncer de mama, é ESSENCIAL fazer teste completo de CYP2D6 antes. Metabolizadores lentos devem usar inibidores de aromatase (anastrozol, letrozol). Também evitar inibidores do CYP2D6 (fluoxetina, paroxetina) durante uso de tamoxifeno.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. TAFENOQUINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tafenoquina',
    (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Krintafel®',
    'A tafenoquina é um antimalárico de dose única utilizado para prevenção de recaídas de malária vivax (Plasmodium vivax). Atua eliminando hipnozoítos hepáticos. A FDA EXIGE teste quantitativo de G6PD antes da administração — em pacientes com deficiência de G6PD causa hemólise grave e potencialmente fatal. Efeitos adversos incluem cefaleia, tontura, náuseas e metemoglobinemia.',
    'O gene G6PD (cromossomo X) codifica a glicose-6-fosfato desidrogenase. O genótipo TT em rs1050829 indica atividade NORMAL. Sem risco de hemólise por deficiência de G6PD.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829 (N126D):\n- TT (homens) = Normal (sem deficiência)\n- CC (homens) = Deficiente → CONTRAINDICADO\n- CT (mulheres) = Heterozigota (teste quantitativo obrigatório)\n\nNota: FDA exige teste quantitativo de G6PD antes de administrar tafenoquina.',
    'G6PD normal (TT). Sem contraindicação genética ao uso de tafenoquina. A FDA exige confirmação com teste quantitativo mesmo com genótipo normal.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. TACROLIMO
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tacrolimo',
    (SELECT id FROM pgx_drug_classes WHERE code='imuno_transplante' LIMIT 1),
    'Prograf®, Prolimus®, Tacrolil®, Tarfic®',
    'O tacrolimo é um imunossupressor inibidor da calcineurina, utilizado para prevenção de rejeição em transplantes de órgãos sólidos (rim, fígado, coração) e em doenças autoimunes. É metabolizado pelo CYP3A5 e CYP3A4 no fígado. A janela terapêutica é estreita — níveis baixos causam rejeição, níveis altos causam nefrotoxicidade, neurotoxicidade e diabetes. Monitoramento dos níveis séricos (TDM) é OBRIGATÓRIO.',
    'O gene CYP3A5 (cromossomo 7) determina a velocidade de metabolização do tacrolimo. O genótipo CT em rs776746 (*3) indica que o paciente é EXPRESSOR PARCIAL do CYP3A5. Isso significa que ele metaboliza tacrolimo MAIS RAPIDAMENTE que não-expressores e pode necessitar de dose 1.5x maior para atingir níveis terapêuticos.',
    'rs776746', '7', 'CYP3A5',
    'Europeia (frequência de expressores: ~15-20% em europeus, ~70% em africanos)',
    'CYP3A5 rs776746 (*3):\n- GG/AA (não-expressor *3/*3) = Dose padrão (metabolismo mais lento)\n- AG/CT (expressor parcial *1/*3) = Dose 1.5x maior necessária\n- CC/TT (expressor *1/*1) = Dose 2x maior necessária\n\nNota: O CPIC recomenda ajuste de dose inicial baseado no genótipo CYP3A5, com TDM obrigatório.',
    'CYP3A5 EXPRESSOR PARCIAL (CT/*1/*3). Se tacrolimo for prescrito, a dose inicial deve ser ~1.5x maior que a padrão para atingir níveis terapêuticos. Monitoramento dos níveis séricos (TDM) é OBRIGATÓRIO independente do genótipo. Este é um achado RELEVANTE para transplantes.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
