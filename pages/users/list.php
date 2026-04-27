<?php
$pageTitle = 'Gerenciar Usuários';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();

$users = $pdo->query("
    SELECT u.*, 
        (SELECT COUNT(*) FROM user_patients WHERE user_id = u.id) as total_patients
    FROM users u 
    ORDER BY u.name ASC
")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-person-gear me-2"></i>Gerenciar Usuários</h1>
    <a href="<?= baseUrl('pages/users/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Novo Usuário
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="bi bi-person-gear"></i>
            <h4>Nenhum usuário encontrado</h4>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th class="text-center">Pacientes</th>
                        <th>Criado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="fw-semibold"><?= sanitize($u['name']) ?></td>
                        <td><?= sanitize($u['email']) ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                            <span class="badge bg-primary">Usuário</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($u['role'] === 'user'): ?>
                            <span class="badge bg-info"><?= $u['total_patients'] ?></span>
                            <?php else: ?>
                            <span class="text-muted">Todos</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDateTime($u['created_at']) ?></td>
                        <td class="text-end">
                            <a href="<?= baseUrl('pages/users/form.php?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($u['role'] === 'user'): ?>
                            <a href="<?= baseUrl('pages/users/patients.php?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-info" title="Gerenciar Pacientes">
                                <i class="bi bi-people"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($u['id'] != getCurrentUserId()): ?>
                            <a href="<?= baseUrl('pages/users/delete.php?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-danger" title="Excluir" data-confirm="Tem certeza que deseja excluir este usuário?">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>