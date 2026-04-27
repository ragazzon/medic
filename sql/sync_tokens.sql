-- =============================================
-- Tabela de Tokens de Sincronização (Modo Local)
-- =============================================

CREATE TABLE IF NOT EXISTS `sync_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `used_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_sync_tokens_token ON sync_tokens(token);