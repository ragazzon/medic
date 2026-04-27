-- =============================================
-- Tabela de Logs de Acesso
-- =============================================

CREATE TABLE IF NOT EXISTS `access_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `user_name` VARCHAR(255) NULL,
    `user_email` VARCHAR(255) NULL,
    `action` ENUM('login', 'logout', 'login_failed') NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `details` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_access_logs_user ON access_logs(user_id);
CREATE INDEX idx_access_logs_action ON access_logs(action);
CREATE INDEX idx_access_logs_created ON access_logs(created_at);