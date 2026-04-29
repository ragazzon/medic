import os

os.chdir(r'c:\Dev_Projects\Medicina_Familiar\medic\sql')
out = "-- BATCHES 11-20: PARTE A LIMPA\n-- Rode este arquivo para medicamentos 111-200 no dashboard\n\nSET NAMES utf8mb4;\n\n"

for i in range(11, 21):
    fname = f'seed_drug_details_batch{i}.sql'
    if not os.path.exists(fname):
        continue
    with open(fname, 'r', encoding='utf-8') as f:
        c = f.read()
    s = c.find('INSERT IGNORE INTO')
    if s == -1:
        continue
    e = c.find(';', s)
    if e == -1:
        continue
    out += f'-- BATCH {i}\n' + c[s:e+1] + '\n\n'

with open('seed_batch11to20_clean.sql', 'w', encoding='utf-8') as f:
    f.write(out)
print(f'OK: {len(out)} bytes written to seed_batch11to20_clean.sql')