<?php
$pageTitle = 'Medicamentos';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Controle de acesso
list($accessWhere, $accessParams) = getPatientAccessFilter('p');

// Filtros
$filterPatient = intval($_GET['patient_id'] ?? 0);
$filterActive = $_GET['active'] ?? '';
$filterContinuous = $_GET['continuous'] ?? '';
$filterSpecialty = trim($_GET['specialty'] ?? '');
$search = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if ($accessWhere) {
    $where[] = $accessWhere;
    $params = array_merge($params, $accessParams);
}

if ($filterPatient) {
    $where[] = "m.patient_id = ?";
    $params[] = $filterPatient;
}
if ($filterActive !== '') {
    $where[] = "m.is_active = ?";
    $params[] = intval($filterActive);
}
if ($filterContinuous !== '') {
    $where[] = "m.is_continuous = ?";
    $params[] = intval($filterContinuous);
}
if ($filterSpecialty) {
    $where[] = "m.specialty = ?";
    $params[] = $filterSpecialty;
}
if ($search) {
    $where[] = "(m.name LIKE ? OR m.active_ingredient LIKE ? OR m.prescriber LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT m.*, p.name as patient_name 
        FROM medications m 
        JOIN patients p ON m.patient_id = p.id 
        {$whereSQL}
        ORDER BY m.is_active DESC, m.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medications = $stmt->fetchAll();

// Pacientes para filtro
if (isAdmin()) {
    $patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
} else {
    $ids = getAllowedPatientIds();
    if (!empty($ids)) {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stmt2 = $pdo->prepare("SELECT id, name FROM patients WHERE id IN ({$ph}) ORDER BY name");
        $stmt2->execute($ids);
        $patients = $stmt2->fetchAll();
    } else {
        $patients = [];
    }
}

$medSpecialties = $pdo->query("SELECT DISTINCT specialty FROM medications WHERE specialty IS NOT NULL AND specialty != '' ORDER BY specialty")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-capsule me-2"></i>Medicamentos</h1>
    <?php if (isAdmin()): ?>
    <a href="<?= baseUrl('pages/medications/form.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Medicamento
    </a>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Paciente</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filterPatient == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select name="active" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="1" <?= $filterActive === '1' ? 'selected' : '' ?>>Em uso</option>
                    <option value="0" <?= $filterActive === '0' ? 'selected' : '' ?>>Suspenso</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Tipo</label>
                <select name="continuous" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="1" <?= $filterContinuous === '1' ? 'selected' : '' ?>>Uso contínuo</option>
                    <option value="0" <?= $filterContinuous === '0' ? 'selected' : '' ?>>Temporário</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Especialidade</label>
                <select name="specialty" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($medSpecialties as $s): ?>
                    <option value="<?= sanitize($s) ?>" <?= $filterSpecialty === $s ? 'selected' : '' ?>><?= sanitize($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Busca</label>
                <input type="text" name="q" class="form-control form-control-sm" value="<?= sanitize($search) ?>" placeholder="Nome, princípio ativo...">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i> Filtrar</button>
                <a href="<?= baseUrl('pages/medications/list.php') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Lista -->
<?php if (empty($medications)): ?>
<div class="card">
    <div class="card-body empty-state py-5">
        <i class="bi bi-capsule" style="font-size:60px;"></i>
        <h5 class="mt-3">Nenhum medicamento encontrado</h5>
        <p class="text-muted">Cadastre medicamentos para acompanhar prescrições e uso.</p>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Paciente</th>
                        <th>Dosagem</th>
                        <th>Frequência</th>
                        <th>Prescritor</th>
                        <th>Início</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($medications as $m): ?>
                    <tr class="<?= !$m['is_active'] ? 'table-light text-muted' : '' ?>">
                        <td>
                            <div class="fw-semibold"><?= sanitize($m['name']) ?></div>
                            <?php if ($m['active_ingredient']): ?>
                            <small class="text-muted"><?= sanitize($m['active_ingredient']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= baseUrl('pages/patients/view.php?id=' . $m['patient_id']) ?>"><?= sanitize($m['patient_name']) ?></a>
                        </td>
                        <td><?= sanitize($m['dosage'] ?? '-') ?></td>
                        <td><?= sanitize($m['frequency'] ?? '-') ?></td>
                        <td><?= sanitize($m['prescriber'] ?? '-') ?></td>
                        <td><?= formatDate($m['start_date']) ?></td>
                        <td>
                            <?php if ($m['is_active']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Em uso</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Suspenso</span>
                            <?php endif; ?>
                            <?php if ($m['is_continuous']): ?>
                                <span class="badge bg-info">Contínuo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (isAdmin()): ?>
                            <a href="<?= baseUrl('pages/medications/form.php?id=' . $m['id']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= baseUrl('pages/medications/delete.php?id=' . $m['id']) ?>" class="btn btn-sm btn-outline-danger" data-confirm="Excluir este medicamento?" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted small">
        <?= count($medications) ?> medicamento(s) encontrado(s)
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>