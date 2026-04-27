<?php
require_once __DIR__ . '/functions.php';

/**
 * Registra log de acesso no banco de dados
 */
function logAccess($action, $userId = null, $userName = null, $userEmail = null, $details = null) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO access_logs (user_id, user_name, user_email, action, ip_address, user_agent, details) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $userName,
            $userEmail,
            $action,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            $details
        ]);
    } catch (Exception $e) {
        // Silenciosamente ignora erros de log para não afetar o fluxo
    }
}

/**
 * Protege a página - redireciona para login se não autenticado
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Você precisa estar logado para acessar esta página.');
        redirect(baseUrl('pages/login.php'));
    }
}

/**
 * Protege a página - apenas admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlash('danger', 'Acesso negado. Apenas administradores.');
        redirect(baseUrl('pages/dashboard.php'));
    }
}

/**
 * Realiza login do usuário
 */
function loginUser($email, $password) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        logAccess('login', $user['id'], $user['name'], $user['email']);
        return true;
    }
    // Login falhou
    logAccess('login_failed', null, null, $email, 'Tentativa de login com senha incorreta');
    return false;
}

/**
 * Registra novo usuário
 */
function registerUser($name, $email, $password, $role = 'user') {
    $pdo = getConnection();
    
    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Este e-mail já está cadastrado.'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $role]);
    
    return ['success' => true, 'id' => $pdo->lastInsertId()];
}

/**
 * Realiza logout
 */
function logoutUser() {
    $userId = $_SESSION['user_id'] ?? null;
    $userName = $_SESSION['user_name'] ?? null;
    $userEmail = $_SESSION['user_email'] ?? null;
    logAccess('logout', $userId, $userName, $userEmail);
    session_destroy();
    redirect(baseUrl('pages/login.php'));
}
