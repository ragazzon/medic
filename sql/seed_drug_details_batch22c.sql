-- =====================================================
-- BATCH 22C: Medicamentos 219-220 (Anlodipino, Anfetamina)
-- FINAL DOS 232 MEDICAMENTOS!
-- =====================================================

-- 9. ANLODIPINO
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anlodipino',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'O anlodipino é um bloqueador de canais de cálcio diidropiridínico, utilizado no tratamento de hipertensão e angina. Possui meia-vida longa (30-50h). Via principal: CYP3A4 (com contribuição menor do CYP2D6). Efeitos adversos: edema periférico, cefaleia, rubor, tontura. Nomes comerciais: Norvasc®, Alivpress®, Anlo®, Tenlopin®, Tensaliv®.',
  'O CYP3A4 é a via PRINCIPAL de metabolismo do anlodipino, com contribuição SECUNDÁRIA do CYP2D6. Eric: CYP3A4 GG (normal). Como a via principal está normal, o impacto do CYP2D6 desconhecido é LIMITADO.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D mas CYP3A4 GG (normal). Como CYP3A4 é a via PRINCIPAL, o impacto do CYP2D6 desconhecido é clinicamente LIMITADO para anlodipino.',
  'CYP3A4 é a via principal do anlodipino e está NORMAL (GG). O CYP2D6 é via secundária com impacto limitado. Anlodipino pode ser usado normalmente. VANTAGEM: não depende criticamente do CYP2D6 desconhecido. Se anti-hipertensivo necessário, anlodipino é opção farmacogeneticamente aceitável. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Norvasc, Alivpress, Anlo, Tenlopin, Tensaliv',
  '3',
  'CYP2D6 e anlodipino: evidência baixa (3). CYP3A4 é via principal (normal). Impacto clínico do CYP2D6 limitado.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 10. ANFETAMINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anfetamina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'As anfetaminas são psicoestimulantes utilizados no tratamento de TDAH e narcolepsia. Incluem dextroanfetamina, anfetamina racêmica e lisdexanfetamina (pró-droga). Mecanismo: liberação de dopamina e noradrenalina. Efeitos adversos: insônia, diminuição de apetite, taquicardia, ansiedade, irritabilidade. Nomes comerciais: Evekeo®, Dynavel®, Adzenys®. No Brasil, a lisdexanfetamina (Venvanse®) é a forma mais acessível.',
  'As anfetaminas são primariamente metabolizadas por desaminação oxidativa (MAO) e conjugação (não-CYP). O CYP2D6 participa apenas de vias SECUNDÁRIAS de metabolismo. O impacto farmacogenético é LIMITADO para anfetaminas. Os genes mais relevantes para RESPOSTA são DRD1, SLC6A3 e COMT.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'CYP2D6 N/D mas impacto LIMITADO para anfetaminas (via secundária). DRD1 TT e COMT AG são mais relevantes para resposta.',
  'ANFETAMINAS PARA TDAH NO ERIC: O CYP2D6 desconhecido tem impacto LIMITADO (via secundária). PORÉM, DRD1 rs4532 TT sugere resposta VARIÁVEL a estimulantes dopaminérgicos. RECOMENDAÇÃO: (1) Lisdexanfetamina (Venvanse) é preferível pois é convertida por hidrólise (não-CYP); (2) Monitorar resposta clinicamente; (3) Se resposta insuficiente, considerar Guanfacina (não-estimulante, CYP3A4 normal). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Evekeo, Dynavel, Adzenys, Venvanse (lisdexanfetamina)',
  '3',
  'CYP2D6 e anfetaminas: evidência baixa (3) para metabolismo. DRD1/COMT: evidência moderada (2B) para resposta clínica.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);