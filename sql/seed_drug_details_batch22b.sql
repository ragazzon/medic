-- =====================================================
-- BATCH 22B: Medicamentos 214-220 (continuação final)
-- Asenapina, Aripiprazol Lauroxil, Aripiprazol, Apixabana,
-- Anticoncepcionais, Anlodipino, Anfetamina
-- =====================================================

-- 4. ASENAPINA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Asenapina',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'A asenapina é um antipsicótico atípico sublingual para esquizofrenia e mania bipolar. Antagonista D2/5-HT2A. Efeitos adversos: sonolência, acatisia, ganho de peso moderado, hipoestesia oral. Nomes comerciais: Saphris®, Secuado®.',
  'O gene CYP1A2, no cromossomo 15, metaboliza a asenapina. A variante rs762551 (*1F, alelo A) confere alta indutibilidade. Eric possui CYP1A2 CA = metabolizador ultra-rápido (induzível). Isso pode resultar em níveis menores de asenapina, especialmente se exposto a indutores (tabaco, carne grelhada).',
  'rs762551', '15', 'CYP1A2', 'C,A',
  'Europeia',
  'Eric: CYP1A2 CA (ultra-rápido induzível). Níveis de asenapina podem ser menores que o esperado. Porém, como via sublingual, a biodisponibilidade é menos CYP-dependente.',
  'CYP1A2 CA: metabolismo potencialmente mais rápido. PORÉM: asenapina sublingual tem biodisponibilidade de 35% por via sublingual vs <2% se engolida. O metabolismo hepático de primeira passagem é parcialmente contornado pela via sublingual. Impacto clínico do CYP1A2 é MODERADO. Monitorar resposta. Via transdérmica (Secuado) também contorna CYP1A2. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Saphris, Secuado',
  '2A',
  'CYP1A2 e asenapina: evidência moderada (2A). Via sublingual atenua impacto do CYP1A2. Eric CA = potencialmente rápido.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 5. ARIPIPRAZOL LAUROXIL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Aripiprazol Lauroxil',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O aripiprazol lauroxil é a forma injetável de ação prolongada (LAI) do aripiprazol, administrado mensalmente ou a cada 2 meses. Pró-droga que é convertida em aripiprazol. Indicado para esquizofrenia. Efeitos adversos: acatisia, insônia, cefaleia, reações no local da injeção. Nomes comerciais: Aristada®.',
  'O gene CYP2D6, no cromossomo 22, metaboliza o aripiprazol (forma ativa). Metabolizadores lentos CYP2D6 apresentam níveis ~60% maiores. A FDA recomenda ajuste de dose. Eric NÃO possui tipagem CYP2D6.',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D. Para forma LAI, ajuste é mais crítico pois não pode ser revertido rapidamente.',
  'ARIPIPRAZOL LAI SEM CYP2D6: ALTO RISCO. Como é injeção de longa duração, se Eric for metabolizador lento, os níveis elevados persistirão por semanas. RECOMENDAÇÃO: NÃO iniciar forma LAI sem tipagem CYP2D6 prévia. Se aripiprazol oral for tolerado, pode-se inferir fenótipo. FDA: PM CYP2D6 = reduzir para 441mg (ao invés de 882mg). Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Aristada',
  '1A',
  'CYP2D6 e aripiprazol LAI: evidência forte (1A). FDA label. Forma LAI = irreversibilidade = teste ANTES.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 6. ARIPIPRAZOL
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Aripiprazol',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Psiquiátricos/Neurológicos'),
  'O aripiprazol é um antipsicótico atípico, agonista parcial D2/5-HT1A e antagonista 5-HT2A. Utilizado em esquizofrenia, mania bipolar, depressão adjuvante, irritabilidade no autismo e Tourette. É um dos antipsicóticos mais prescritos para TEA. Efeitos adversos: acatisia, insônia, náuseas, cefaleia, ganho de peso moderado. Nomes comerciais: Abilify®, Aristab®, Toarip®, Sensaz®.',
  'O gene CYP2D6 metaboliza o aripiprazol. PM CYP2D6 = níveis ~60% maiores. FDA recomenda redução de dose. Além disso, MC4R (rs17782313) e HTR2C (rs3813929) influenciam ganho de peso. Eric: CYP2D6 N/D, MC4R TT (normal), HTR2C CC (risco padrão de ganho de peso).',
  'rs3892097', '22', 'CYP2D6', 'N/D',
  'Europeia',
  'Eric: CYP2D6 N/D. MC4R TT e HTR2C CC = menor risco de ganho de peso metabólico com antipsicóticos. Perfil favorável EXCETO pela incerteza do CYP2D6.',
  'ARIPIPRAZOL PARA AUTISMO: MC4R TT e HTR2C CC = perfil FAVORÁVEL para menor ganho de peso. CYP2D6 desconhecido: iniciar com dose BAIXA (2mg) e titular lentamente. Se boa tolerância em 2-5mg, fenótipo provavelmente não é PM. ALTERNATIVAS se CYP2D6 for preocupação: Paliperidona (não CYP2D6), Lurasidona (CYP3A4 normal). Para irritabilidade no TEA, aripiprazol tem aprovação FDA. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Abilify, Aristab, Toarip, Sensaz',
  '1A',
  'CYP2D6 e aripiprazol: evidência forte (1A). FDA label. MC4R/HTR2C: evidência 2B para ganho de peso.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 7. APIXABANA
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Apixabana',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'A apixabana é um anticoagulante oral direto (DOAC), inibidor do fator Xa. Utilizada na prevenção de AVC em fibrilação atrial e tratamento/prevenção de trombose venosa. NÃO requer monitoramento de INR. Efeitos adversos: sangramento, hematomas, anemia. Nomes comerciais: Eliquis®.',
  'O gene ABCG2, no cromossomo 4, codifica um transportador de efluxo. A variante rs2231142 (Q141K) reduz a função do transportador, podendo aumentar níveis de apixabana. Eric: ABCG2 GG (normal) = transporte normal. Além disso, F5 Leiden (rs6025) CC = sem trombofilia hereditária.',
  'rs2231142', '4', 'ABCG2', 'G,G',
  'Europeia',
  'Eric: ABCG2 GG (normal). Transporte adequado da apixabana. F5 Leiden CC = sem Fator V Leiden.',
  'Apixabana é ALTERNATIVA PREFERENCIAL à varfarina para Eric! Motivos: (1) VKORC1 TT torna varfarina muito sensível/instável; (2) ABCG2 GG = transporte normal; (3) F5 CC = sem trombofilia; (4) NÃO requer monitoramento de INR; (5) NÃO depende de CYP2C9 nem VKORC1. Se anticoagulação necessária (peri-operatória ou outra), PREFERIR apixabana ou rivaroxabana. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Eliquis',
  '2A',
  'ABCG2 e apixabana: evidência moderada (2A). Apixabana preferencial à varfarina quando VKORC1 TT.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 8. ANTICONCEPCIONAIS ORAIS (ESTROGÊNIO)
