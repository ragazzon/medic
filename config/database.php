<?php
// Modo de operação
if (!defined('LOCAL_MODE')) {
    define('LOCAL_MODE', false);
}

// Configuração do Banco de Dados
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_PORT', 3306);
define('DB_NAME', 'if0_41605348_medic');
define('DB_USER', 'if0_41605348');
define('DB_PASS', 'pYeFrzHAz8ja');
define('DB_CHARSET', 'utf8mb4');

// Configurações do sistema
define('SITE_NAME', 'MEDIC');
define('SITE_URL', '/');
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp']);
define('ALLOWED_DOC_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);
define('ALLOWED_AUDIO_TYPES', [
    'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac',
    'audio/flac', 'audio/x-m4a', 'audio/mp4'
]);
define('ALLOWED_VIDEO_TYPES', [
    'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime',
    'video/x-msvideo', 'video/x-matroska'
]);
define('ALLOWED_FILE_TYPES', array_merge(
    ALLOWED_IMAGE_TYPES,
    ALLOWED_DOC_TYPES,
    ALLOWED_AUDIO_TYPES,
    ALLOWED_VIDEO_TYPES
));

// Conexão PDO
function getConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, '1045') !== false) {
                die("<div style='font-family:Arial;padding:40px;text-align:center;'>"
                    . "<h2 style='color:#dc3545;'>❌ Erro de autenticação no banco de dados</h2>"
                    . "<p>Verifique no painel do InfinityFree:</p>"
                    . "<ul style='text-align:left;max-width:500px;margin:0 auto;'>"
                    . "<li>Se o banco <strong>" . DB_NAME . "</strong> foi criado</li>"
                    . "<li>Se o usuário <strong>" . DB_USER . "</strong> tem permissão</li>"
                    . "<li>Se a senha está correta</li>"
                    . "<li>Se o host é <strong>" . DB_HOST . "</strong></li>"
                    . "</ul></div>");
            }
            die("Erro de conexão: " . $errorMsg);
        }
    }
    return $pdo;
}

// Iniciar sessão se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}