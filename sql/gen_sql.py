# Combines gen_rules.py + gen_rules_b.py and outputs SQL
import os, sys
os.chdir(os.path.dirname(os.path.abspath(__file__)))

rules = []
def R(p,g,r,v,ref,het,risk,pn,ph,pr,cs,ev,rec):
    rules.append((p,g,r,v,ref,het,risk,pn,ph,pr,cs,ev,rec))

exec(open("gen_rules.py").read())
exec(open("gen_rules_b.py").read())

def esc(s):
    return s.replace("'","''") if s else ''

with open("genomic_seed_rules.sql","w",encoding="utf-8") as f:
    f.write("-- AUTO-GENERATED PGX RULES SEED\n")
    f.write("-- Total rules: %d\n\n" % len(rules))
    for r in rules:
        p,g,rs,v,ref,het,risk,pn,ph,pr,cs,ev,rec = r
        f.write("INSERT INTO pgx_rules (panel_id,gene_symbol,rsid,variant_name,ref_genotype,het_genotypes,risk_genotypes,phenotype_normal,phenotype_het,phenotype_risk,clinical_significance,evidence_level,recommendations,source) VALUES ")
        f.write("(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','curated');\n" % (
            p,esc(g),esc(rs),esc(v),esc(ref),esc(het),esc(risk),esc(pn),esc(ph),esc(pr),esc(cs),esc(ev),esc(rec)))
    f.write("\n-- Drug-Gene interactions\n")
    
    # Drug-gene interactions
    drugs = [
        ('Escitalopram','ISRS','CYP2C19','rs12248560','substrate','Niveis ~30% menores com *17','Dose padrao','Dosar nivel serico','Considerar alternativa'),
        ('Sertralina','ISRS','CYP2C19','rs12248560','substrate','Niveis ~40% menores com *17','Dose padrao','Dose maior ou trocar','Trocar farmaco'),
        ('Clopidogrel','Antiplaquetario','CYP2C19','rs4244285','substrate','Ativacao reduzida com *2','Normal','Prasugrel/ticagrelor','Prasugrel/ticagrelor'),
        ('Omeprazol','IBP','CYP2C19','rs12248560','substrate','Menos eficaz com *17','Normal','Dose maior','Trocar IBP'),
        ('Atomoxetina','TDAH','CYP2D6','rs3892097','substrate','Acumula com *4','Dose padrao','Reduzir dose','Alternativa'),
        ('Metilfenidato','TDAH','CES1','rs2244613','substrate','Duracao alterada','Normal','Monitorar','Ajustar formulacao'),
        ('Melatonina','Sono','CYP1A2','rs762551','substrate','Meia-vida reduzida com *1F','Dose padrao','Lib prolongada','Lib prolongada + dose maior'),
        ('Varfarina','Anticoagulante','CYP2C9','rs1799853','substrate','Dose menor com *2','Dose padrao','Reduzir 25%','Reduzir 50%'),
        ('Varfarina','Anticoagulante','VKORC1','rs9923231','target','Sensibilidade aum','Dose padrao','Reduzir 25%','Reduzir 50%'),
        ('Sinvastatina','Estatina','SLCO1B1','rs4149056','transporter','Risco miopatia','Dose padrao','Max 20mg','Trocar estatina'),
        ('Canabidiol','Canabinoide','CYP2C19',None,'inhibitor','INIBE CYP2C19. Compensa *17.','N/A','N/A','N/A'),
        ('Canabidiol','Canabinoide','CYP2D6',None,'inhibitor','INIBE CYP2D6. Aumenta substratos.','N/A','N/A','N/A'),
        ('Canabidiol','Canabinoide','CYP3A4',None,'inhibitor','Substrato e inibidor leve.','N/A','N/A','N/A'),
        ('Valproato','Antiepileptico','UGT1A6','rs2070959','substrate','Met alterado','Dose padrao','Dosar niveis','Dosar niveis'),
        ('Lamotrigina','Antiepileptico','UGT1A4',None,'substrate','Met pode ser alterado','Dose padrao','Dosar niveis','Dosar niveis'),
        ('Lorazepam','Benzodiazepínico','UGT2B15','rs1902023','substrate','Met reduzido com *2','Dose padrao','Dose menor','Dose muito menor'),
        ('5-Fluorouracil','Quimioterapico','DPYD','rs3918290','substrate','TOXICIDADE FATAL','Dose padrao','CONTRA-INDICADO','CONTRA-INDICADO'),
        ('Azatioprina','Imunossupressor','TPMT','rs1142345','substrate','Mielossupressao','Dose padrao','Reduzir 50%','Reduzir 90%'),
        ('Tacrolimus','Imunossupressor','CYP3A5','rs776746','substrate','Expressores: dose maior','Dose padrao','Dose 1.5x','Dose 2x'),
        ('Ibuprofeno','AINE','CYP2C9','rs1799853','substrate','Met reduzido','Normal','Cuidado','Evitar dose alta'),
        ('Codeina','Opioide','CYP2D6','rs3892097','substrate','Conversao reduzida','Normal','Alternativa','Tramadol ou morfina'),
        ('Fluoxetina','ISRS','CYP2D6','rs3892097','substrate','Acumula com *4','Normal','Reduzir dose','Alternativa'),
    ]
    
    for d in drugs:
        name,cls,gene,rsid,itype,desc,rn,rh,rr = d
        rsid_val = "'%s'" % rsid if rsid else 'NULL'
        f.write("INSERT INTO pgx_drug_genes (drug_name,drug_class,gene_symbol,rsid,interaction_type,effect_description,recommendation_normal,recommendation_het,recommendation_risk,source) VALUES ")
        f.write("('%s','%s','%s',%s,'%s','%s','%s','%s','%s','curated');\n" % (
            esc(name),esc(cls),esc(gene),rsid_val,itype,esc(desc),esc(rn),esc(rh),esc(rr)))

print("Generated genomic_seed_rules.sql with %d rules and %d drug interactions" % (len(rules), len(drugs)))