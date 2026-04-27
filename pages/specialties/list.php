<?php
$pageTitle = 'Especialidades Médicas';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

$search = trim($_GET['search'] ?? '');
$where = [];
$params = [];

if ($search) {
    $where[] = "s.name LIKE ?";
    $params[] = "%{$search}%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$specialties = $pdo->prepare("
    SELECT s.*,
        (SELECT COUNT(*) FROM medical_records WHERE specialty = s.name) as record_count,
        (SELECT COUNT(*) FROM exams WHERE specialty = s.name) as exam_count
    FROM specialties s
    {$whereSQL}
    ORDER BY s.name
");
$specialties->execute($params);
$specialties = $specialties->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-heart-pulse me-2"></i>Especialidades Médicas</h1>
    <?php if (isAdmin()): ?>
    <a href="<?= baseUrl('pages/specialties/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nova Especialidade
    </a>
    <?php endif; ?>
</div>

<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Buscar</label>
            <input type="text" name="search" class="form-control" placeholder="Nome da especialidade..." value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i>Filtrar</button>
            <a href="<?= baseUrl('pages/specialties/list.php') ?>" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($specialties)): ?>
        <div class="empty-state">
            <i class="bi bi-heart-pulse"></i>
            <h4>Nenhuma especialidade encontrada</h4>
            <p>Cadastre a primeira especialidade médica.</p>
            <?php if (isAdmin()): ?>
            <a href="<?= baseUrl('pages/specialties/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nova Especialidade</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="text-center">Consultas</th>
                        <th class="text-center">Exames</th>
                        <th>Cadastrada em</th>
                        <?php if (isAdmin()): ?>
                        <th class="text-end">Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($specialties as $s): ?>
                    <tr>
                        <td>
                            <i class="bi bi-heart-pulse text-primary me-1"></i>
                            <span class="fw-semibold"><?= sanitize($s['name']) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($s['record_count'] > 0): ?>
                            <span class="badge bg-info"><?= $s['record_count'] ?></span>
                            <?php else: ?>
                            <span class="text-muted">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($s['exam_count'] > 0): ?>
                            <span class="badge bg-warning text-dark"><?= $s['exam_count'] ?></span>
                            <?php else: ?>
                            <span class="text-muted">0</span>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?= formatDateTime($s['created_at']) ?></small></td>
                        <?php if (isAdmin()): ?>
                        <td class="text-end">
                            <a href="<?= baseUrl('pages/specialties/form.php?id=' . $s['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <a href="<?= baseUrl('pages/specialties/delete.php?id=' . $s['id']) ?>" class="btn btn-sm btn-outline-danger" data-confirm="Excluir a especialidade '<?= sanitize($s['name']) ?>'? Ela será removida da lista, mas os registros existentes manterão o texto."><i class="bi bi-trash"></i></a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>