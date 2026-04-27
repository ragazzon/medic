
-- DPYD rs67376798: Genera TT = hom ALT = RISK (correct)
-- But ref should only be AA (hom ref) not TT
UPDATE pgx_rules SET ref_genotype='AA', het_genotypes='AT,TA', risk_genotypes='TT' WHERE rsid='rs67376798';

-- G6PD rs1050829: Genera TT = hom ALT = variant
-- ref=CC(ref allele C), het=CT, risk=TT
-- This is CORRECT as-is for forward strand
-- BUT: need to check if Eric's actual genotype from debug was CT not TT
-- Let me not change this one

-- CES1 rs2244613: het=CT, risk=TT - this seems correct
-- ACE rs4343: risk=GG - Eric is GG which IS risk (elevated ACE)

-- The strand complement changes made ref_genotype have both strands
-- This causes the engine to ALSO match complement of risk genotypes
-- Fix: make the engine prioritize ref > het > risk (not risk first)
