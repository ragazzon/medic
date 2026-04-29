import os, re

sqldir = r'c:\Dev_Projects\Medicina_Familiar\medic\sql'

# The table pgx_drug_genes has these columns:
# drug_name, drug_class, gene_symbol, rsid, interaction_type, 
# effect_description, recommendation_normal, recommendation_het, 
# recommendation_risk, evidence_level, source, is_active

# Batches 21 and 22 incorrectly use: gene_name, effect_allele, recommendation
# Need to fix the PARTE A INSERT statements

files_to_fix = [
    'seed_drug_details_batch21.sql',
    'seed_drug_details_batch22.sql',
]

old_header = "INSERT IGNORE INTO pgx_drug_genes (drug_name, gene_name, rsid, effect_allele, effect_description, recommendation, evidence_level)"

for fname in files_to_fix:
    fpath = os.path.join(sqldir, fname)
    if not os.path.exists(fpath):
        print(f"NOT FOUND: {fname}")
        continue
    
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if old_header not in content:
        print(f"Header not found in {fname}")
        continue
    
    # Find start of INSERT and end (semicolon)
    insert_start = content.find(old_header)
    values_start = content.find("VALUES", insert_start)
    insert_end = content.find(";", values_start)
    
    # Extract each row
    values_section = content[values_start+6:insert_end]  # after "VALUES"
    
    # Parse rows: ('col1', 'col2', ..., 'coln')
    pattern = r"\('((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)',\s*'((?:[^'\\]|\\.|'')*)'\)"
    rows = re.findall(pattern, values_section)
    
    if not rows:
        print(f"No rows parsed in {fname}")
        continue
    
    # Convert: old (name, gene, rsid, allele, desc, rec, level)
    # To new: (name, '', gene, rsid, 'substrate', desc, rec, rec, rec, level, 'curated', 1)
    new_header = "INSERT IGNORE INTO `pgx_drug_genes` (`drug_name`, `drug_class`, `gene_symbol`, `rsid`, `interaction_type`, `effect_description`, `recommendation_normal`, `recommendation_het`, `recommendation_risk`, `evidence_level`, `source`, `is_active`)"
    
    new_rows = []
    for row in rows:
        name, gene, rsid, allele, desc, rec, level = row
        # Escape single quotes in text
        desc_e = desc.replace("'", "''")
        rec_e = rec.replace("'", "''")
        new_row = f"('{name}', '', '{gene}', '{rsid}', 'substrate', '{desc_e}', '{rec_e}', '{rec_e}', '{rec_e}', '{level}', 'curated', 1)"
        new_rows.append(new_row)
    
    new_values = "VALUES\n" + ",\n".join(new_rows) + ";"
    
    # Replace in content
    before = content[:insert_start]
    after = content[insert_end+1:]
    new_content = before + new_header + "\n" + new_values + "\n" + after
    
    with open(fpath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"FIXED {fname}: {len(new_rows)} rows converted")

print("\nDone! Now regenerate seed_ALL_pending.sql")