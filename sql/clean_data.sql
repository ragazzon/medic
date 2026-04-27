-- =============================================
-- MEDIC - Limpar todas as tabelas exceto usuários
-- Remove todos os dados de pacientes, prontuários,
-- exames, medicamentos e associações.
-- A tabela 'users' é preservada.
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Limpar arquivos de exames
TRUNCATE TABLE `exam_files`;

-- Limpar arquivos de prontuários
TRUNCATE TABLE `record_files`;

-- Limpar medicamentos
TRUNCATE TABLE `medications`;

-- Limpar exames
TRUNCATE TABLE `exams`;

-- Limpar prontuários
TRUNCATE TABLE `medical_records`;

-- Limpar associações usuário-paciente
TRUNCATE TABLE `user_patients`;

-- Limpar pacientes
TRUNCATE TABLE `patients`;

SET FOREIGN_KEY_CHECKS = 1;

-- Confirmar limpeza
SELECT 'Pacientes' AS tabela, COUNT(*) AS total FROM patients
UNION ALL SELECT 'Prontuários', COUNT(*) FROM medical_records
UNION ALL SELECT 'Exames', COUNT(*) FROM exams
UNION ALL SELECT 'Arq. Exames', COUNT(*) FROM exam_files
UNION ALL SELECT 'Arq. Prontuários', COUNT(*) FROM record_files
UNION ALL SELECT 'Medicamentos', COUNT(*) FROM medications
UNION ALL SELECT 'Assoc. Usuário-Paciente', COUNT(*) FROM user_patients
UNION ALL SELECT '--- PRESERVADOS ---', 0
UNION ALL SELECT 'Usuários (preservados)', COUNT(*) FROM users;