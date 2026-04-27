-- =============================================
-- MEDIC - Massa de dados de teste
-- 20 pacientes com prontuários, exames e medicamentos
-- Requer: usuário admin já inserido (seed_admin.sql)
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Obter o ID do admin
SET @admin_id = (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1);
SET @admin_id = COALESCE(@admin_id, 1);

-- =============================================
-- PACIENTES (20)
-- =============================================
INSERT INTO `patients` (`id`,`name`,`birth_date`,`gender`,`cpf`,`blood_type`,`relationship`,`phone`,`email`,`address`,`allergies`,`chronic_conditions`,`medications`,`health_insurance`,`insurance_number`,`notes`,`created_by`,`created_at`) VALUES
(1,'Maria Clara Ragazzon','1985-03-15','F','123.456.789-00','A+','Esposa','(11) 99876-5432','maria.clara@email.com','Rua das Flores, 123 - São Paulo/SP','Dipirona, Látex','Enxaqueca crônica','Topiramato 25mg','Unimed','00123456','Paciente acompanhada desde 2020',@admin_id,'2023-01-10 08:00:00'),
(2,'Pedro Henrique Ragazzon','2015-07-22','M',NULL,'O+','Filho','(11) 99876-5432',NULL,'Rua das Flores, 123 - São Paulo/SP','Amendoim',NULL,NULL,'Unimed','00123457','Acompanhamento pediátrico',@admin_id,'2023-01-10 08:05:00'),
(3,'Ana Luísa Ragazzon','2018-11-03','F',NULL,'A+','Filha','(11) 99876-5432',NULL,'Rua das Flores, 123 - São Paulo/SP',NULL,NULL,NULL,'Unimed','00123458',NULL,@admin_id,'2023-01-10 08:10:00'),
(4,'José Carlos Ragazzon','1955-06-18','M','987.654.321-00','B+','Pai','(11) 98765-4321','jose.carlos@email.com','Av. Brasil, 456 - São Paulo/SP',NULL,'Hipertensão, Diabetes tipo 2','Losartana 50mg, Metformina 850mg','Bradesco Saúde','00234567','Requer acompanhamento frequente',@admin_id,'2023-01-15 09:00:00'),
(5,'Francisca Ragazzon','1958-12-25','F','111.222.333-44','O-','Mãe','(11) 98765-4321','francisca@email.com','Av. Brasil, 456 - São Paulo/SP','Penicilina','Artrite reumatoide','Metotrexato 15mg','Bradesco Saúde','00234568',NULL,@admin_id,'2023-01-15 09:05:00'),
(6,'Lucas Gabriel Silva','1990-08-10','M','222.333.444-55','AB+','Irmão','(11) 97654-3210','lucas.silva@email.com','Rua Palmeiras, 789 - Campinas/SP',NULL,'Asma','Budesonida spray','SulAmérica','00345678',NULL,@admin_id,'2023-02-01 10:00:00'),
(7,'Juliana Costa Mendes','1992-04-30','F','333.444.555-66','A-','Cunhada','(11) 97654-3211','juliana.costa@email.com','Rua Palmeiras, 789 - Campinas/SP','Camarão, Frutos do mar',NULL,NULL,'SulAmérica','00345679',NULL,@admin_id,'2023-02-01 10:05:00'),
(8,'Rafael Oliveira Santos','1982-01-20','M','444.555.666-77','O+','Tio','(11) 96543-2109','rafael.santos@email.com','Rua Augusta, 321 - São Paulo/SP',NULL,'Colesterol alto','Sinvastatina 20mg','Amil','00456789','Fumante, orientado a parar',@admin_id,'2023-02-15 11:00:00'),
(9,'Beatriz Fernandes Lima','1988-09-14','F','555.666.777-88','B-','Tia','(11) 96543-2110','beatriz.lima@email.com','Rua Augusta, 321 - São Paulo/SP','Ibuprofeno','Hipotireoidismo','Levotiroxina 75mcg','Amil','00456790',NULL,@admin_id,'2023-03-01 08:00:00'),
(10,'Mateus Rodrigues Alves','1978-05-05','M','666.777.888-99','A+','Primo','(11) 95432-1098','mateus.alves@email.com','Rua Consolação, 654 - São Paulo/SP',NULL,'Gastrite crônica','Omeprazol 20mg','Porto Seguro','00567890',NULL,@admin_id,'2023-03-10 09:30:00'),
(11,'Carolina Duarte Pereira','2000-02-28','F','777.888.999-00','O+','Prima','(11) 95432-1099','carolina.pereira@email.com','Rua Consolação, 654 - São Paulo/SP','Sulfas',NULL,NULL,'Porto Seguro','00567891','Estudante universitária',@admin_id,'2023-03-10 09:35:00'),
(12,'Fernando Souza Neto','1970-11-11','M','888.999.000-11','AB-','Avô','(11) 94321-0987','fernando.neto@email.com','Rua São Bento, 111 - Santos/SP','AAS','Insuficiência cardíaca, DPOC','Carvedilol 25mg, Furosemida 40mg','Unimed','00678901','Múltiplas comorbidades',@admin_id,'2023-04-01 08:00:00'),
(13,'Mariana Albuquerque Costa','1995-06-20','F','999.000.111-22','B+','Amiga','(11) 94321-0988','mariana.costa@email.com','Rua XV de Novembro, 222 - Curitiba/PR',NULL,'Ansiedade generalizada','Escitalopram 10mg','Hapvida','00789012',NULL,@admin_id,'2023-04-15 10:00:00'),
(14,'Gabriel Martins Rocha','2010-03-08','M',NULL,'O+','Sobrinho','(11) 93210-9876',NULL,'Rua Tiradentes, 333 - São Paulo/SP','Ovo','Rinite alérgica','Loratadina 10mg','Bradesco Saúde','00890123','Criança com alergias alimentares',@admin_id,'2023-05-01 08:30:00'),
(15,'Isabela Moreira Campos','1965-08-17','F','000.111.222-33','A+','Sogra','(11) 93210-9877','isabela.campos@email.com','Av. Paulista, 1000 - São Paulo/SP','Contraste iodado','Osteoporose, Hipertensão','Alendronato 70mg, Enalapril 10mg','Unimed','00901234',NULL,@admin_id,'2023-05-15 09:00:00'),
(16,'Thiago Nascimento Borges','1998-01-30','M','111.333.555-77','B+','Cunhado','(11) 92109-8765','thiago.borges@email.com','Rua Liberdade, 444 - São Paulo/SP',NULL,NULL,NULL,'NotreDame','01012345','Atleta amador',@admin_id,'2023-06-01 10:00:00'),
(17,'Larissa Pinto Azevedo','1993-10-12','F','222.444.666-88','O+','Amiga','(11) 92109-8766','larissa.azevedo@email.com','Rua da Paz, 555 - São Paulo/SP','Glúten','Doença celíaca',NULL,'SulAmérica','01123456','Dieta restritiva obrigatória',@admin_id,'2023-06-15 08:00:00'),
(18,'Eduardo Lima Barbosa','1975-04-02','M','333.555.777-99','A-','Vizinho/Amigo','(11) 91098-7654','eduardo.barbosa@email.com','Rua das Flores, 125 - São Paulo/SP',NULL,'Lombalgia crônica','Pregabalina 75mg','Amil','01234567',NULL,@admin_id,'2023-07-01 09:00:00'),
(19,'Camila Torres Ribeiro','2020-05-18','F',NULL,'O+','Afilhada','(11) 91098-7655',NULL,'Rua São João, 666 - São Paulo/SP',NULL,NULL,NULL,'Unimed','01345678','Bebê, vacinação em dia',@admin_id,'2023-07-15 08:00:00'),
(20,'Roberto Cardoso Vieira','1960-09-28','M','444.666.888-00','AB+','Sogro','(11) 90987-6543','roberto.vieira@email.com','Av. Paulista, 1000 - São Paulo/SP','Dipirona','Diabetes tipo 2, Gota','Insulina Glargina, Alopurinol 300mg','Unimed','01456789','Controle glicêmico rigoroso',@admin_id,'2023-08-01 08:00:00');

