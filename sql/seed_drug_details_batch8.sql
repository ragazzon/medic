-- =============================================
-- MEDIC - Seed de Detalhes de Medicamentos (Lote 8 de N)
-- 10 medicamentos: Oxicodona → Norfloxacina
-- Pode ser rodado múltiplas vezes com segurança
-- =============================================

SET NAMES utf8mb4;

-- =============================================
-- PARTE A: Inserir medicamentos na pgx_drug_genes
-- =============================================

INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`) VALUES
('Oxicodona', 'Analgésicos Opioides', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 converte oxicodona em oximorfona (potente) - NÃO é essencial', 'Dose padrão (funciona sem CYP2D6)', 'Monitorar', 'Efeito analgésico preservado', '2A', 'CPIC', 1),
('Oxcarbazepina', 'Anticonvulsivantes', 'HLA-B', 'rs3909184', 'risk', 'HLA-B*15:02 associado a SJS/TEN - risco em asiáticos', 'Sem risco (europeus)', 'Baixo risco', 'Tipagem HLA obrigatória em asiáticos', '1A', 'CPIC', 1),
('Oxazepam', 'Ansiolíticos (Benzodiazepínicos)', 'UGT2B15', 'rs1902023', 'substrate', 'UGT2B15 metaboliza oxazepam por glucuronidação', 'Dose padrão', 'Metabolismo reduzido', 'Meia-vida prolongada', '2B', 'PharmGKB', 1),
('Oxaliplatina', 'Antineoplásicos', 'GSTP1', 'rs1695', 'target', 'GSTP1 I105V modula resposta/toxicidade à oxaliplatina', 'Resposta e toxicidade padrão', 'Resposta variável', 'Possível melhor resposta mas mais toxicidade', '2B', 'PharmGKB', 1),
('Ondansetrona', 'Antieméticos', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza ondansetrona - ultra-rápidos têm eficácia reduzida', 'Dose padrão', 'Monitorar eficácia', 'Eficácia possivelmente reduzida em ultra-rápidos', '2B', 'DPWG', 1),
('Omeprazol', 'IBPs (Inibidores de Bomba de Prótons)', 'CYP2C19', 'rs12248560', 'substrate', 'CYP2C19 é via PRINCIPAL - *17 reduz eficácia significativamente', 'Dose padrão', 'Eficácia reduzida - dose maior ou alternativa', 'Eficácia muito reduzida', '1A', 'CPIC', 1),
('Olanzapina', 'Antipsicóticos', 'CYP1A2', 'rs762551', 'substrate', 'CYP1A2 metaboliza olanzapina - ultra-rápidos eliminam mais rápido', 'Dose padrão', 'Metabolismo aumentado - pode necessitar dose maior', 'Eficácia possivelmente reduzida', '2A', 'PharmGKB', 1),
('Olanzapina', 'Antipsicóticos', 'MC4R', 'rs17782313', 'risk', 'MC4R modula ganho de peso com olanzapina', 'Sem risco aumentado', 'Risco moderado', 'Risco alto', '2B', 'PharmGKB', 1),
('Ofloxacina', 'Antibióticos (Fluoroquinolonas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - fluoroquinolonas podem causar hemólise', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1),
('Nortriptilina', 'Antidepressivos (Tricíclicos)', 'CYP2D6', 'rs3892097', 'substrate', 'CYP2D6 metaboliza nortriptilina - CPIC guideline disponível', 'Dose padrão', 'Monitorar níveis', 'Reduzir dose 50% ou alternativa', '1A', 'CPIC', 1),
('Nortriptilina', 'Antidepressivos (Tricíclicos)', 'BDNF', 'rs6265', 'target', 'BDNF Val66Met modula resposta a antidepressivos', 'Normal', 'Secreção reduzida', 'Resposta reduzida', '2B', 'PharmGKB', 1),
('Norfloxacina', 'Antibióticos (Fluoroquinolonas)', 'G6PD', 'rs1050829', 'risk', 'G6PD deficiente - risco de hemólise com fluoroquinolonas', 'Sem risco', 'Cautela', 'Monitorar', '3', 'PharmGKB', 1);

-- =============================================
-- PARTE B: Detalhes dos medicamentos
-- =============================================

-- 1. OXICODONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Oxicodona', (SELECT id FROM pgx_drug_classes WHERE code='analgesico' LIMIT 1),
    'OxyContin®, Oxygesic®, Oxypynal®',
    'A oxicodona é um opioide semi-sintético potente utilizado para dor moderada a intensa. Diferente do tramadol/codeína, a oxicodona NÃO depende criticamente do CYP2D6 para analgesia — o composto original já é ativo. O CYP2D6 converte parte em oximorfona (mais potente), mas isso não é essencial para o efeito. Efeitos adversos incluem constipação, náuseas, sedação, depressão respiratória e dependência.',
    'CYP2D6 (rs3892097) N/D. A oxicodona NÃO depende criticamente do CYP2D6 para funcionar (diferente do tramadol). O composto original é ativo. OPRM1 (AA) = receptor normal. COMT (AG) = resposta intermediária.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 e Oxicodona:\n- A oxicodona funciona INDEPENDENTE do CYP2D6\n- CYP2D6 converte em oximorfona (potente) mas não é essencial\n- Diferente do TRAMADOL que NÃO funciona sem CYP2D6\n\nOPRM1 rs1799971: AA = Receptor normal\nCOMT rs4680: AG = Resposta intermediária a opioides',
    'RELEVANTE PARA CIRURGIA MAXILAR: Oxicodona é opção SEGURA para analgesia pós-operatória. NÃO depende criticamente do CYP2D6 (desconhecido). OPRM1 AA = receptor normal. COMT AG = pode necessitar dose ligeiramente maior. Alternativa à morfina com perfil oral mais previsível.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 2. OXCARBAZEPINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Oxcarbazepina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Trileptal®, Alzepinol®, Oleptal®, Oxcarb®',
    'A oxcarbazepina é um anticonvulsivante utilizado para epilepsia parcial e neuralgia do trigêmeo. É pró-droga convertida em licarbazepina. O HLA-B*15:02 está associado a síndrome de Stevens-Johnson (SJS/TEN) — o risco é principalmente em populações asiáticas. Em europeus o risco é muito baixo. Efeitos adversos incluem hiponatremia, tontura, sonolência, diplopia e erupção cutânea.',
    'O HLA-B*15:02 não pode ser determinado com certeza pelo chip GSA v3.0 (requer tipagem HLA direta). Proxy rs3909184 GG sugere NEGATIVO. Em pacientes de ancestralidade europeia (92%), o risco de SJS com oxcarbazepina é MUITO BAIXO (<0.01%).',
    'rs3909184', '6', 'HLA-B', 'Europeia (risco SJS muito baixo em europeus)',
    'HLA-B*15:02 e Oxcarbazepina:\n- Europeus: Risco de SJS muito baixo independente do HLA\n- Asiáticos: CPIC exige tipagem HLA antes de prescrever\n- Proxy rs3909184 GG sugere negativo mas NÃO substitui tipagem',
    'Ancestralidade 92% europeia → risco de SJS com oxcarbazepina é MUITO BAIXO. Proxy genético sugere HLA-B*15:02 negativo. Para 100% certeza, tipagem HLA direta pode ser considerada mas não é obrigatória em europeus.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 3. OXAZEPAM
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Oxazepam', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Serax®',
    'O oxazepam é um benzodiazepínico de ação intermediária utilizado para ansiedade e abstinência alcoólica. É metabolizado por glucuronidação (UGT2B15) — NÃO depende de CYP450. Isso o torna opção com menos interações medicamentosas. Efeitos adversos incluem sedação, confusão, ataxia e dependência.',
    'UGT2B15 (rs1902023) N/D — SNP não disponível no chip. O oxazepam NÃO depende do CYP450 (vantagem farmacogenética).',
    'rs1902023', '4', 'UGT2B15', 'Europeia',
    'UGT2B15 rs1902023 (D85Y):\n- SNP não disponível no chip\n\nNota: Oxazepam NÃO depende de CYP2D6, CYP3A4 ou CYP2C19.',
    'UGT2B15 indeterminado. O oxazepam tem vantagem farmacogenética: NÃO depende de CYP450. É alternativa aos benzodiazepínicos metabolizados pelo CYP3A4 (alprazolam, midazolam) quando se quer evitar interações.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 4. OXALIPLATINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Oxaliplatina', (SELECT id FROM pgx_drug_classes WHERE code='onco' LIMIT 1),
    'Bioezulen®, Collectro®, Oxa-Platin®',
    'A oxaliplatina é um quimioterápico de platina utilizado para câncer colorretal (FOLFOX), gástrico e pancreático. O GSTP1 modula a detoxificação e pode influenciar resposta/toxicidade. Efeitos adversos incluem neuropatia periférica (dose-limitante), mielossupressão, náuseas e reações alérgicas.',
    'GSTP1 (rs1695 AA = Ile/Ile) = detoxificação NORMAL. Resposta e toxicidade padrão à oxaliplatina.',
    'rs1695', '11', 'GSTP1', 'Europeia',
    'GSTP1 rs1695 (I105V):\n- AA (Ile/Ile) = Normal (detoxificação padrão)\n- AG (Ile/Val) = Detoxificação reduzida (resposta/toxicidade variável)\n- GG (Val/Val) = Muito reduzida (possível melhor resposta mas mais toxicidade)',
    'GSTP1 AA (Ile/Ile) = normal. Resposta e toxicidade padrão conforme protocolo oncológico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 5. ONDANSETRONA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ondansetrona', (SELECT id FROM pgx_drug_classes WHERE code='gastro' LIMIT 1),
    'Vonau®, Enavo®, Jofix®, Listo®, Bienn®',
    'A ondansetrona é um antiemético antagonista 5-HT3 utilizado para náuseas/vômitos induzidos por quimioterapia, radioterapia e pós-operatórios. É metabolizada pelo CYP2D6. Diferente da tropisetrona, é MENOS dependente do CYP2D6. Efeitos adversos incluem cefaleia, constipação, prolongamento QTc (doses altas IV) e fadiga.',
    'CYP2D6 (rs3892097) N/D. A ondansetrona é MENOS dependente do CYP2D6 que a tropisetrona. É opção preferencial como antiemético quando CYP2D6 é desconhecido.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 e Ondansetrona:\n- Menos dependente do CYP2D6 que tropisetrona\n- Ultra-rápidos podem ter eficácia levemente reduzida\n- Em geral, funciona bem independente do CYP2D6',
    'RELEVANTE PARA CIRURGIA MAXILAR: Ondansetrona é a MELHOR escolha como antiemético pós-operatório por ser MENOS dependente do CYP2D6 (desconhecido). Preferir sobre tropisetrona. Dose padrão 4-8mg IV/VO.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 6. OMEPRAZOL
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Omeprazol', (SELECT id FROM pgx_drug_classes WHERE code='gastro' LIMIT 1),
    'Omeprazol®, Peprazol®, Gastrium®, Gaspiren®, Somedini®',
    'O omeprazol é o IBP mais prescrito, utilizado para DRGE, úlcera péptica e erradicação do H. pylori. É o IBP MAIS dependente do CYP2C19. Em metabolizadores rápidos (*1/*17), a eficácia é SIGNIFICATIVAMENTE reduzida — o medicamento é eliminado antes de fazer efeito completo. O CPIC recomenda dose maior ou troca para metabolizadores rápidos. Efeitos adversos incluem cefaleia, diarreia, deficiência de B12/Mg a longo prazo.',
    'CYP2C19 *1/*17 (CT) = METABOLIZADOR RÁPIDO. O omeprazol é eliminado mais rapidamente, com EFICÁCIA REDUZIDA. A Genera também identificou este achado. O CPIC recomenda considerar dose maior ou trocar por rabeprazol.',
    'rs12248560', '10', 'CYP2C19', 'Europeia',
    'CYP2C19 e Omeprazol (CPIC guideline):\n- *1/*1 (CC) = Normal → Dose padrão\n- *1/*17 (CT) = Metabolizador rápido → EFICÁCIA REDUZIDA. Dose maior ou alternativa\n- *17/*17 (TT) = Ultra-rápido → Trocar por rabeprazol\n- *1/*2 ou *2/*2 = Lento → Eficácia AUMENTADA (pode reduzir dose)',
    'ACHADO CONFIRMADO (Genera + nosso): CYP2C19 *1/*17 = metabolizador rápido. Omeprazol tem eficácia REDUZIDA. RECOMENDAÇÕES: 1) PREFERIR RABEPRAZOL (menos dependente do CYP2C19), 2) Se usar omeprazol, dose MAIOR (40mg em vez de 20mg), 3) Para erradicação de H. pylori, considerar terapia intensificada.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 7. OLANZAPINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Olanzapina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Ziprexa®, Neupine®',
    'A olanzapina é um antipsicótico atípico utilizado para esquizofrenia, transtorno bipolar e como adjuvante em depressão. É o antipsicótico com MAIOR risco de ganho de peso e síndrome metabólica. É metabolizada pelo CYP1A2 (via principal). MC4R modula ganho de peso. Efeitos adversos incluem ganho de peso significativo, síndrome metabólica, sedação, hipotensão e diabetes.',
    'CYP1A2 (rs762551 CA) = METABOLIZADOR ULTRA-RÁPIDO. A olanzapina pode ser eliminada mais rapidamente, necessitando dose maior. MC4R (TT) = sem risco genético ADICIONAL de obesidade (mas olanzapina causa ganho de peso independente da genética). HTR2C (CC) = risco padrão.',
    'rs762551', '15', 'CYP1A2', 'Europeia',
    'CYP1A2 rs762551 (*1F) e Olanzapina:\n- CC = Normal\n- CA = Ultra-rápido (pode necessitar dose maior)\n- AA = Ultra-rápido\n\nMC4R rs17782313: TT = Sem risco GENÉTICO adicional de obesidade\nHTR2C rs3813929: CC = Risco padrão\n\nNota: Olanzapina causa ganho de peso em TODOS os genótipos.',
    'CYP1A2 CA = ultra-rápido. Olanzapina pode necessitar dose ligeiramente maior. MC4R e HTR2C normais MAS a olanzapina causa ganho de peso significativo independente da genética. Se prescrita para autismo, monitorar peso RIGOROSAMENTE. Considerar aripiprazol como alternativa com menor impacto metabólico.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 8. OFLOXACINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Ofloxacina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Oflox®, Nostil®, Ofloxino®',
    'A ofloxacina é um antibiótico fluoroquinolona utilizado para infecções urinárias, respiratórias e oculares. Pode causar hemólise em deficientes de G6PD (evidência limitada). Efeitos adversos incluem tendinopatia, fotossensibilidade, neuropatia, prolongamento QTc e ruptura de tendão.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829: TT = Normal',
    'G6PD normal. Ofloxacina pode ser usada sem risco de hemólise. Nota: fluoroquinolonas têm restrições de uso em adolescentes pelo risco de tendinopatia.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 9. NORTRIPTILINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Nortriptilina', (SELECT id FROM pgx_drug_classes WHERE code='psiq_neuro' LIMIT 1),
    'Pamelor®, Aventyl Hydrochloride®',
    'A nortriptilina é um antidepressivo tricíclico (metabólito ativo da amitriptilina) utilizado para depressão, dor neuropática, enxaqueca profilática e TDAH (off-label). É metabolizada pelo CYP2D6. O CPIC tem guideline específico. BDNF modula resposta. Efeitos adversos incluem boca seca, constipação, retenção urinária, ganho de peso, sedação e arritmias.',
    'CYP2D6 (rs3892097) N/D. CPIC recomenda: normais=dose padrão, lentos=reduzir 50%. BDNF (rs6265 CC) = Val/Val = secreção NORMAL de BDNF — favorável para resposta a antidepressivos.',
    'rs3892097', '22', 'CYP2D6', 'Europeia',
    'CYP2D6 rs3892097 (*4):\n- GG = Normal (dose padrão com nível sérico alvo 50-150ng/mL)\n- GA = Intermediário\n- AA = Metabolizador lento (reduzir dose 50% - CPIC)\n\nBDNF rs6265 (Val66Met):\n- CC (Val/Val) = Secreção normal de BDNF (favorável)\n- CT = Secreção reduzida\n- TT = Muito reduzida',
    'CYP2D6 indeterminado. BDNF CC (Val/Val) = favorável para resposta. Se nortriptilina for prescrita (dor neuropática, enxaqueca, TDAH), iniciar dose baixa e titular. Monitoramento de nível sérico (50-150ng/mL) recomendado pelo CPIC. ECG obrigatório em tricíclicos.',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();

-- 10. NORFLOXACINA
INSERT INTO `pgx_drug_details` (`drug_name`, `class_id`, `commercial_names`, `description`, `understanding_result`, `snp_rsid`, `chromosome`, `gene_symbol`, `study_population`, `genotype_results`, `suggestions`, `disclaimer`, `is_active`)
VALUES (
    'Norfloxacina', (SELECT id FROM pgx_drug_classes WHERE code='infecto' LIMIT 1),
    'Floxacin®, Surfest®, Miuron®, Norf®',
    'A norfloxacina é um antibiótico fluoroquinolona utilizado para infecções urinárias e gastrointestinais. Pode causar hemólise em deficientes de G6PD. Efeitos adversos incluem tendinopatia, fotossensibilidade, neuropatia e distúrbios GI.',
    'G6PD (rs1050829 TT) = NORMAL. Sem risco de hemólise.',
    'rs1050829', 'X', 'G6PD', 'Europeia',
    'G6PD rs1050829: TT = Normal',
    'G6PD normal. Norfloxacina pode ser usada sem risco de hemólise. Nota: fluoroquinolonas têm restrições em adolescentes (tendinopatia).',
    'Nunca inicie, interrompa ou altere tratamentos sem orientação médica.', 1
) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id), commercial_names=VALUES(commercial_names), description=VALUES(description), understanding_result=VALUES(understanding_result), snp_rsid=VALUES(snp_rsid), chromosome=VALUES(chromosome), gene_symbol=VALUES(gene_symbol), study_population=VALUES(study_population), genotype_results=VALUES(genotype_results), suggestions=VALUES(suggestions), disclaimer=VALUES(disclaimer), updated_at=NOW();
