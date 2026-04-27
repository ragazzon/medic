# Part B: Onco, Nutri, Musculo, Derma, Immuno, Endocrino, Sleep rules
# This file is appended to gen_rules.py data

# ONCO continued (4)
R(4,'RARG','rs2229774','Ser427Leu','CC','CT','TT','Normal','Risco cardiotox','Alto risco cardiotox','moderate','2B','Monitorar funcao cardiaca antraciclinas.')
R(4,'SLC28A3','rs7853758','L461L','CC','CT','TT','Normal','Variante protetora','Protetor','low','3','Variante protetora cardiotox.')

# NUTRI (5)
R(5,'MTHFR','rs1801133','C677T','GG','AG','AA','Normal','Ativ ~65% (het)','Ativ ~30% (hom)','high','1A','L-metilfolato. Dosar homocisteina.')
R(5,'MTHFR','rs1801131','A1298C','AA','AC','CC','Normal','Ativ levemente reduz','Ativ reduzida','moderate','2A','L-metilfolato se duplo het.')
R(5,'MTRR','rs1801394','A66G','AA','AG','GG','Normal','Reciclagem B12 reduz','Muito reduzida','moderate','2B','Dosar B12. Metilcobalamina.')
R(5,'VDR','rs2228570','FokI','CC','CT','TT','Normal','Resp vit D var','Resp vit D alt','moderate','3','Dosar 25-OH vit D.')
R(5,'FUT2','rs601338','Secretor','GG','GA','AA','Secretor','Secretor','Nao-secretor','moderate','2B','Nao-secretor: menor absorcao B12 dieta.')
R(5,'BCMO1','rs12934922','Intronico','TT','AT','AA','Normal','Conversao caroteno reduz','Muito reduzida','low','3','Preferir vit A pre-formada (retinol).')
R(5,'MCM6','rs4988235','Lactase','CC','CT','TT','Intolerante lactose','Tolerante (het)','Tolerante (hom)','moderate','2A','CC: intolerancia lactose provavel.')
R(5,'HFE','rs1800562','C282Y','GG','GA','AA','Normal','Portador hemocromatose','Risco hemocromatose','high','1A','Dosar ferritina e saturacao transferrina.')

# MUSCULO (6)
R(6,'ACTN3','rs1815739','R577X','CC','CT','TT','Fibras rapidas (potencia)','Misto','Fibras lentas (resistencia)','low','3','CC: esportes potencia. TT: esportes resistencia.')
R(6,'COL1A1','rs1800012','Sp1','GG','GT','TT','Normal','Risco osteoporose leve','Risco osteoporose','moderate','3','Suplementar calcio e vit D.')
R(6,'COL5A1','rs12722','BstUI','CC','CT','TT','Normal','Flex aumentada','Muito flexivel','low','3','Hipermobilidade. Cuidado lesoes.')
R(6,'GDF5','rs143383','Intronico','CC','CT','TT','Normal','Risco osteoartrite','Alto risco','moderate','2B','Manter peso adequado.')

# DERMA (7)
R(7,'MC1R','rs1805007','R151C','CC','CT','TT','Normal','Pele clara/sardas','Ruivismo/melanoma','moderate','2B','Protetor solar rigoroso.')
R(7,'MC1R','rs1805008','R160W','CC','CT','TT','Normal','Pele clara','Alto risco melanoma','moderate','2B','Protecao solar FPS50+.')
R(7,'OCA2','rs12913832','Cor olhos','AA','AG','GG','Olhos escuros','Variavel','Olhos claros','informational','3','Cor olhos determinada.')
R(7,'SLC45A2','rs16891982','Pigmentacao','GG','GC','CC','Pele escura','Intermediaria','Pele clara','informational','3','Pigmentacao pele.')

# IMMUNO (8)
R(8,'HLA-B','rs2395029','B*5701 proxy','TT','TG','GG','Negativo','Positivo','Positivo','high','1A','Se positivo: CONTRA-INDICAR abacavir.')
R(8,'BDKRB1','rs12050217','Promotor','AA','AG','GG','Normal','Risco tosse iECA','Alto risco tosse','moderate','3','Preferir BRAs a iECAs.')

# ENDOCRINO (9)
R(9,'TCF7L2','rs7903146','Intronico','CC','CT','TT','Normal','Risco DM2 aum 40%','Risco DM2 aum 80%','moderate','1A','Controle glicemico. Dieta low-carb.')
R(9,'PPARG','rs1801282','Pro12Ala','CC','CG','GG','Normal','Sensib insulina aum','Muito sensivel','low','3','Ala carriers: protecao DM2.')
R(9,'MC4R','rs17782313','Near MC4R','TT','CT','CC','Normal','Risco obesidade','Alto risco','moderate','2B','Controle apetite.')
R(9,'DIO2','rs225014','Thr92Ala','CC','CT','TT','Normal','Conversao T4-T3 reduz','Muito reduzida','moderate','3','Se hipotireoide: considerar T3.')
R(9,'G6PD','rs1050829','N126D','CC','CT','TT','Normal','G6PD variante','G6PD deficiente','moderate','2B','Dosar atividade G6PD.')

# SLEEP (10)
R(10,'PER2','rs2304672','Intronico','CC','CG','GG','Cronotipo normal','Tendencia matutino','Muito matutino','low','3','Cronotipo.')
R(10,'CLOCK','rs1801260','3111T/C','TT','TC','CC','Normal','Tendencia vespertino','Muito vespertino','low','3','Horario sono ajustar.')
R(10,'ADA','rs73598374','Asp8Asn','CC','CT','TT','Normal','Sono mais profundo','Sono muito profundo','low','3','Variante benef sono.')