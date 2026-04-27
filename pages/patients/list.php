<?php
$pageTitle = 'Pacientes';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Filtros
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];

// Controle de acesso - users veem apenas pacientes associados
list($accessWhere, $accessParams) = getPatientAccessFilter('p');
if ($accessWhere) {
    $where[] = $accessWhere;
    $params = array_merge($params, $accessParams);
}

if ($search) {
    $where[] = "(p.name LIKE ?)";
    $params[] = "%{$search}%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$patients = $pdo->prepare("
    SELECT p.*, 
        (SELECT COUNT(*) FROM medical_records WHERE patient_id = p.id) as total_records,
        (SELECT COUNT(*) FROM exams WHERE patient_id = p.id) as total_exams
    FROM patients p 
    {$whereSQL}
    ORDER BY p.name ASC
");
$patients->execute($params);
$patients = $patients->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Pacientes</h1>
    <?php if (isAdmin()): ?>
    <a href="<?= baseUrl('pages/patients/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Paciente
    </a>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Buscar</label>
            <input type="text" name="search" class="form-control" placeholder="Nome do paciente..." value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i>Filtrar</button>
            <a href="<?= baseUrl('pages/patients/list.php') ?>" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($patients)): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h4>Nenhum paciente encontrado</h4>
            <?php if (isAdmin()): ?>
            <p>Cadastre o primeiro paciente para começar.</p>
            <a href="<?= baseUrl('pages/patients/form.php') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Novo Paciente
            </a>
            <?php else: ?>
            <p>Nenhum paciente foi associado à sua conta ainda.</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Parentesco</th>
                        <th>Data Nasc.</th>
                        <th>Tipo Sanguíneo</th>
                        <th class="text-center">Prontuários</th>
                        <th class="text-center">Exames</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($patients as $p): ?>
                    <tr>
                        <td>
                            <a href="<?= baseUrl('pages/patients/view.php?id=' . $p['id']) ?>" class="fw-semibold text-decoration-none">
                                <?= sanitize($p['name']) ?>
                            </a>
                        </td>
                        <td><?php if (!empty($p['relationship'])): ?><span class="badge bg-secondary"><?= sanitize($p['relationship']) ?></span><?php else: ?>-<?php endif; ?></td>
                        <td><?= formatDate($p['birth_date']) ?></td>
                        <td><?= sanitize($p['blood_type'] ?? '-') ?></td>
                        <td class="text-center"><?= $p['total_records'] ?></td>
                        <td class="text-center"><?= $p['total_exams'] ?></td>
                        <td class="text-end">
                            <a href="<?= baseUrl('pages/patients/view.php?id=' . $p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="<?= baseUrl('pages/patients/form.php?id=' . $p['id']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= baseUrl('pages/patients/delete.php?id=' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" title="Excluir" data-confirm="Tem certeza que deseja excluir este paciente? Todos os prontuários e exames serão perdidos.">
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