INSERT INTO pgx_drug_details (drug_name, class_id, description, understanding_result, snp_id, chromosome, gene_name, patient_genotype, study_population, genotype_result, suggestions, commercial_names, confidence_level, evidence_notes)
VALUES
(
  'Anticoncepcionais orais (estrogênio)',
  (SELECT id FROM pgx_drug_classes WHERE class_name = 'Cardiológicos'),
  'Os anticoncepcionais orais combinados contêm estrogênio (etinilestradiol) e progestágeno. Embora primariamente contraceptivos, a análise farmacogenética foca no RISCO TROMBÓTICO associado ao estrogênio. Portadores do Fator V Leiden têm risco 7-30x aumentado de trombose venosa com estrogênio. Nomes comerciais: Yasmin®, Diane®, Belara®, Ciclo21®.',
  'O gene F5, no cromossomo 1, codifica o Fator V da coagulação. A mutação Leiden (rs6025 G>A) causa resistência à proteína C ativada e risco 3-8x de trombose venosa. Com estrogênio, o risco é sinérgico (7-30x). Eric: F5 CC (referência) = SEM Fator V Leiden.',
  'rs6025', '1', 'F5', 'C,C',
  'Europeia',
  'Eric: F5 CC = SEM Fator V Leiden. Risco trombótico com estrogênio é o PADRÃO populacional (não aumentado geneticamente).',
  'F5 CC: Sem Fator V Leiden. Se anticoncepção hormonal ou terapia estrogênica forem necessárias no futuro, não há contraindicação farmacogenética por esta via. Risco trombótico é o padrão da população. Considerar outros fatores de risco (obesidade, imobilização, cirurgia). PARA A CIRURGIA MAXILAR: Se Eric estiver em uso de qualquer medicamento estrogênico, informar cirurgião. Nunca inicie, interrompa ou altere tratamentos sem orientação médica.',
  'Yasmin, Diane, Belara, Ciclo21',
  '1A',
  'Fator V Leiden e estrogênio: evidência MUITO forte (1A). Eric CC = sem risco genético adicional.'
)
ON DUPLICATE KEY UPDATE
  class_id=VALUES(class_id), description=VALUES(description), understanding_result=VALUES(understanding_result),
  snp_id=VALUES(snp_id), chromosome=VALUES(chromosome), gene_name=VALUES(gene_name),
  patient_genotype=VALUES(patient_genotype), study_population=VALUES(study_population),
  genotype_result=VALUES(genotype_result), suggestions=VALUES(suggestions),
  commercial_names=VALUES(commercial_names), confidence_level=VALUES(confidence_level), evidence_notes=VALUES(evidence_notes);

-- 9. ANLODIPINO
INSERT INTO pgx_drug_details (drug