-- =============================================
-- PRONTUÁRIOS (~ 4 por paciente)
-- =============================================
INSERT INTO `medical_records` (`patient_id`,`title`,`description`,`diagnosis`,`symptoms`,`prescription`,`doctor_name`,`specialty`,`clinic_hospital`,`record_date`,`category`,`notes`,`created_by`,`created_at`) VALUES
-- Paciente 1
(1,'Consulta rotina','Cansaço e dores de cabeça recorrentes. Exames solicitados.','Enxaqueca crônica, anemia leve','Fadiga, cefaleia, tontura','Sulfato ferroso 40mg, Topiramato 25mg','Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2023-03-15','Consulta','Retorno em 30 dias',@admin_id,'2023-03-15 10:00:00'),
(1,'Retorno exames','Hb 11.2 - anemia leve confirmada.','Anemia ferropriva leve','Melhora parcial','Manter sulfato ferroso 60 dias','Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2023-04-20','Retorno',NULL,@admin_id,'2023-04-20 10:00:00'),
(1,'Neurologia - Enxaqueca','Crises 3x/semana com aura visual.','Enxaqueca com aura','Cefaleia pulsátil, náusea, fotofobia','Topiramato 50mg, Sumatriptano SOS','Dr. Ricardo Bastos','Neurologia','Hosp. Sírio-Libanês','2023-08-22','Consulta',NULL,@admin_id,'2023-08-22 09:00:00'),
(1,'Check-up anual','Exame clínico sem alterações.','Saudável','Nenhum','Exames de rotina','Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2024-03-12','Consulta',NULL,@admin_id,'2024-03-12 10:00:00'),
-- Paciente 2
(2,'Consulta pediátrica','Crescimento adequado. Peso P50, Altura P75.','Saudável','Nenhum','Vitamina D 400UI/dia','Dra. Ana Paula Lima','Pediatria','Clín. Infantil Esperança','2023-02-20','Consulta','Vacinação em dia',@admin_id,'2023-02-20 08:00:00'),
(2,'Urgência - Amigdalite','Febre 38.8°C, amígdalas hipertrofiadas.','Amigdalite bacteriana','Febre, dor de garganta','Amoxicilina 250mg/5ml 8/8h 10 dias','Dra. Ana Paula Lima','Pediatria','PS Infantil','2023-05-14','Emergência',NULL,@admin_id,'2023-05-14 20:00:00'),
(2,'Alergologia - Teste','Prick test positivo amendoim. IgE elevada.','Alergia alimentar','Urticária pós-amendoim','Epinefrina auto-injetável','Dr. Paulo Mendes','Alergologia','Hosp. Infantil Sabará','2024-01-18','Consulta',NULL,@admin_id,'2024-01-18 10:00:00'),
-- Paciente 3
(3,'Puericultura 4 anos','DNPM adequado. Fala fluente.','Saudável','Nenhum',NULL,'Dra. Ana Paula Lima','Pediatria','Clín. Infantil Esperança','2023-01-25','Consulta','Vacinas atualizadas',@admin_id,'2023-01-25 08:00:00'),
(3,'Otite média aguda','Otalgia bilateral, MT hiperemiada.','Otite média aguda bilateral','Dor de ouvido, febre 38.2°C','Amoxicilina+Clavulanato, Paracetamol','Dra. Ana Paula Lima','Pediatria','PS Infantil','2023-07-08','Emergência',NULL,@admin_id,'2023-07-08 15:00:00'),
(3,'Dermatite atópica','Lesões eczematosas em dobras.','Dermatite atópica leve','Coceira, pele ressecada','Hidratante Cetaphil 2x/dia, Dexametasona creme','Dra. Renata Souza','Dermatologia','Clínica DermKids','2024-02-15','Consulta',NULL,@admin_id,'2024-02-15 10:00:00'),
-- Paciente 4
(4,'Cardiologia - Rotina','PA 150/95. ECG com HVE.','Hipertensão estágio 2','Cefaleia, dispneia aos esforços','Losartana 100mg, Anlodipino 5mg, HCTZ 25mg','Dr. Carlos E. Pinto','Cardiologia','InCor','2023-02-10','Consulta','MAPA solicitado',@admin_id,'2023-02-10 09:00:00'),
(4,'Endocrinologia - DM2','HbA1c 8.2%. Glicemia 185.','DM2 descompensado','Polidipsia, poliúria','Metformina 850mg 2x, Glicazida 60mg','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-04-05','Consulta',NULL,@admin_id,'2023-04-05 10:00:00'),
(4,'Retorno Endocrinologia','HbA1c 7.1% - melhora.','DM2 em melhora','Assintomático','Manter esquema','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-07-20','Retorno',NULL,@admin_id,'2023-07-20 10:00:00'),
(4,'Oftalmologia - Fundo de olho','Retinopatia diabética NPDR leve.','Retinopatia diabética','Visão turva','Controle glicêmico rigoroso','Dr. André Yamamoto','Oftalmologia','Hosp. de Olhos','2023-09-15','Exame',NULL,@admin_id,'2023-09-15 14:00:00'),
-- Paciente 5
(5,'Reumatologia','AR atividade moderada. DAS28 4.2.','AR moderada','Dor articular, rigidez matinal','Metotrexato 15mg/sem, Ác. Fólico, Prednisona 5mg','Dr. Roberto Ferreira','Reumatologia','Hosp. das Clínicas','2023-03-08','Consulta',NULL,@admin_id,'2023-03-08 10:00:00'),
(5,'Retorno Reumato','DAS28 3.1 - melhora.','AR leve','Redução rigidez','Reduzir Prednisona 2.5mg','Dr. Roberto Ferreira','Reumatologia','Hosp. das Clínicas','2023-06-12','Retorno',NULL,@admin_id,'2023-06-12 10:00:00'),
(5,'Densitometria','T-score coluna -1.8. Osteopenia.','Osteopenia','Assintomática','Cálcio + Vit D 1000UI','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-09-20','Exame',NULL,@admin_id,'2023-09-20 08:00:00'),
(5,'Reumato - Semestral','DAS28 2.8 - remissão.','AR em remissão','Sem queixas','Suspender Prednisona','Dr. Roberto Ferreira','Reumatologia','Hosp. das Clínicas','2024-01-15','Retorno',NULL,@admin_id,'2024-01-15 10:00:00'),
-- Paciente 6
(6,'Pneumologia - Asma','Espirometria: padrão obstrutivo reversível.','Asma moderada persistente','Dispneia noturna, tosse','Budesonida/Formoterol 200/6, Salbutamol SOS','Dr. Pedro H. Souza','Pneumologia','Hosp. Pulmonar','2023-04-18','Consulta',NULL,@admin_id,'2023-04-18 09:00:00'),
(6,'Retorno Pneumo','PFE 90% - melhora. Sem crises 60 dias.','Asma controlada','Assintomático','Manter tratamento','Dr. Pedro H. Souza','Pneumologia','Hosp. Pulmonar','2023-08-15','Retorno',NULL,@admin_id,'2023-08-15 09:00:00'),
(6,'Check-up esportivo','Apto para atividade física.','Saudável','Nenhum',NULL,'Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2024-01-10','Consulta','Atestado emitido',@admin_id,'2024-01-10 10:00:00'),
-- Paciente 7
(7,'Alergologia','Prick test positivo camarão, lula, caranguejo.','Alergia frutos do mar','Urticária, angioedema','Epinefrina auto-inj, Cetirizina SOS','Dr. Paulo Mendes','Alergologia','Hosp. Albert Einstein','2023-03-22','Consulta',NULL,@admin_id,'2023-03-22 10:00:00'),
(7,'Ginecologia Rotina','Papanicolau normal. US normal.','Sem alterações','Nenhum',NULL,'Dra. Fernanda Oliveira','Ginecologia','Hosp. Albert Einstein','2023-08-05','Consulta',NULL,@admin_id,'2023-08-05 14:00:00'),
(7,'Dermatologia','Dermatite de contato por cosméticos.','Dermatite de contato','Eritema facial, prurido','Hidrocortisona creme 1%','Dra. Renata Souza','Dermatologia','Clínica Derme','2024-02-20','Consulta',NULL,@admin_id,'2024-02-20 11:00:00'),
-- Paciente 8
(8,'Clínica Geral','CT 265, LDL 175, HDL 38.','Dislipidemia mista','Assintomático','Sinvastatina 20mg à noite','Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2023-02-28','Consulta','Cessação tabágica orientada',@admin_id,'2023-02-28 10:00:00'),
(8,'Retorno lipídico','CT 210, LDL 130 - melhora.','Dislipidemia em tratamento','Assintomático','Manter Sinvastatina','Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2023-06-20','Retorno',NULL,@admin_id,'2023-06-20 10:00:00'),
(8,'Pneumo - Tabagismo','Espirometria normal. RX normal.','Tabagismo ativo','Tosse matinal','Vareniclina 1mg 2x/dia 12 sem','Dr. Pedro H. Souza','Pneumologia','Hosp. Pulmonar','2023-09-05','Consulta',NULL,@admin_id,'2023-09-05 09:00:00'),
(8,'Retorno cessação','Não fuma há 60 dias.','Ex-tabagista','Sem tosse','Completar Vareniclina','Dr. Pedro H. Souza','Pneumologia','Hosp. Pulmonar','2024-01-08','Retorno',NULL,@admin_id,'2024-01-08 09:00:00'),
-- Paciente 9
(9,'Endocrinologia','TSH 8.5, T4L 0.7. Hipotireoidismo.','Hipotireoidismo','Ganho de peso, fadiga','Levotiroxina 75mcg em jejum','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-04-10','Consulta',NULL,@admin_id,'2023-04-10 10:00:00'),
(9,'Retorno Endocrinologia','TSH 3.2 - normalizado.','Hipotireoidismo controlado','Assintomática','Manter Levotiroxina 75mcg','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-08-10','Retorno',NULL,@admin_id,'2023-08-10 10:00:00'),
(9,'Clínica Geral Rotina','Exame físico sem alterações.','Saudável','Nenhum',NULL,'Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2024-02-05','Consulta',NULL,@admin_id,'2024-02-05 10:00:00'),
-- Paciente 10
(10,'Gastroenterologia','EDA: gastrite erosiva antral. H. pylori +.','Gastrite erosiva, H. pylori','Epigastralgia, pirose','Omeprazol 40mg + Claritro + Amox 14d','Dr. Gustavo Ramos','Gastroenterologia','Hosp. das Clínicas','2023-05-12','Consulta',NULL,@admin_id,'2023-05-12 09:00:00'),
(10,'Retorno Gastro','Teste respiratório H. pylori negativo.','H. pylori erradicado','Assintomático','Omeprazol 20mg 30 dias','Dr. Gustavo Ramos','Gastroenterologia','Hosp. das Clínicas','2023-08-18','Retorno',NULL,@admin_id,'2023-08-18 09:00:00'),
(10,'Gastro Semestral','Sem queixas. Dieta adequada.','Gastrite controlada','Assintomático','Omeprazol 20mg manutenção','Dr. Gustavo Ramos','Gastroenterologia','Hosp. das Clínicas','2024-03-10','Retorno',NULL,@admin_id,'2024-03-10 09:00:00'),
-- Paciente 11
(11,'Clínica Geral Rotina','Jovem saudável. Exames normais.','Saudável','Nenhum',NULL,'Dr. Marcos Almeida','Clínica Geral','Clínica São Lucas','2023-06-15','Consulta',NULL,@admin_id,'2023-06-15 10:00:00'),
(11,'Dermatologia - Acne','Acne grau II em face.','Acne vulgar grau II','Espinhas e cravos','Adapaleno 0.1% + Peróxido benzoíla 5%','Dra. Renata Souza','Dermatologia','Clínica Derme','2023-10-22','Consulta',NULL,@admin_id,'2023-10-22 11:00:00'),
-- Paciente 12
(12,'Cardiologia - IC','FEVE 35%. BNP 850. IC III NYHA.','IC sistólica classe III','Dispneia, edema MMII','Carvedilol 25mg 2x, Furosemida 40mg, Espironolactona 25mg','Dr. Carlos E. Pinto','Cardiologia','InCor','2023-03-05','Consulta','Restrição hídrica',@admin_id,'2023-03-05 09:00:00'),
(12,'Pneumologia - DPOC','VEF1 45%. DPOC GOLD III.','DPOC GOLD III','Tosse crônica, dispneia','Tiotrópio 18mcg + Budesonida/Formoterol','Dr. Pedro H. Souza','Pneumologia','Hosp. Pulmonar','2023-05-10','Consulta',NULL,@admin_id,'2023-05-10 09:00:00'),
(12,'Internação - IC','IC descompensada com congestão pulmonar.','IC descompensada','Dispneia repouso, ortopneia','Dobutamina, Furosemida EV','Dr. Carlos E. Pinto','Cardiologia','InCor','2023-08-20','Internação','5 dias internado',@admin_id,'2023-08-20 09:00:00'),
(12,'Retorno pós-internação','Estável. FEVE 38%. BNP 420.','IC compensada','Dispneia esforços moderados','Sacubitril/Valsartana 50mg, Carvedilol, Furosemida 80mg','Dr. Carlos E. Pinto','Cardiologia','InCor','2023-09-15','Retorno',NULL,@admin_id,'2023-09-15 09:00:00'),
-- Paciente 13
(13,'Psiquiatria','GAD-7: 15. PHQ-9: 8.','TAG','Preocupação excessiva, insônia','Escitalopram 10mg, TCC','Dra. Luciana Barros','Psiquiatria','Clínica Equilíbrio','2023-05-20','Consulta',NULL,@admin_id,'2023-05-20 10:00:00'),
(13,'Retorno Psiquiatria','GAD-7: 10 - melhora parcial.','TAG em tratamento','Melhora insônia','Manter Escitalopram','Dra. Luciana Barros','Psiquiatria','Clínica Equilíbrio','2023-08-25','Retorno',NULL,@admin_id,'2023-08-25 10:00:00'),
(13,'Psiquiatria Trimestral','GAD-7: 6 - boa evolução.','TAG controlado','Ansiedade eventual','Manter tratamento','Dra. Luciana Barros','Psiquiatria','Clínica Equilíbrio','2024-01-30','Retorno',NULL,@admin_id,'2024-01-30 10:00:00'),
-- Paciente 14
(14,'Pediatria - Rinite','Mucosa nasal hipertrofiada.','Rinite alérgica','Espirros, congestão nasal','Mometasona spray, Loratadina 10mg','Dra. Ana Paula Lima','Pediatria','Clín. Infantil Esperança','2023-06-05','Consulta',NULL,@admin_id,'2023-06-05 08:00:00'),
(14,'Alergologia - Imunoterapia','Início dessensibilização ácaros/pólen.','Rinite alérgica','Rinite persistente','Imunoterapia subcutânea semanal','Dr. Paulo Mendes','Alergologia','Hosp. Infantil Sabará','2023-10-10','Procedimento',NULL,@admin_id,'2023-10-10 10:00:00'),
(14,'Retorno Alergologia','Boa tolerância. Redução sintomas.','Rinite em dessensibilização','Melhora parcial','Manter imunoterapia','Dr. Paulo Mendes','Alergologia','Hosp. Infantil Sabará','2024-02-12','Retorno',NULL,@admin_id,'2024-02-12 10:00:00'),
-- Paciente 15
(15,'Endocrinologia - Osteoporose','T-score coluna -3.2, fêmur -2.8.','Osteoporose severa','Dor lombar','Alendronato 70mg semanal, Cálcio+VitD','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-03-25','Consulta',NULL,@admin_id,'2023-03-25 10:00:00'),
(15,'Cardiologia','PA 160/100. ECG HVE.','Hipertensão estágio 2','Cefaleia, tonturas','Enalapril 10mg 2x/dia','Dr. Carlos E. Pinto','Cardiologia','InCor','2023-06-18','Consulta',NULL,@admin_id,'2023-06-18 09:00:00'),
(15,'Ortopedia - Lombar','RNM: discopatia L4-L5, L5-S1.','Discopatia degenerativa','Lombalgia crônica','Fisioterapia, Paracetamol SOS','Dr. Felipe Costa','Ortopedia','Hosp. Ortopédico','2023-10-02','Consulta',NULL,@admin_id,'2023-10-02 14:00:00'),
(15,'Retorno Endocrinologia','Densitometria estável.','Osteoporose acompanhamento','Sem fraturas','Manter Alendronato','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2024-03-20','Retorno',NULL,@admin_id,'2024-03-20 10:00:00'),
-- Paciente 16
(16,'Ortopedia - Joelho','RNM: lesão parcial LCA direito.','Lesão parcial LCA','Dor no joelho, edema','Fisioterapia, anti-inflamatório 7 dias','Dr. Felipe Costa','Ortopedia','Hosp. Ortopédico','2023-07-12','Consulta','Evitar corrida 3 meses',@admin_id,'2023-07-12 14:00:00'),
(16,'Retorno Ortopedia','Joelho estável. Força recuperada.','LCA cicatrizado','Sem dor','Retorno gradual à corrida','Dr. Felipe Costa','Ortopedia','Hosp. Ortopédico','2023-11-15','Retorno',NULL,@admin_id,'2023-11-15 14:00:00'),
-- Paciente 17
(17,'Gastro - Celíaca','Anti-transglut >200. Biópsia Marsh III.','Doença celíaca','Diarreia crônica, distensão','Dieta sem glúten rigorosa','Dr. Gustavo Ramos','Gastroenterologia','Hosp. das Clínicas','2023-07-20','Consulta','Orientação nutricional',@admin_id,'2023-07-20 09:00:00'),
(17,'Retorno Gastro','Anti-transglut normalizado. Ganho 3kg.','Celíaca controlada','Assintomática','Manter dieta sem glúten','Dr. Gustavo Ramos','Gastroenterologia','Hosp. das Clínicas','2023-12-10','Retorno',NULL,@admin_id,'2023-12-10 09:00:00'),
(17,'Nutrição','Adequação nutricional. IMC normalizado.','Acompanhamento nutricional','Nenhum','Manter plano alimentar','Dra. Carla Nutrição','Nutrição','Clínica Nutri+','2024-03-05','Consulta',NULL,@admin_id,'2024-03-05 10:00:00'),
-- Paciente 18
(18,'Ortopedia - Lombalgia','RNM: protrusão L4-L5.','Lombalgia, protrusão discal','Dor lombar irradiando perna E','Pregabalina 75mg 2x, Fisioterapia','Dr. Felipe Costa','Ortopedia','Hosp. Ortopédico','2023-08-10','Consulta',NULL,@admin_id,'2023-08-10 14:00:00'),
(18,'Fisiatria','Bloqueio facetário L4-L5 realizado.','Pós-bloqueio','Melhora 70% da dor','Manter Pregabalina, fisioterapia','Dr. Alexandre Fisiatra','Fisiatria','Clínica da Dor','2023-11-20','Procedimento',NULL,@admin_id,'2023-11-20 10:00:00'),
(18,'Retorno Ortopedia','Melhora significativa. Caminha sem dor.','Lombalgia controlada','Dor leve ocasional','Reduzir Pregabalina','Dr. Felipe Costa','Ortopedia','Hosp. Ortopédico','2024-02-28','Retorno',NULL,@admin_id,'2024-02-28 14:00:00'),
-- Paciente 19
(19,'Puericultura 3 anos','Peso P50, Altura P60. DNPM adequado.','Saudável','Nenhum','Vitamina D 600UI/dia','Dra. Ana Paula Lima','Pediatria','Clín. Infantil Esperança','2023-06-01','Consulta','Vacinas em dia',@admin_id,'2023-06-01 08:00:00'),
(19,'Urgência - Bronquiolite','Sibilância, taquipneia. SatO2 94%.','Bronquiolite aguda','Tosse, sibilância','Nebulização SF, Oxigênio SOS','Dra. Ana Paula Lima','Pediatria','PS Infantil','2023-08-15','Emergência','Observação 4h',@admin_id,'2023-08-15 22:00:00'),
(19,'Puericultura 3a6m','Desenvolvimento adequado.','Saudável','Nenhum',NULL,'Dra. Ana Paula Lima','Pediatria','Clín. Infantil Esperança','2024-01-05','Consulta',NULL,@admin_id,'2024-01-05 08:00:00'),
-- Paciente 20
(20,'Endocrinologia - DM2','HbA1c 9.5%. Glicemia 220. Início insulina.','DM2 descompensado','Polidipsia, poliúria, emagrecimento','Insulina Glargina 20UI, Metformina 850mg 2x','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2023-09-01','Consulta',NULL,@admin_id,'2023-09-01 10:00:00'),
(20,'Reumatologia - Gota','Artrite gotosa 1º MTF D. Ác. úrico 9.8.','Gota','Dor hálux D, edema, eritema','Colchicina 0.5mg 2x crise, Alopurinol 300mg','Dr. Roberto Ferreira','Reumatologia','Hosp. das Clínicas','2023-10-15','Consulta','Evitar álcool e purinas',@admin_id,'2023-10-15 10:00:00'),
(20,'Retorno Endocrinologia','HbA1c 7.8% - melhora.','DM2 em melhora','Assintomático','Insulina Glargina 24UI','Dra. Helena Martins','Endocrinologia','Hosp. das Clínicas','2024-01-10','Retorno',NULL,@admin_id,'2024-01-10 10:00:00'),
(20,'Retorno Reumatologia','Ác. úrico 6.5. Sem crises.','Gota controlada','Assintomático','Manter Alopurinol 300mg','Dr. Roberto Ferreira','Reumatologia','Hosp. das Clínicas','2024-03-15','Retorno',NULL,@admin_id,'2024-03-15 10:00:00');

