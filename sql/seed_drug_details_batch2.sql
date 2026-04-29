-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 2 de N)
-- Inclui correção da classe da Varfarina + 10 novos medicamentos
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- CORREÇÃO: Atualizar classe da Varfarina de "Anticoag" para "Anticoagulantes"
-- =============================================
UPDATE `pgx_drug_genes` SET `drug_class` = 'Anticoagulantes' WHERE `drug_name` = 'Varfarina';

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes (faz aparecer no dashboard)
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Vardenafila', 'Inibidores de Fosfodiesterases', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza vardenafila', 'Dose padrão', 'Metabolismo reduzido - iniciar dose menor', 'Risco de acúmulo e efeitos adversos', '2A', 'PharmGKB', 1),
('Valbenazina', 'Inibidores do Transportador de Monoamina', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza valbenazina em metabólito ativo', 'Dose padrão', 'Monitorar resposta', 'Dose máxima 40mg (metabolizador lento)', '1A', 'FDA', 1),
('Tropisetrona', 'Antieméticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza tropisetrona', 'Dose padrão', 'Monitorar resposta', 'Eficácia pode estar reduzida em ultra-rápidos', '2B', 'DPWG', 1),
('Trimipramina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 é via principal de metabolização', 'Dose padrão', 'Monitorar níveis', 'Reduzir dose ou usar alternativa', '1A', 'CPIC', 1),
('Trimipramina', 'Antidepressivos (Tricíclicos)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via secundária - *17 aumenta metabolismo', 'Dose padrão', 'Metabolismo aumentado', 'Pode necessitar dose maior', '2A', 'CPIC', 1),
('Trimetoprina-Sulfametoxazol', 'Antibióticos', 'G6PD', 'rs1050829', 'risk', 'Deficiência de G6PD pode causar anemia hemolítica com sulfonamidas', 'Sem risco aumentado', 'Risco intermediário', 'CONTRAINDICADO se G6PD deficiente', '1A', 'CPIC', 1),
('Trazodona', 'Antidepressivos', 'CYP3A4', 'rs35599367', 'substrate', 'CYP3A4 metaboliza trazodona', 'Dose padrão', 'Metabolismo reduzido - monitorar sedação', 'Risco de acúmulo', '2A', 'PharmGKB', 1),
('Tramadol', 'Analgésicos Opioides', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 converte tramadol em metabólito ativo (M1/O-desmetiltramadol)', 'Dose padrão', 'Analgesia possivelmente reduzida', 'Sem conversão - tramadol INEFICAZ', '1A', 'CPIC', 1),
('Tramadol', 'Analgésicos Opioides', 'COMT', 'rs4680', 'target', 'COMT modula percepção de dor e resposta a opioides', 'Boa resposta a opioides', 'Resposta intermediária', 'Pode necessitar dose maior', '2A', 'PharmGKB', 1),
('Torasemida', 'Diuréticos', 'GNB3', 'rs5443', 'target', 'GNB3 modula resposta a diuréticos tiazídicos', 'Resposta padrão', 'Resposta variável', 'Melhor resposta a tiazídicos', '3', 'PharmGKB', 1),
('Tolterodina', 'Antimuscarínicos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza tolterodina', 'Dose padrão', 'Monitorar efeitos anticolinérgicos', 'Risco de acúmulo - considerar fesoterodina', '2A', 'DPWG', 1),
('Tioridazina', 'Antipsicóticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza tioridazina - risco de QTc', 'Dose padrão com monitoramento ECG', 'Risco aumentado de prolongamento QTc', 'CONTRAINDICADO em metabolizadores lentos', '1A', 'FDA', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. VARDENAFILA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Vardenafila',
    (SELECT id FROM pgx_drug_classes WHERE code='pde_inhib' LIMIT 1),
    'Levitra®, Leviosa®, Varmuve®',
    'A vardenafila é um inibidor da fosfodiesterase tipo 5 (PDE5), utilizada para tratamento de disfunção erétil e, em alguns casos, hipertensão arterial pulmonar. Atua relaxando a musculatura lisa vascular, aumentando o fluxo sanguíneo. É metabolizada principalmente pelo CYP3A4 no fígado. Efeitos adversos incluem cefaleia, rubor facial, congestão nasal, dispepsia e alterações visuais.',
    'O gene CYP3A4 (cromossomo 7) metaboliza a vardenafila. O genótipo GG no rs35599367 (*22) indica metabolismo normal — a enzima funciona adequadamente.',
    'rs35599367',
    '7',
    'CYP3A4',
    'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal\n- GA/CT = Metabolismo reduzido (iniciar dose menor)\n- AA/TT = Metabolismo muito reduzido (risco de hipotensão)',
    'Metabolismo normal pelo CYP3A4. Dose padrão apropriada. Evitar uso concomitante com inibidores potentes do CYP3A4 (cetoconazol, ritonavir) e nitratos.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. VALBENAZINA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Valbenazina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Ingrezza®',
    'A valbenazina é um inibidor seletivo do transportador vesicular de monoaminas 2 (VMAT2), indicada para tratamento de discinesia tardia em adultos. É metabolizada pelo CYP2D6 em seu metabólito ativo. Efeitos adversos incluem sonolência, acatisia, desequilíbrio e prolongamento do QTc.',
    'O CYP2D6 metaboliza a valbenazina em seu metabólito ativo. A FDA recomenda dose máxima de 40mg para metabolizadores lentos do CYP2D6. O SNP principal (*4, rs3892097) não está disponível no chip GSA v3.0.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (dose até 80mg)\n- GA = Intermediário\n- AA = Metabolizador lento (dose máxima 40mg por recomendação FDA)',
    'CYP2D6 indeterminado. Se prescrita, iniciar com dose baixa e monitorar. A FDA limita a 40mg em metabolizadores lentos — considerar teste específico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. TROPISETRONA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tropisetrona',
    (SELECT id FROM pgx_drug_classes WHERE code='gastro' LIMIT 1),
    'Navoban®',
    'A tropisetrona é um antiemético antagonista do receptor 5-HT3, utilizada para prevenção de náuseas e vômitos induzidos por quimioterapia e pós-operatórios. É metabolizada pelo CYP2D6. Em metabolizadores ultra-rápidos, a eficácia pode estar reduzida por eliminação acelerada. Efeitos adversos incluem cefaleia, constipação e tontura.',
    'O CYP2D6 metaboliza a tropisetrona. Em metabolizadores ultra-rápidos, o medicamento pode ser eliminado rapidamente demais, reduzindo a eficácia antiemética. O SNP principal não está disponível no chip.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário\n- AA = Metabolizador lento (eficácia mantida, monitorar efeitos)\n\nMetabolizadores ultra-rápidos: eficácia pode ser reduzida',
    'CYP2D6 indeterminado. Se usado como antiemético pós-operatório (cirurgia maxilar), monitorar eficácia. Alternativas: ondansetrona (menos dependente do CYP2D6).',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. TRIMIPRAMINA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Trimipramina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Surmontil®',
    'A trimipramina é um antidepressivo tricíclico utilizado para tratamento de depressão, ansiedade e insônia. Diferente de outros tricíclicos, possui efeito sedativo pronunciado e menor ação noradrenérgica. É metabolizada pelo CYP2D6 (via principal) e CYP2C19 (via secundária). Efeitos adversos incluem sedação, boca seca, constipação, retenção urinária e ganho de peso.',
    'O CYP2D6 (não disponível) é a via principal e o CYP2C19 a via secundária. Para CYP2C19: o genótipo CT em rs12248560 (*17) indica metabolizador rápido, podendo necessitar de dose maior para atingir efeito terapêutico por esta via.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário (monitorar níveis)\n- AA = Lento (reduzir dose 50%)\n\nCYP2C19 rs12248560 (*17):\n- CC = Normal\n- CT = Metabolizador rápido (pode necessitar dose maior)\n- TT = Ultra-rápido',
    'CYP2D6 indeterminado. CYP2C19 é metabolizador rápido (*1/*17) — pode necessitar dose ligeiramente maior pela via secundária. Monitorar resposta clínica. CPIC recomenda considerar alternativas em metabolizadores lentos do CYP2D6.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. TRIMETOPRINA-SULFAMETOXAZOL
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Trimetoprina-Sulfametoxazol',
    (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Bactrim®, Bacteracin®, Bactoprin®, Espectroprima®, Subtrax®',
    'A associação trimetoprina + sulfametoxazol é um antibiótico de amplo espectro utilizado para infecções urinárias, respiratórias, gastrointestinais e profilaxia em imunodeprimidos. A sulfonamida (sulfametoxazol) pode desencadear anemia hemolítica em pacientes com deficiência de G6PD. Efeitos adversos incluem náuseas, erupção cutânea, fotossensibilidade e, raramente, síndrome de Stevens-Johnson.',
    'O gene G6PD (cromossomo X) codifica a enzima glicose-6-fosfato desidrogenase, essencial para proteger os glóbulos vermelhos do estresse oxidativo. O genótipo TT no rs1050829 (N126D) indica atividade NORMAL da G6PD, sem risco aumentado de hemólise.',
    'rs1050829',
    'X',
    'G6PD',
    'Europeia',
    'G6PD rs1050829 (N126D):\n- CC/TT = Normal (sem deficiência)\n- CT = Variante heterozigota (mulheres - atividade variável)\n- TT em homens = Normal\n\nNota: Deficiência de G6PD é mais prevalente em populações africanas e mediterrâneas.',
    'G6PD normal (TT). Sem contraindicação ao uso de trimetoprina-sulfametoxazol por motivo genético. Medicamento pode ser usado com segurança neste aspecto.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. TRAZODONA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Trazodona',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Donaren®, Molipaxin®, Trazostab®, Andhora®',
    'A trazodona é um antidepressivo atípico com propriedades sedativas, utilizada para depressão e, em doses baixas, como indutor do sono. Atua como antagonista/inibidor da recaptação de serotonina (SARI). É metabolizada pelo CYP3A4 no fígado. Efeitos adversos incluem sonolência, tontura, boca seca, hipotensão ortostática e, raramente, priapismo.',
    'O CYP3A4 (cromossomo 7) metaboliza a trazodona. O genótipo GG indica metabolismo normal.',
    'rs35599367',
    '7',
    'CYP3A4',
    'Europeia',
    'CYP3A4 rs35599367 (*22):\n- GG/CC = Normal\n- GA/CT = Metabolismo reduzido (sedação aumentada)\n- AA/TT = Metabolismo muito reduzido',
    'Metabolismo normal pelo CYP3A4. Trazodona processada adequadamente. Dose padrão apropriada. Frequentemente usada em doses baixas (50-100mg) como indutor do sono em autistas.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. TRAMADOL
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tramadol',
    (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'Tramal®, Neotramol®, Tramadon®, Tramaliv®, Traum®',
    'O tramadol é um analgésico opioide sintético de ação central, utilizado para dores moderadas a intensas. É uma PRÓ-DROGA que precisa ser convertida pelo CYP2D6 em seu metabólito ativo (O-desmetiltramadol/M1) para ter efeito analgésico. Sem essa conversão, o tramadol é INEFICAZ. Efeitos adversos incluem náuseas, tontura, constipação, sonolência e risco de convulsões.',
    'O CYP2D6 (cromossomo 22) converte o tramadol no metabólito ativo M1. SEM CYP2D6 funcional, o tramadol NÃO funciona como analgésico. O COMT (rs4680) modula a percepção de dor — o genótipo AG (Val/Met) do paciente indica resposta INTERMEDIÁRIA a opioides, podendo necessitar doses um pouco maiores.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (tramadol eficaz)\n- GA = Intermediário (eficácia parcial)\n- AA = Metabolizador nulo → TRAMADOL INEFICAZ\n\nCOMT rs4680 (Val158Met):\n- AA (Met/Met) = Maior sensibilidade à dor, melhor resposta\n- AG (Val/Met) = Intermediário\n- GG (Val/Val) = Menor sensibilidade, pode necessitar dose maior',
    'ATENÇÃO PARA CIRURGIA: CYP2D6 indeterminado — não é possível garantir que tramadol será eficaz. COMT AG indica resposta intermediária a opioides. RECOMENDAÇÃO: Para analgesia pós-operatória da cirurgia maxilar, preferir opioides que NÃO dependem do CYP2D6 (morfina, oxicodona). Evitar tramadol e codeína até esclarecer CYP2D6.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. TORASEMIDA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Torasemida',
    (SELECT id FROM pgx_drug_classes WHERE code='cardio' LIMIT 1),
    'Soaanz®, Demadex®',
    'A torasemida é um diurético de alça utilizado no tratamento de edemas associados a insuficiência cardíaca, hepática ou renal, e hipertensão. Atua inibindo o cotransportador Na-K-2Cl no ramo ascendente da alça de Henle. Efeitos adversos incluem hipocalemia, hiponatremia, hipotensão, tontura e ototoxicidade (em doses altas).',
    'O gene GNB3 (cromossomo 12) codifica a subunidade beta-3 da proteína G, envolvida na transdução de sinal. A variante C825T (rs5443) afeta a resposta a diuréticos. O genótipo CC (referência) indica resposta padrão.',
    'rs5443',
    '12',
    'GNB3',
    'Europeia',
    'GNB3 rs5443 (C825T):\n- CC = Resposta padrão a diuréticos\n- CT = Resposta variável\n- TT = Melhor resposta a tiazídicos (pode ter pressão mais responsiva)',
    'GNB3 CC (normal). Resposta padrão esperada à torasemida. Sem necessidade de ajuste de dose por motivo genético.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. TOLTERODINA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tolterodina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Detrusitol®, Detrol®',
    'A tolterodina é um antimuscarínico (anticolinérgico) utilizado para tratamento de bexiga hiperativa com sintomas de urgência urinária, frequência e incontinência. É metabolizada pelo CYP2D6 no fígado. Em metabolizadores lentos, pode haver acúmulo com mais efeitos anticolinérgicos. Efeitos adversos incluem boca seca, constipação, visão turva e retenção urinária.',
    'O CYP2D6 metaboliza a tolterodina. Em metabolizadores lentos, níveis plasmáticos podem ser 2-3 vezes maiores, aumentando efeitos anticolinérgicos. O DPWG recomenda considerar fesoterodina como alternativa. O SNP principal não está disponível.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal\n- GA = Intermediário (monitorar boca seca, constipação)\n- AA = Metabolizador lento (considerar fesoterodina ou darifenacina)',
    'CYP2D6 indeterminado. Se prescrita, monitorar efeitos anticolinérgicos (boca seca, constipação, retenção urinária). Se efeitos excessivos, considerar fesoterodina como alternativa.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. TIORIDAZINA
INSERT INTO `pgx_drug_details` 
(`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Tioridazina',
    (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Melleril®, Unitidazin®',
    'A tioridazina é um antipsicótico típico (fenotiazínico) utilizado para esquizofrenia. É um dos antipsicóticos com MAIOR risco de prolongamento do intervalo QTc, podendo causar arritmias fatais (torsade de pointes). É metabolizada pelo CYP2D6. A FDA CONTRAINDICA em metabolizadores lentos do CYP2D6. Efeitos adversos incluem sedação, hipotensão, retinopatia pigmentar (dose cumulativa) e sintomas extrapiramidais.',
    'O CYP2D6 metaboliza a tioridazina. Em metabolizadores lentos, os níveis podem subir perigosamente, aumentando o risco de prolongamento QTc e arritmias fatais. A FDA exige teste de CYP2D6 antes de prescrever e CONTRAINDICA em metabolizadores lentos. O SNP principal não está disponível no chip.',
    'rs3892097',
    '22',
    'CYP2D6',
    'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (usar com monitoramento ECG)\n- GA = Intermediário (cautela, ECG obrigatório)\n- AA = Metabolizador lento → CONTRAINDICADO (risco de arritmia fatal)',
    'CYP2D6 indeterminado. ALERTA: A tioridazina é CONTRAINDICADA pela FDA em metabolizadores lentos do CYP2D6 pelo risco de arritmia fatal. Sem dados do CYP2D6, este medicamento NÃO deve ser prescrito sem teste farmacogenético prévio.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
    1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
