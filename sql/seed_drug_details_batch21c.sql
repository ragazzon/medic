-- =====================================================
-- BATCH 21C: Medicamento 210 - Atorvastatina (final)
-- =====================================================

-- 10. ATORVASTATINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Atorvastatina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'A atorvastatina é uma estatina (inibidor da HMG-CoA redutase) utilizada para redução do colesterol LDL e prevenção cardiovascular. É a estatina mais prescrita mundialmente. Efeitos adversos incluem mialgia, elevação de CPK, rabdomiólise (raro), hepatotoxicidade e diabetes. Nomes comerciais: Citalor®, Atorless®, Atorvasterol®, Corastorva®, Kolevas®, Lipitor®.',
  'O gene SLCO1B1, localizado no cromossomo 12, codifica o transportador OATP1B1, responsável pela captação hepática das estatinas. A variante rs4149056 (SLCO1B1*5, Val174Ala) reduz a captação hepática, aumentando os níveis sistêmicos e o risco de miopatia. Eric possui SLCO1B1 TC (heterozigoto) = transporte REDUZIDO. Além disso, APOA5 rs964184 GC = risco moderado de triglicerídeos elevados.',
  'rs4149056', '12', 'SLCO1B1', 'T,C',
  'Europeia',
  'Eric possui SLCO1B1 TC (heterozigoto *1/*5). Risco de miopatia 2-3x aumentado com atorvastatina em doses altas (>40mg). APOA5 GC sugere possível benefício de estatinas se colesterol elevado.',
  'SLCO1B1 TC (heterozigoto): A atorvastatina é MENOS afetada pelo SLCO1B1 que a sinvastatina, mas em doses altas (>40mg) o risco de miopatia existe. RECOMENDAÇÕES: (1) Se estatina necessária, atorvastatina em dose baixa-moderada (10-20mg) OU rosuvastatina (menor impacto SLCO1B1) são preferíveis à sinvastatina. (2) APOA5 GC indica possível predisposição a triglicerídeos elevados - monitorar perfil lipídico. (3) Evitar sinvastatina >20mg (risco 4.5x miopatia). (4) Monitorar CPK e sintomas musculares. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Citalor, Atorless, Atorvasterol, Corastorva, Kolevas, Lipitor',
  '1A',
  'SLCO1B1 e estatinas: evidência FORTE (1A). Guideline CPIC. Atorvastatina menos afetada que sinvastatina mas risco existe em doses altas. APOA5 rs964184: evidência 2B para triglicerídeos.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);