<?php
$pageTitle = 'Usuário';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$user = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        setFlash('danger', 'Usuário não encontrado.');
        redirect(baseUrl('pages/users/list.php'));
    }
    $pageTitle = 'Editar Usuário';
} else {
    $pageTitle = 'Novo Usuário';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email)) {
        setFlash('danger', 'Nome e e-mail são obrigatórios.');
    } elseif (!$id && empty($password)) {
        setFlash('danger', 'A senha é obrigatória para novos usuários.');
    } elseif (!empty($password) && $password !== $password_confirm) {
        setFlash('danger', 'As senhas não coincidem.');
    } elseif (!empty($password) && strlen($password) < 6) {
        setFlash('danger', 'A senha deve ter pelo menos 6 caracteres.');
    } else {
        $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->execute([$email, $id]);
        if ($checkEmail->fetch()) {
            setFlash('danger', 'Este e-mail já está sendo usado por outro usuário.');
        } else {
            if ($id) {
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $hashedPassword, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $id]);
                }
                setFlash('success', 'Usuário atualizado com sucesso!');
            } else {
                $result = registerUser($name, $email, $password, $role);
                if ($result['success']) {
                    setFlash('success', 'Usuário criado com sucesso!');
                } else {
                    setFlash('danger', $result['error']);
                }
            }
            redirect(baseUrl('pages/users/list.php'));
        }
    }
}

$d = $user ?? $_POST ?? [];

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-person-gear me-2"></i><?= $id ? 'Editar' : 'Novo' ?> Usuário</h1>
    <a href="<?= baseUrl('pages/users/list.php') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nome completo *</label>
                    <input type="text" name="name" class="form-control" value="<?= sanitize($d['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail *</label>
                    <input type="email" name="email" class="form-control" value="<?= sanitize($d['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Perfil</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= ($d['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>Usuário (consulta)</option>
                        <option value="admin" <?= ($d['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <small class="text-muted">Administradores podem cadastrar dados. Usuários apenas consultam pacientes associados.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Senha <?= $id ? '(deixe em branco para manter)' : '*' ?></label>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" <?= $id ? '' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Confirmar senha</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repita a senha" <?= $id ? '' : 'required' ?>>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Criar Usuário' ?>
                </button>
                <a href="<?= baseUrl('pages/users/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php if ($id && ($d['role'] ?? '') === 'user'): ?>
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Pacientes Associados</span>
        <a href="<?= baseUrl('pages/users/patients.php?id=' . $id) ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i>Gerenciar Pacientes
        </a>
    </div>
    <div class="card-body">
        <?php
        $assigned = $pdo->prepare("
            SELECT p.id, p.name, p.relationship 
            FROM patients p 
            JOIN user_patients up ON p.id = up.patient_id 
            WHERE up.user_id = ? 
            ORDER BY p.name
        ");
        $assigned->execute([$id]);
        $assignedPatients = $assigned->fetchAll();
        ?>
        <?php if (empty($assignedPatients)): ?>
        <p class="text-muted mb-0">Nenhum paciente associado a este usuário.</p>
        <?php else: ?>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($assignedPatients as $ap): ?>
            <span class="badge bg-info fs-6 fw-normal">
                <i class="bi bi-person me-1"></i><?= sanitize($ap['name']) ?>
                <?php if (!empty($ap['relationship'])): ?>
                <small>(<?= sanitize($ap['relationship']) ?>)</small>
                <?php endif; ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>