-- =============================================
-- EXAMES (~50 registros)
-- =============================================
INSERT INTO `exams` (`patient_id`,`title`,`exam_type`,`exam_date`,`lab_clinic`,`doctor_name`,`results`,`notes`,`status`,`created_by`,`created_at`) VALUES
(1,'Hemograma Completo','Hemograma','2023-03-18','Lab Fleury','Dr. Marcos Almeida','Hb: 11.2 g/dL | Ht: 34% | Leuc: 6800 | Plaq: 250000','Anemia leve','Alterado',@admin_id,'2023-03-18 08:00:00'),
(1,'Perfil Lipídico','Bioquímico','2023-03-18','Lab Fleury','Dr. Marcos Almeida','CT: 195 | LDL: 115 | HDL: 58 | TG: 110',NULL,'Normal',@admin_id,'2023-03-18 08:00:00'),
(1,'TSH e T4 Livre','Hormonal','2023-03-18','Lab Fleury','Dr. Marcos Almeida','TSH: 2.8 | T4L: 1.2',NULL,'Normal',@admin_id,'2023-03-18 08:00:00'),
(1,'Mamografia Bilateral','Imagem','2023-06-15','Hosp. Albert Einstein','Dra. Fernanda Oliveira','BI-RADS 1 - Normal',NULL,'Normal',@admin_id,'2023-06-15 10:00:00'),
(1,'RM Crânio','Imagem','2023-08-25','Hosp. Sírio-Libanês','Dr. Ricardo Bastos','Sem lesões estruturais','Enxaqueca com aura','Normal',@admin_id,'2023-08-25 09:00:00'),
(1,'Hemograma Controle','Hemograma','2024-03-15','Lab Fleury','Dr. Marcos Almeida','Hb: 13.1 | Ht: 39% | Leuc: 5900 | Plaq: 230000','Anemia corrigida','Normal',@admin_id,'2024-03-15 08:00:00'),
(2,'Hemograma Pediátrico','Hemograma','2023-02-22','Lab Fleury','Dra. Ana Paula Lima','Hb: 12.8 | Leuc: 8200 | Plaq: 320000',NULL,'Normal',@admin_id,'2023-02-22 08:00:00'),
(2,'IgE Específica Amendoim','Alergia','2024-01-20','Hosp. Inf. Sabará','Dr. Paulo Mendes','IgE amendoim: 85 kU/L (classe 5) | IgE total: 450','Alergia severa','Alterado',@admin_id,'2024-01-20 10:00:00'),
(3,'Hemograma Pediátrico','Hemograma','2023-01-28','Lab Fleury','Dra. Ana Paula Lima','Hb: 11.8 | Leuc: 9100 | Plaq: 290000',NULL,'Normal',@admin_id,'2023-01-28 08:00:00'),
(4,'Hemoglobina Glicada','Bioquímico','2023-04-03','Lab Fleury','Dra. Helena Martins','HbA1c: 8.2% | Glicemia jejum: 185','Controle inadequado','Alterado',@admin_id,'2023-04-03 08:00:00'),
(4,'Ecocardiograma','Imagem','2023-02-12','InCor','Dr. Carlos E. Pinto','FE: 55% | HVE concêntrica | Disfunção diastólica I',NULL,'Alterado',@admin_id,'2023-02-12 10:00:00'),
(4,'MAPA 24h','Cardiológico','2023-02-15','InCor','Dr. Carlos E. Pinto','PA vigília: 148/92 | PA sono: 130/82','Hipertensão confirmada','Alterado',@admin_id,'2023-02-15 08:00:00'),
(4,'HbA1c Controle','Bioquímico','2023-07-18','Lab Fleury','Dra. Helena Martins','HbA1c: 7.1% | Glicemia: 130','Melhora significativa','Alterado',@admin_id,'2023-07-18 08:00:00'),
(4,'Fundo de Olho','Oftalmológico','2023-09-15','Hosp. de Olhos','Dr. André Yamamoto','NPDR leve | Sem edema macular | PIO normal',NULL,'Alterado',@admin_id,'2023-09-15 14:00:00'),
(5,'VHS e PCR','Bioquímico','2023-03-06','Lab Fleury','Dr. Roberto Ferreira','VHS: 45 | PCR: 2.8 | FR: 120','Atividade inflamatória','Alterado',@admin_id,'2023-03-06 08:00:00'),
(5,'Densitometria Óssea','Imagem','2023-09-18','Hosp. das Clínicas','Dra. Helena Martins','Coluna T-score: -1.8 | Fêmur T-score: -1.5 | Osteopenia',NULL,'Alterado',@admin_id,'2023-09-18 08:00:00'),
(5,'VHS PCR Controle','Bioquímico','2024-01-13','Lab Fleury','Dr. Roberto Ferreira','VHS: 18 | PCR: 0.4 | FR: 80','Remissão','Normal',@admin_id,'2024-01-13 08:00:00'),
(6,'Espirometria','Pulmonar','2023-04-18','Hosp. Pulmonar','Dr. Pedro H. Souza','CVF: 4.2L(85%) | VEF1: 3.0L(75%) | PBD: positiva +15%','Obstrutivo reversível','Alterado',@admin_id,'2023-04-18 09:00:00'),
(6,'Espirometria Controle','Pulmonar','2023-08-15','Hosp. Pulmonar','Dr. Pedro H. Souza','CVF: 4.5L(92%) | VEF1: 3.6L(90%) | Normal','Melhora','Normal',@admin_id,'2023-08-15 09:00:00'),
(6,'ECG','Cardiológico','2024-01-10','Clínica São Lucas','Dr. Marcos Almeida','Ritmo sinusal, FC 62bpm, normal','Apto esporte','Normal',@admin_id,'2024-01-10 10:00:00'),
(7,'IgE Frutos do Mar','Alergia','2023-03-20','Hosp. Albert Einstein','Dr. Paulo Mendes','IgE camarão: 45 | IgE lula: 28 | IgE caranguejo: 52',NULL,'Alterado',@admin_id,'2023-03-20 10:00:00'),
(7,'Papanicolau','Ginecológico','2023-08-05','Hosp. Albert Einstein','Dra. Fernanda Oliveira','Citologia: negativa | Flora: lactobacilar',NULL,'Normal',@admin_id,'2023-08-05 14:00:00'),
(8,'Perfil Lipídico','Bioquímico','2023-02-26','Lab Fleury','Dr. Marcos Almeida','CT: 265 | LDL: 175 | HDL: 38 | TG: 260','Dislipidemia','Alterado',@admin_id,'2023-02-26 08:00:00'),
(8,'Perfil Lipídico Controle','Bioquímico','2023-06-18','Lab Fleury','Dr. Marcos Almeida','CT: 210 | LDL: 130 | HDL: 42 | TG: 190','Melhora','Alterado',@admin_id,'2023-06-18 08:00:00'),
(8,'RX Tórax','Imagem','2023-09-05','Hosp. Pulmonar','Dr. Pedro H. Souza','Campos limpos. ICT normal. Sem alterações.',NULL,'Normal',@admin_id,'2023-09-05 09:00:00'),
(9,'TSH e T4L','Hormonal','2023-04-08','Lab Fleury','Dra. Helena Martins','TSH: 8.5 | T4L: 0.7','Hipotireoidismo','Alterado',@admin_id,'2023-04-08 08:00:00'),
(9,'TSH T4L Controle','Hormonal','2023-08-08','Lab Fleury','Dra. Helena Martins','TSH: 3.2 | T4L: 1.1','Normalizado','Normal',@admin_id,'2023-08-08 08:00:00'),
(10,'Endoscopia Digestiva','Endoscopia','2023-05-10','Hosp. das Clínicas','Dr. Gustavo Ramos','Gastrite erosiva antral | H. pylori +',NULL,'Alterado',@admin_id,'2023-05-10 09:00:00'),
(10,'Teste Respiratório H.pylori','Bioquímico','2023-08-16','Hosp. das Clínicas','Dr. Gustavo Ramos','NEGATIVO - H. pylori erradicado',NULL,'Normal',@admin_id,'2023-08-16 08:00:00'),
(12,'BNP','Bioquímico','2023-03-03','InCor','Dr. Carlos E. Pinto','BNP: 850 | Cr: 1.4 | Na: 136','IC descompensada','Alterado',@admin_id,'2023-03-03 08:00:00'),
(12,'Ecocardiograma','Imagem','2023-03-05','InCor','Dr. Carlos E. Pinto','FE: 35% | Dilatação câmaras E | IM moderada',NULL,'Alterado',@admin_id,'2023-03-05 10:00:00'),
(12,'Espirometria','Pulmonar','2023-05-10','Hosp. Pulmonar','Dr. Pedro H. Souza','CVF: 2.8L(65%) | VEF1: 1.2L(45%) | DPOC GOLD III',NULL,'Alterado',@admin_id,'2023-05-10 09:00:00'),
(12,'RX Tórax','Imagem','2023-08-20','InCor','Dr. Carlos E. Pinto','Congestão bilateral | Derrame pleural | ICT aumentado',NULL,'Alterado',@admin_id,'2023-08-20 09:00:00'),
(12,'BNP Pós-internação','Bioquímico','2023-09-13','InCor','Dr. Carlos E. Pinto','BNP: 420 | Cr: 1.2','Melhora parcial','Alterado',@admin_id,'2023-09-13 08:00:00'),
(13,'Hemograma Bioquímica','Hemograma','2023-05-18','Lab Fleury','Dra. Luciana Barros','Hemograma normal | Glicemia: 88 | TSH: 2.1','Causas orgânicas descartadas','Normal',@admin_id,'2023-05-18 08:00:00'),
(14,'Prick Test','Alergia','2023-06-05','Clín. Infantil Esperança','Dra. Ana Paula Lima','Ácaros: +++ | Pólen gramíneas: ++ | Cão: +',NULL,'Alterado',@admin_id,'2023-06-05 08:00:00'),
(15,'Densitometria Óssea','Imagem','2023-03-23','Hosp. das Clínicas','Dra. Helena Martins','Coluna T-score: -3.2 | Fêmur T-score: -2.8','Osteoporose severa','Alterado',@admin_id,'2023-03-23 08:00:00'),
(15,'ECG','Cardiológico','2023-06-18','InCor','Dr. Carlos E. Pinto','Sinusal, FC 78 | HVE | Alteração repolarização',NULL,'Alterado',@admin_id,'2023-06-18 09:00:00'),
(15,'RNM Coluna Lombar','Imagem','2023-10-01','Hosp. Ortopédico','Dr. Felipe Costa','Discopatia L4-L5/L5-S1 | Protrusão L4-L5 | Espondiloartrose',NULL,'Alterado',@admin_id,'2023-10-01 08:00:00'),
(16,'RNM Joelho Direito','Imagem','2023-07-10','Hosp. Ortopédico','Dr. Felipe Costa','Lesão parcial LCA (grau II) | Menisco íntegro | Derrame leve',NULL,'Alterado',@admin_id,'2023-07-10 10:00:00'),
(17,'Anti-transglutaminase','Bioquímico','2023-07-18','Lab Fleury','Dr. Gustavo Ramos','Anti-tTG IgA: >200 (ref: <20) | IgA total: 280','Forte positivo','Alterado',@admin_id,'2023-07-18 08:00:00'),
(17,'Anti-transglut Controle','Bioquímico','2023-12-08','Lab Fleury','Dr. Gustavo Ramos','Anti-tTG IgA: 12 (ref: <20) | Normalizado','Dieta eficaz','Normal',@admin_id,'2023-12-08 08:00:00'),
(18,'RNM Coluna Lombar','Imagem','2023-08-08','Hosp. Ortopédico','Dr. Felipe Costa','Protrusão L4-L5 foraminal E | Compressão raiz L5',NULL,'Alterado',@admin_id,'2023-08-08 08:00:00'),
(20,'HbA1c','Bioquímico','2023-08-30','Lab Fleury','Dra. Helena Martins','HbA1c: 9.5% | Glicemia: 220 | Cr: 1.1','DM2 descompensado','Alterado',@admin_id,'2023-08-30 08:00:00'),
(20,'Ácido Úrico','Bioquímico','2023-10-13','Lab Fleury','Dr. Roberto Ferreira','Ác. úrico: 9.8 (ref: 3.5-7.2) | Cr: 1.0','Hiperuricemia','Alterado',@admin_id,'2023-10-13 08:00:00'),
(20,'HbA1c Controle','Bioquímico','2024-01-08','Lab Fleury','Dra. Helena Martins','HbA1c: 7.8% | Glicemia: 150','Melhora com insulina','Alterado',@admin_id,'2024-01-08 08:00:00'),
(20,'Ácido Úrico Controle','Bioquímico','2024-03-13','Lab Fleury','Dr. Roberto Ferreira','Ác. úrico: 6.5 | Controlado',NULL,'Normal',@admin_id,'2024-03-13 08:00:00');

