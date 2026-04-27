-- =============================================
-- MEDIC - Limpar todos os pacientes exceto Eric Machado Fressatto
-- ATENÇÃO: Este script apaga PERMANENTEMENTE todos os dados
-- de pacientes, prontuários, exames, medicamentos e arquivos
-- associados, EXCETO do paciente "Eric Machado Fressatto".
-- Execute com cuidado!
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Remover arquivos de exames dos pacientes que serão excluídos
DELETE ef FROM exam_files ef
INNER JOIN exams e ON ef.exam_id = e.id
INNER JOIN patients p ON e.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Remover arquivos de prontuários dos pacientes que serão excluídos
DELETE rf FROM record_files rf
INNER JOIN medical_records mr ON rf.record_id = mr.id
INNER JOIN patients p ON mr.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Remover exames dos pacientes que serão excluídos
DELETE e FROM exams e
INNER JOIN patients p ON e.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Remover prontuários dos pacientes que serão excluídos
DELETE mr FROM medical_records mr
INNER JOIN patients p ON mr.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Remover medicamentos dos pacientes que serão excluídos
DELETE m FROM medications m
INNER JOIN patients p ON m.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Remover associações usuário-paciente dos pacientes que serão excluídos
DELETE up FROM user_patients up
INNER JOIN patients p ON up.patient_id = p.id
WHERE p.name != 'Eric Machado Fressatto';

-- Finalmente, remover os pacientes (exceto Eric Machado Fressatto)
DELETE FROM patients WHERE name != 'Eric Machado Fressatto';

SET FOREIGN_KEY_CHECKS = 1;

-- Verificar resultado
SELECT id, name FROM patients;