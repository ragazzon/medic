-- SEED: Regras Farmacogenomicas (panel_id=1)

INSERT INTO `pgx_rules` (`panel_id`,`gene_symbol`,`rsid`,`variant_name`,`ref_genotype`,`het_genotypes`,`risk_genotypes`,`phenotype_normal`,`phenotype_het`,`phenotype_risk`,`clinical_significance`,`evidence_level`,`description_technical`,`description_practical`,`recommendations`,`source`) VALUES
-- CYP1A2
(1,'CYP1A2','rs762551','*1F (A>C)','CC','AC','AA','Metabolizador normal','Met intermediario (induzivel)','Met ultra-rapido (induzivel)','moderate','2A','CYP1A2*1F confere alta indutibilidade. Homozigotos AA metabolizam substratos muito rapido quando induzidos por fumo/grelhados.','Melatonina e cafeina sao processadas mais rapido. Melatonina pode nao durar a noite toda.','Melatonina: usar liberacao prolongada. Cafeina: efeito moderado a fraco.','PharmGKB'),

-- CYP2B6
(1,'CYP2B6','rs3745274','*6 (G516T)','GG','GT','TT','Met normal','Met intermediario','Met lento','moderate','2A','CYP2B6*6 reduz atividade. Homozigotos TT tem metabolismo muito lento de efavirenz e bupropiona.','Bupropiona e processada mais devagar.','Se *6/*6: reduzir dose efavirenz. Bupropiona pode acumular.','PharmGKB'),

-- CY