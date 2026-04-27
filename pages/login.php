<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../includes/auth.php';

// Se já está logado, redireciona
if (isLoggedIn()) {
    redirect(baseUrl('pages/dashboard.php'));
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        setFlash('danger', 'Preencha todos os campos.');
    } elseif (loginUser($email, $password)) {
        setFlash('success', 'Bem-vindo ao MEDIC!');
        redirect(baseUrl('pages/dashboard.php'));
    } else {
        setFlash('danger', 'E-mail ou senha incorretos.');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-card fade-in">
    <div class="logo">
        <i class="bi bi-heart-pulse"></i>
        <h2>MEDIC</h2>
        <p>Controle Médico Familiar</p>
    </div>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="seu@email.com" value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Sua senha" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
        </button>
        
        <p class="text-center text-muted mb-0">
            <small>Solicite acesso ao administrador do sistema</small>
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>