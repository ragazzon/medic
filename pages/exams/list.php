<?php
$pageTitle = 'Exames';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

$search = trim($_GET['search'] ?? '');
$patientId = intval($_GET['patient_id'] ?? 0);
$examType = trim($_GET['exam_type'] ?? '');
$specialty = trim($_GET['specialty'] ?? '');

// Controle de acesso: user só vê exames de seus pacientes
$where = [];
$params = [];

if (!isAdmin()) {
    $allowedIds = getAllowedPatientIds();
    if (empty($allowedIds)) {
        $where[] = "1=0";
    } else {
        $ph = implode(',', array_fill(0, count($allowedIds), '?'));
        $where[] = "e.patient_id IN ({$ph})";
        $params = array_merge($params, $allowedIds);
    }
}

if ($search) {
    $where[] = "(e.exam_type LIKE ? OR e.lab_clinic LIKE ? OR p.name LIKE ? OR e.title LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($patientId) {
    $where[] = "e.patient_id = ?";
    $params[] = $patientId;
}
if ($examType) {
    $where[] = "e.exam_type = ?";
    $params[] = $examType;
}
if ($specialty) {
    $where[] = "(e.specialty = ? OR e.id IN (SELECT exam_id FROM exam_specialties WHERE specialty_name = ?))";
    $params[] = $specialty;
    $params[] = $specialty;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$exams = $pdo->prepare("
    SELECT e.*, p.name as patient_name,
        (SELECT COUNT(*) FROM exam_files WHERE exam_id = e.id) as file_count
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    {$whereSQL}
    ORDER BY e.exam_date DESC
");
$exams->execute($params);
$exams = $exams->fetchAll();

$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
$examTypes = $pdo->query("SELECT DISTINCT exam_type FROM exams ORDER BY exam_type")->fetchAll(PDO::FETCH_COLUMN);
$specialties = $pdo->query("SELECT DISTINCT specialty_name FROM exam_specialties ORDER BY specialty_name")->fetchAll(PDO::FETCH_COLUMN);
if (empty($specialties)) {
    $specialties = $pdo->query("SELECT DISTINCT specialty FROM exams WHERE specialty IS NOT NULL AND specialty != '' ORDER BY specialty")->fetchAll(PDO::FETCH_COLUMN);
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-clipboard2-pulse me-2"></i>Exames</h1>
    <?php if (isAdmin()): ?>
    <a href="<?= baseUrl('pages/exams/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Exame
    </a>
    <?php endif; ?>
</div>

<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Buscar</label>
            <input type="text" name="search" class="form-control" placeholder="Tipo, lab ou paciente..." value="<?= sanitize($search) ?>">
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
        <div class="col-md-2">
            <label class="form-label">Tipo</label>
            <select name="exam_type" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($examTypes as $t): ?>
                <option value="<?= sanitize($t) ?>" <?= $examType === $t ? 'selected' : '' ?>><?= sanitize($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
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
            <a href="<?= baseUrl('pages/exams/list.php') ?>" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($exams)): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard2-pulse"></i>
            <h4>Nenhum exame encontrado</h4>
            <p>Cadastre o primeiro exame.</p>
            <?php if (isAdmin()): ?>
            <a href="<?= baseUrl('pages/exams/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Exame</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Paciente</th>
                        <th>Especialidade</th>
                        <th>Laboratório</th>
                        <th class="text-center">Arquivos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($exams as $e): ?>
                    <tr>
                        <td><?= formatDate($e['exam_date']) ?></td>
                        <td><span class="badge bg-info"><?= sanitize($e['exam_type']) ?></span></td>
                        <td><a href="<?= baseUrl('pages/patients/view.php?id=' . $e['patient_id']) ?>"><?= sanitize($e['patient_name']) ?></a></td>
                        <td>
                        <?php
                            $specStmt = $pdo->prepare("SELECT specialty_name FROM exam_specialties WHERE exam_id = ?");
                            $specStmt->execute([$e['id']]);
                            $specs = $specStmt->fetchAll(PDO::FETCH_COLUMN);
                            if (empty($specs) && !empty($e['specialty'])) $specs = [$e['specialty']];
                            if (!empty($specs)):
                                foreach ($specs as $sp): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-1"><?= sanitize($sp) ?></span>
                                <?php endforeach;
                            else: echo '-'; endif;
                        ?>
                        </td>
                        <td><?= sanitize($e['lab_clinic'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if ($e['file_count'] > 0): ?>
                            <span class="badge bg-secondary"><i class="bi bi-paperclip me-1"></i><?= $e['file_count'] ?></span>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= baseUrl('pages/exams/view.php?id=' . $e['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <?php if (isAdmin()): ?>
                            <a href="<?= baseUrl('pages/exams/form.php?id=' . $e['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <a href="<?= baseUrl('pages/exams/delete.php?id=' . $e['id']) ?>" class="btn btn-sm btn-outline-danger" data-confirm="Excluir este exame e seus arquivos?"><i class="bi bi-trash"></i></a>
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