<?php
$pageTitle = 'Meu Perfil';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = getConnection();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($name) || empty($email)) {
            setFlash('danger', 'Nome e e-mail são obrigatórios.');
        } else {
            // Verificar e-mail duplicado
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->execute([$email, $userId]);
            if ($check->fetch()) {
                setFlash('danger', 'Este e-mail já está em uso.');
            } else {
                $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?")->execute([$name, $email, $userId]);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                setFlash('success', 'Perfil atualizado com sucesso!');
                redirect(baseUrl('pages/profile.php'));
            }
        }
    }
    
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($current) || empty($new)) {
            setFlash('danger', 'Preencha todos os campos de senha.');
        } elseif (!password_verify($current, $user['password'])) {
            setFlash('danger', 'Senha atual incorreta.');
        } elseif (strlen($new) < 6) {
            setFlash('danger', 'A nova senha deve ter pelo menos 6 caracteres.');
        } elseif ($new !== $confirm) {
            setFlash('danger', 'As senhas não coincidem.');
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $userId]);
            setFlash('success', 'Senha alterada com sucesso!');
            redirect(baseUrl('pages/profile.php'));
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-person-circle me-2"></i>Meu Perfil</h1>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Dados Pessoais</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= sanitize($user['email']) ?>" required>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-calendar3 me-1"></i>Cadastro: <?= formatDate($user['created_at']) ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-key me-2"></i>Alterar Senha</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label class="form-label">Senha atual</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-key me-1"></i>Alterar Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>