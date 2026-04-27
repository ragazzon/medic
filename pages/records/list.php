<?php
$pageTitle = 'Prontuários';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

$search = trim($_GET['search'] ?? '');
$patientId = intval($_GET['patient_id'] ?? 0);
$specialty = trim($_GET['specialty'] ?? '');

// Controle de acesso: user só vê prontuários de seus pacientes
$where = [];
$params = [];

if (!isAdmin()) {
    $allowedIds = getAllowedPatientIds();
    if (empty($allowedIds)) {
        $where[] = "1=0";
    } else {
        $placeholders = implode(',', array_fill(0, count($allowedIds), '?'));
        $where[] = "mr.patient_id IN ({$placeholders})";
        $params = array_merge($params, $allowedIds);
    }
}

if ($search) {
    $where[] = "(mr.title LIKE ? OR mr.doctor_name LIKE ? OR p.name LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($patientId) {
    $where[] = "mr.patient_id = ?";
    $params[] = $patientId;
}
if ($specialty) {
    $where[] = "mr.specialty = ?";
    $params[] = $specialty;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$records = $pdo->prepare("
    SELECT mr.*, p.name as patient_name
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.id
    {$whereSQL}
    ORDER BY mr.record_date DESC
");
$records->execute($params);
$records = $records->fetchAll();

$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
$specialties = $pdo->query("SELECT DISTINCT specialty FROM medical_records WHERE specialty IS NOT NULL AND specialty != '' ORDER BY specialty")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-file-medical me-2"></i>Prontuários</h1>
    <?php if (isAdmin()): ?>
    <a href="<?= baseUrl('pages/records/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Prontuário
    </a>
    <?php endif; ?>
</div>

<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Buscar</label>
            <input type="text" name="search" class="form-control" placeholder="Título, médico ou paciente..." value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Paciente</label>
            <select name="patient_id" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $patientId == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Especialidade</label>
            <select name="specialty" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($specialties as $s): ?>
                <option value="<?= sanitize($s) ?>" <?= $specialty === $s ? 'selected' : '' ?>><?= sanitize($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary me-1"><i class="bi bi-search me-1"></i>Filtrar</button>
            <a href="<?= baseUrl('pages/records/list.php') ?>" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <i class="bi bi-file-medical"></i>
            <h4>Nenhum prontuário encontrado</h4>
            <p>Registre o primeiro prontuário médico.</p>
            <?php if (isAdmin()): ?>
            <a href="<?= baseUrl('pages/records/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Prontuário</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Título</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Especialidade</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $r): ?>
                    <tr>
                        <td><?= formatDate($r['record_date']) ?></td>
                        <td><a href="<?= baseUrl('pages/records/view.php?id=' . $r['id']) ?>" class="fw-semibold text-decoration-none"><?= sanitize($r['title']) ?></a></td>
                        <td><a href="<?= baseUrl('pages/patients/view.php?id=' . $r['patient_id']) ?>"><?= sanitize($r['patient_name']) ?></a></td>
                        <td><?= sanitize($r['doctor_name'] ?? '-') ?></td>
                        <td><?= sanitize($r['specialty'] ?? '-') ?></td>
                        <td class="text-end">
                            <a href="<?= baseUrl('pages/records/view.php?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <?php if (isAdmin()): ?>
                            <a href="<?= baseUrl('pages/records/form.php?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <a href="<?= baseUrl('pages/records/delete.php?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-danger" data-confirm="Excluir este prontuário?"><i class="bi bi-trash"></i></a>
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