-- =============================================
-- MEDICAMENTOS (~45 registros)
-- =============================================
INSERT INTO `medications` (`patient_id`,`name`,`active_ingredient`,`dosage`,`frequency`,`route`,`start_date`,`end_date`,`prescriber`,`specialty`,`reason`,`instructions`,`side_effects`,`is_continuous`,`is_active`,`notes`,`created_by`,`created_at`) VALUES
-- Pac 1
(1,'Topiramato','Topiramato','50mg','1x/dia (noite)','Oral','2023-08-22',NULL,'Dr. Ricardo Bastos','Neurologia','Profilaxia enxaqueca','Tomar à noite','Formigamento, perda apetite',1,1,NULL,@admin_id,'2023-08-22 09:00:00'),
(1,'Sumatriptano','Sumatriptano','50mg','SOS (máx 2/dia)','Oral','2023-08-22',NULL,'Dr. Ricardo Bastos','Neurologia','Crise enxaqueca aguda','Tomar no início da crise',NULL,0,1,NULL,@admin_id,'2023-08-22 09:00:00'),
(1,'Sulfato Ferroso','Sulfato ferroso','40mg','1x/dia','Oral','2023-03-15','2023-09-15','Dr. Marcos Almeida','Clínica Geral','Anemia ferropriva','Jejum com suco laranja','Constipação',0,0,'Concluído',@admin_id,'2023-03-15 10:00:00'),
-- Pac 2
(2,'Vitamina D','Colecalciferol','400 UI','1x/dia','Oral','2023-02-20',NULL,'Dra. Ana Paula Lima','Pediatria','Suplementação','4 gotas/dia com refeição',NULL,1,1,NULL,@admin_id,'2023-02-20 08:00:00'),
(2,'Epinefrina Auto-Injetável','Epinefrina','0.15mg','SOS emergência','Intramuscular','2024-01-18',NULL,'Dr. Paulo Mendes','Alergologia','Anafilaxia amendoim','Aplicar na coxa, chamar SAMU',NULL,0,1,'Verificar validade',@admin_id,'2024-01-18 10:00:00'),
-- Pac 3
(3,'Hidratante Cetaphil','Ceramidas','Qte suficiente','2x/dia','Tópica','2024-02-15',NULL,'Dra. Renata Souza','Dermatologia','Dermatite atópica','Após banho, pele úmida',NULL,1,1,NULL,@admin_id,'2024-02-15 10:00:00'),
(3,'Dexametasona creme','Dexametasona','0.1%','Nas lesões','Tópica','2024-02-15','2024-03-15','Dra. Renata Souza','Dermatologia','Dermatite atópica crise','Fina camada 2x/dia',NULL,0,0,NULL,@admin_id,'2024-02-15 10:00:00'),
-- Pac 4
(4,'Losartana','Losartana potássica','100mg','1x/dia','Oral','2023-02-10',NULL,'Dr. Carlos E. Pinto','Cardiologia','Hipertensão','Tomar pela manhã',NULL,1,1,NULL,@admin_id,'2023-02-10 09:00:00'),
(4,'Anlodipino','Anlodipino besilato','5mg','1x/dia','Oral','2023-02-10',NULL,'Dr. Carlos E. Pinto','Cardiologia','Hipertensão','Tomar com Losartana','Edema maleolar',1,1,NULL,@admin_id,'2023-02-10 09:00:00'),
(4,'Hidroclorotiazida','Hidroclorotiazida','25mg','1x/dia','Oral','2023-02-10',NULL,'Dr. Carlos E. Pinto','Cardiologia','Hipertensão','Tomar pela manhã',NULL,1,1,NULL,@admin_id,'2023-02-10 09:00:00'),
(4,'Metformina','Metformina','850mg','2x/dia','Oral','2023-04-05',NULL,'Dra. Helena Martins','Endocrinologia','Diabetes tipo 2','Com almoço e jantar','Desconforto gástrico',1,1,NULL,@admin_id,'2023-04-05 10:00:00'),
(4,'Glicazida','Glicazida MR','60mg','1x/dia','Oral','2023-04-05',NULL,'Dra. Helena Martins','Endocrinologia','Diabetes tipo 2','No café da manhã',NULL,1,1,NULL,@admin_id,'2023-04-05 10:00:00'),
-- Pac 5
(5,'Metotrexato','Metotrexato','15mg','1x/semana (sábado)','Oral','2023-03-08',NULL,'Dr. Roberto Ferreira','Reumatologia','Artrite reumatoide','Sábados. Evitar álcool.','Náusea',1,1,'Monitorar fígado trimestral',@admin_id,'2023-03-08 10:00:00'),
(5,'Ácido Fólico','Ácido fólico','5mg','1x/semana (segunda)','Oral','2023-03-08',NULL,'Dr. Roberto Ferreira','Reumatologia','Suplemento MTX','48h após MTX',NULL,1,1,NULL,@admin_id,'2023-03-08 10:00:00'),
(5,'Cálcio + Vitamina D','Carbonato Ca + Colecalciferol','500mg+1000UI','1x/dia','Oral','2023-09-20',NULL,'Dra. Helena Martins','Endocrinologia','Osteopenia','Após almoço',NULL,1,1,NULL,@admin_id,'2023-09-20 08:00:00'),
(5,'Prednisona','Prednisona','5mg','1x/dia','Oral','2023-03-08','2024-01-15','Dr. Roberto Ferreira','Reumatologia','AR ativa','Reduzida e suspensa','Ganho de peso',0,0,'Suspensa por remissão',@admin_id,'2023-03-08 10:00:00'),
-- Pac 6
(6,'Budesonida/Formoterol','Budesonida+Formoterol','200/6mcg','2x/dia','Inalatória','2023-04-18',NULL,'Dr. Pedro H. Souza','Pneumologia','Asma moderada','Inalar, prender 10s, enxaguar boca','Rouquidão',1,1,NULL,@admin_id,'2023-04-18 09:00:00'),
(6,'Salbutamol','Salbutamol','100mcg','SOS (máx 4x/dia)','Inalatória','2023-04-18',NULL,'Dr. Pedro H. Souza','Pneumologia','Crise de asma','Usar em falta de ar aguda','Taquicardia',0,1,'Bombinha resgate',@admin_id,'2023-04-18 09:00:00'),
-- Pac 7
(7,'Epinefrina Auto-Injetável','Epinefrina','0.3mg','SOS emergência','Intramuscular','2023-03-22',NULL,'Dr. Paulo Mendes','Alergologia','Anafilaxia frutos do mar','Aplicar na coxa',NULL,0,1,NULL,@admin_id,'2023-03-22 10:00:00'),
(7,'Cetirizina','Cetirizina','10mg','SOS','Oral','2023-03-22',NULL,'Dr. Paulo Mendes','Alergologia','Reação alérgica leve','Em caso de urticária','Sonolência',0,1,NULL,@admin_id,'2023-03-22 10:00:00'),
-- Pac 8
(8,'Sinvastatina','Sinvastatina','20mg','1x/dia (noite)','Oral','2023-02-28',NULL,'Dr. Marcos Almeida','Clínica Geral','Dislipidemia','Tomar à noite','Mialgia leve',1,1,NULL,@admin_id,'2023-02-28 10:00:00'),
(8,'Vareniclina','Vareniclina','1mg','2x/dia','Oral','2023-09-05','2023-12-05','Dr. Pedro H. Souza','Pneumologia','Cessação tabágica','Com alimento','Náusea, sonhos vívidos',0,0,'Concluído com sucesso',@admin_id,'2023-09-05 09:00:00'),
-- Pac 9
(9,'Levotiroxina','Levotiroxina sódica','75mcg','1x/dia (jejum)','Oral','2023-04-10',NULL,'Dra. Helena Martins','Endocrinologia','Hipotireoidismo','Jejum 30min antes café',NULL,1,1,NULL,@admin_id,'2023-04-10 10:00:00'),
-- Pac 10
(10,'Omeprazol','Omeprazol','20mg','1x/dia (manhã)','Oral','2023-05-12',NULL,'Dr. Gustavo Ramos','Gastroenterologia','Gastrite crônica','Jejum 30min antes café',NULL,1,1,'Manutenção pós-H.pylori',@admin_id,'2023-05-12 09:00:00'),
-- Pac 11
(11,'Adapaleno gel','Adapaleno','0.1%','1x/dia (noite)','Tópica','2023-10-22',NULL,'Dra. Renata Souza','Dermatologia','Acne vulgar','Noite, pele limpa e seca','Ressecamento',0,1,NULL,@admin_id,'2023-10-22 11:00:00'),
(11,'Peróxido de Benzoíla','Peróxido de benzoíla','5%','1x/dia (manhã)','Tópica','2023-10-22',NULL,'Dra. Renata Souza','Dermatologia','Acne vulgar','Manhã nas lesões','Irritação leve',0,1,NULL,@admin_id,'2023-10-22 11:00:00'),
-- Pac 12
(12,'Sacubitril/Valsartana','Sacubitril+Valsartana','50mg','2x/dia','Oral','2023-09-15',NULL,'Dr. Carlos E. Pinto','Cardiologia','IC','Substitui Enalapril','Hipotensão',1,1,NULL,@admin_id,'2023-09-15 09:00:00'),
(12,'Carvedilol','Carvedilol','25mg','2x/dia','Oral','2023-03-05',NULL,'Dr. Carlos E. Pinto','Cardiologia','IC','','Bradicardia',1,1,NULL,@admin_id,'2023-03-05 09:00:00'),
(12,'Furosemida','Furosemida','80mg','1x/dia (manhã)','Oral','2023-09-15',NULL,'Dr. Carlos E. Pinto','Cardiologia','IC congestão','Manhã. Peso diário.',NULL,1,1,'Dose aumentada pós-internação',@admin_id,'2023-09-15 09:00:00'),
(12,'Espironolactona','Espironolactona','25mg','1x/dia','Oral','2023-03-05',NULL,'Dr. Carlos E. Pinto','Cardiologia','IC','','Ginecomastia',1,1,NULL,@admin_id,'2023-03-05 09:00:00'),
(12,'Tiotrópio','Tiotrópio','18mcg','1x/dia','Inalatória','2023-05-10',NULL,'Dr. Pedro H. Souza','Pneumologia','DPOC','1 cápsula inalada/dia','Boca seca',1,1,NULL,@admin_id,'2023-05-10 09:00:00'),
-- Pac 13
(13,'Escitalopram','Escitalopram','10mg','1x/dia (manhã)','Oral','2023-05-20',NULL,'Dra. Luciana Barros','Psiquiatria','TAG','Tomar pela manhã','Náusea inicial',1,1,NULL,@admin_id,'2023-05-20 10:00:00'),
-- Pac 14
(14,'Mometasona spray nasal','Mometasona','50mcg/dose','2 jatos/narina 1x/dia','Nasal','2023-06-05',NULL,'Dra. Ana Paula Lima','Pediatria','Rinite alérgica','Pela manhã',NULL,1,1,NULL,@admin_id,'2023-06-05 08:00:00'),
(14,'Loratadina','Loratadina','10mg','1x/dia','Oral','2023-06-05',NULL,'Dra. Ana Paula Lima','Pediatria','Rinite alérgica','À noite',NULL,0,1,NULL,@admin_id,'2023-06-05 08:00:00'),
-- Pac 15
(15,'Alendronato','Alendronato sódico','70mg','1x/semana','Oral','2023-03-25',NULL,'Dra. Helena Martins','Endocrinologia','Osteoporose','Jejum, com água, ficar em pé 30min',NULL,1,1,NULL,@admin_id,'2023-03-25 10:00:00'),
(15,'Cálcio + Vitamina D','Carbonato Ca + Colecalciferol','500mg+1000UI','1x/dia','Oral','2023-03-25',NULL,'Dra. Helena Martins','Endocrinologia','Osteoporose','Após almoço',NULL,1,1,NULL,@admin_id,'2023-03-25 10:00:00'),
(15,'Enalapril','Enalapril','10mg','2x/dia','Oral','2023-06-18',NULL,'Dr. Carlos E. Pinto','Cardiologia','Hipertensão','Manhã e noite',NULL,1,1,NULL,@admin_id,'2023-06-18 09:00:00'),
-- Pac 18
(18,'Pregabalina','Pregabalina','75mg','2x/dia','Oral','2023-08-10',NULL,'Dr. Felipe Costa','Ortopedia','Lombalgia crônica','Manhã e noite','Sonolência, tontura',1,1,'Reduzir gradualmente',@admin_id,'2023-08-10 14:00:00'),
-- Pac 19
(19,'Vitamina D','Colecalciferol','600 UI','1x/dia','Oral','2023-06-01',NULL,'Dra. Ana Paula Lima','Pediatria','Suplementação','6 gotas/dia',NULL,1,1,NULL,@admin_id,'2023-06-01 08:00:00'),
-- Pac 20
(20,'Insulina Glargina','Insulina glargina','24 UI','1x/dia (noite)','Subcutânea','2023-09-01',NULL,'Dra. Helena Martins','Endocrinologia','DM2','Aplicar no abdômen à noite','Hipoglicemia',1,1,'Ajustada de 20 para 24UI',@admin_id,'2023-09-01 10:00:00'),
(20,'Metformina','Metformina','850mg','2x/dia','Oral','2023-09-01',NULL,'Dra. Helena Martins','Endocrinologia','DM2','Com almoço e jantar','Desconforto gástrico',1,1,NULL,@admin_id,'2023-09-01 10:00:00'),
(20,'Alopurinol','Alopurinol','300mg','1x/dia','Oral','2023-10-15',NULL,'Dr. Roberto Ferreira','Reumatologia','Gota','Após refeição',NULL,1,1,'Evitar álcool e purinas',@admin_id,'2023-10-15 10:00:00'),
(20,'Colchicina','Colchicina','0.5mg','2x/dia (durante crise)','Oral','2023-10-15','2023-10-25','Dr. Roberto Ferreira','Reumatologia','Crise gotosa aguda','Durante crises','Diarreia',0,0,'Uso apenas na crise',@admin_id,'2023-10-15 10:00:00');

SET FOREIGN_KEY_CHECKS = 1;

-- Confirmar
SELECT 'Pacientes' AS tabela, COUNT(*) AS total FROM patients
UNION ALL SELECT 'Prontuários', COUNT(*) FROM medical_records
UNION ALL SELECT 'Exames', COUNT(*) FROM exams
UNION ALL SELECT 'Medicamentos', COUNT(*) FROM medications;