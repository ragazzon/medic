-- =============================================
-- MEDIC - Inserir usuário administrador
-- Usuário: Giovani Ragazzon
-- E-mail: ragazzon@gmail.com
-- Senha: mvTmjsunp01!
-- =============================================

SET NAMES utf8mb4;

-- Inserir admin (ON DUPLICATE KEY para não duplicar se já existir)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`)
VALUES (
    'Giovani Ragazzon',
    'ragazzon@gmail.com',
    '$2b$10$7wD4iSigNd8HVbaBFQBpAOMQyge/1vAvt33LsCF8aco0xWW.BrTqu',
    'admin',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `name` = 'Giovani Ragazzon',
    `password` = '$2b$10$7wD4iSigNd8HVbaBFQBpAOMQyge/1vAvt33LsCF8aco0xWW.BrTqu',
    `role` = 'admin';

-- Confirmar
SELECT id, name, email, role FROM users WHERE email = 'ragazzon@gmail.com';