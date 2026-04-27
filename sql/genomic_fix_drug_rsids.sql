
-- Fix drug-gene rsids for better dashboard display
UPDATE pgx_drug_genes SET rsid='rs3892097' WHERE drug_name='Atomoxetina' AND gene_symbol='CYP2D6' AND rsid IS NULL;
UPDATE pgx_drug_genes SET rsid='rs3892097' WHERE drug_name='Codeina' AND gene_symbol='CYP2D6' AND rsid IS NULL;
UPDATE pgx_drug_genes SET rsid='rs3892097' WHERE drug_name='Fluoxetina' AND gene_symbol='CYP2D6' AND rsid IS NULL;
UPDATE pgx_drug_genes SET rsid='rs1142345' WHERE drug_name='Azatioprina' AND gene_symbol='TPMT' AND rsid IS NULL;
UPDATE pgx_drug_genes SET rsid='rs1902023' WHERE drug_name='Lorazepam' AND gene_symbol='UGT2B15' AND rsid IS NULL;
