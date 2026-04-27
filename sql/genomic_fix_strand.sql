
-- Fix strand-complement rules (Genera reports forward strand)
-- CYP3A4 rs35599367: chip=GG means ref on complement strand
UPDATE pgx_rules SET ref_genotype='CC,GG', het_genotypes='CT,AG,GA,TC', risk_genotypes='TT,AA' WHERE rsid='rs35599367';
-- CYP3A4 rs2740574: chip=TT means ref on complement
UPDATE pgx_rules SET ref_genotype='AA,TT', het_genotypes='AG,TC,GA,CT', risk_genotypes='GG,CC' WHERE rsid='rs2740574';
-- CYP3A5 rs776746: chip=AG
UPDATE pgx_rules SET ref_genotype='TT,AA', het_genotypes='CT,AG,GA,TC', risk_genotypes='CC,GG' WHERE rsid='rs776746';
-- DPYD rs67376798: chip=TT could be complement of AA=ref
UPDATE pgx_rules SET ref_genotype='AA,TT', het_genotypes='AT,TA', risk_genotypes='TT' WHERE rsid='rs67376798';
-- COL1A1 rs1800012: chip=CC
UPDATE pgx_rules SET ref_genotype='GG,CC', het_genotypes='GT,CA,AC,TG', risk_genotypes='TT,AA' WHERE rsid='rs1800012';
-- CLOCK rs1801260: chip=AG
UPDATE pgx_rules SET ref_genotype='TT,AA', het_genotypes='TC,AG,GA,CT', risk_genotypes='CC,GG' WHERE rsid='rs1801260';
-- GDF5 rs143383: chip=AG
UPDATE pgx_rules SET ref_genotype='CC,GG', het_genotypes='CT,AG,GA,TC', risk_genotypes='TT,AA' WHERE rsid='rs143383';
-- ACTN3 rs1815739: chip=CT already matches
-- SLC6A4 rs25531: chip=AG already matches het
-- TCF7L2 rs7903146: chip=CT already matches het
