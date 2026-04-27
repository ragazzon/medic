<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Controle de acesso
list($accessWhere, $accessParams) = getPatientAccessFilter('p');
$patientJoinWhere = $accessWhere ? "WHERE {$accessWhere}" : '';

// Filtros do dashboard
$filterPatient = intval($_GET['patient_id'] ?? 0);
$filterPeriod = $_GET['period'] ?? '3y'; // Padrão: 3 anos

// Calcular intervalo de datas baseado no período
$periodMap = [
    '6m' => '6 MONTH',
    '1y' => '1 YEAR',
    '3y' => '3 YEAR',
    '5y' => '5 YEAR',
    'all' => null,
];
$dateInterval = $periodMap[$filterPeriod] ?? '3 YEAR';
$dateFilter = $dateInterval ? sqlDateSub($dateInterval) : null;

// Construir cláusulas de filtro extras
function buildExtraWhere($dateCol, $dateFilter, $filterPatient, $tableAlias = '') {
    $parts = [];
    $params = [];
    $prefix = $tableAlias ? "{$tableAlias}." : '';
    if ($dateFilter) {
        $parts[] = "{$prefix}{$dateCol} >= {$dateFilter}";
    }
    if ($filterPatient) {
        $parts[] = "{$prefix}patient_id = ?";
        $params[] = $filterPatient;
    }
    return [$parts, $params];
}

// Estatísticas
if (isAdmin()) {
    $totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    
    // Records com filtros
    $rWhere = [];
    $rParams = [];
    if ($dateFilter) $rWhere[] = "record_date >= {$dateFilter}";
    if ($filterPatient) { $rWhere[] = "patient_id = ?"; $rParams[] = $filterPatient; }
    $rWhereSQL = $rWhere ? 'WHERE ' . implode(' AND ', $rWhere) : '';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medical_records {$rWhereSQL}");
    $stmt->execute($rParams);
    $totalRecords = $stmt->fetchColumn();
    
    // Exames com filtros
    $eWhere = [];
    $eParams = [];
    if ($dateFilter) $eWhere[] = "exam_date >= {$dateFilter}";
    if ($filterPatient) { $eWhere[] = "patient_id = ?"; $eParams[] = $filterPatient; }
    $eWhereSQL = $eWhere ? 'WHERE ' . implode(' AND ', $eWhere) : '';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM exams {$eWhereSQL}");
    $stmt->execute($eParams);
    $totalExams = $stmt->fetchColumn();
    
    // Medicamentos ativos
    $mWhere = ['is_active = 1'];
    $mParams = [];
    if ($filterPatient) { $mWhere[] = "patient_id = ?"; $mParams[] = $filterPatient; }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medications WHERE " . implode(' AND ', $mWhere));
    $stmt->execute($mParams);
    $totalMeds = $stmt->fetchColumn();
    
} else {
    $ids = getAllowedPatientIds();
    $totalPatients = count($ids);
    
    if (empty($ids)) {
        $totalRecords = 0;
        $totalExams = 0;
        $totalMeds = 0;
    } else {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        
        $rWhere = ["patient_id IN ({$ph})"];
        $rParams = $ids;
        if ($dateFilter) $rWhere[] = "record_date >= {$dateFilter}";
        if ($filterPatient) { $rWhere[] = "patient_id = ?"; $rParams[] = $filterPatient; }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM medical_records WHERE " . implode(' AND ', $rWhere));
        $stmt->execute($rParams);
        $totalRecords = $stmt->fetchColumn();
        
        $eWhere = ["patient_id IN ({$ph})"];
        $eParams = $ids;
        if ($dateFilter) $eWhere[] = "exam_date >= {$dateFilter}";
        if ($filterPatient) { $eWhere[] = "patient_id = ?"; $eParams[] = $filterPatient; }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM exams WHERE " . implode(' AND ', $eWhere));
        $stmt->execute($eParams);
        $totalExams = $stmt->fetchColumn();
        
        $mWhere = ["patient_id IN ({$ph})", "is_active = 1"];
        $mParams = $ids;
        if ($filterPatient) { $mWhere[] = "patient_id = ?"; $mParams[] = $filterPatient; }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM medications WHERE " . implode(' AND ', $mWhere));
        $stmt->execute($mParams);
        $totalMeds = $stmt->fetchColumn();
    }
}

// Pacientes para filtro
if (isAdmin()) {
    $patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
} else {
    $ids = getAllowedPatientIds();
    if (!empty($ids)) {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stmtPat = $pdo->prepare("SELECT id, name FROM patients WHERE id IN ({$ph}) ORDER BY name");
        $stmtPat->execute($ids);
        $patients = $stmtPat->fetchAll();
    } else {
        $patients = [];
    }
}

// Últimas consultas
$recentRecordsSQL = "
    SELECT r.*, p.name as patient_name 
    FROM medical_records r 
    JOIN patients p ON r.patient_id = p.id 
    " . ($accessWhere ? "WHERE {$accessWhere}" : '') . "
    " . ($filterPatient ? (($accessWhere ? ' AND ' : ' WHERE ') . "r.patient_id = ?") : '') . "
    ORDER BY r.record_date DESC LIMIT 5
";
$recParams = $accessParams;
if ($filterPatient) $recParams[] = $filterPatient;
$stmt = $pdo->prepare($recentRecordsSQL);
$stmt->execute($recParams);
$recentRecords = $stmt->fetchAll();

// Últimos exames
$recentExamsSQL = "
    SELECT e.*, p.name as patient_name 
    FROM exams e 
    JOIN patients p ON e.patient_id = p.id 
    " . ($accessWhere ? "WHERE {$accessWhere}" : '') . "
    " . ($filterPatient ? (($accessWhere ? ' AND ' : ' WHERE ') . "e.patient_id = ?") : '') . "
    ORDER BY e.exam_date DESC LIMIT 5
";
$exParams = $accessParams;
if ($filterPatient) $exParams[] = $filterPatient;
$stmt = $pdo->prepare($recentExamsSQL);
$stmt->execute($exParams);
$recentExams = $stmt->fetchAll();

// Medicamentos em uso
$medsSQL = "
    SELECT m.*, p.name as patient_name 
    FROM medications m 
    JOIN patients p ON m.patient_id = p.id 
    WHERE m.is_active = 1
    " . ($accessWhere ? "AND {$accessWhere}" : '') . "
    " . ($filterPatient ? "AND m.patient_id = ?" : '') . "
    ORDER BY m.is_continuous DESC, m.name ASC LIMIT 8
";
$medParams = $accessParams;
if ($filterPatient) $medParams[] = $filterPatient;
$stmt = $pdo->prepare($medsSQL);
$stmt->execute($medParams);
$activeMeds = $stmt->fetchAll();

// Dados para gráfico - Atividade por mês
$chartInterval = $dateInterval ?? '3 YEAR';
$chartExtraWhere = $filterPatient ? "AND e.patient_id = ?" : '';
$chartExtraParams = $filterPatient ? [$filterPatient] : [];

$chartDateSub = sqlDateSub($chartInterval);
$dateFormatMonth = sqlDateFormat('e.exam_date', '%Y-%m');
$examsByMonthSQL = "
    SELECT {$dateFormatMonth} as month, COUNT(*) as total
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    " . ($accessWhere ? "WHERE {$accessWhere} AND " : "WHERE ") . "
    e.exam_date >= {$chartDateSub}
    {$chartExtraWhere}
    GROUP BY {$dateFormatMonth}
    ORDER BY month ASC
";
$chartParams = array_merge($accessParams, $chartExtraParams);
$stmt = $pdo->prepare($examsByMonthSQL);
$stmt->execute($chartParams);
$examsByMonth = $stmt->fetchAll();

$months = [];
$monthCounts = [];
foreach ($examsByMonth as $row) {
    $months[] = formatMonthYear($row['month']);
    $monthCounts[] = (int)$row['total'];
}

// Exames por tipo
$typeExtraWhere = $filterPatient ? "AND e.patient_id = ?" : '';
$typeExtraParams = $filterPatient ? [$filterPatient] : [];
$examsByTypeSQL = "
    SELECT e.exam_type, COUNT(*) as total
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    {$patientJoinWhere}
    {$typeExtraWhere}
    GROUP BY e.exam_type
    ORDER BY total DESC
    LIMIT 8
";
$typeParams = array_merge($accessParams, $typeExtraParams);
$stmt = $pdo->prepare($examsByTypeSQL);
$stmt->execute($typeParams);
$examsByType = $stmt->fetchAll();

$typeLabels = [];
$typeCounts = [];
foreach ($examsByType as $row) {
    $typeLabels[] = $row['exam_type'];
    $typeCounts[] = (int)$row['total'];
}

// Período labels
$periodLabels = [
    '6m' => '6 meses',
    '1y' => '1 ano',
    '3y' => '3 anos',
    '5y' => '5 anos',
    'all' => 'Tudo',
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-grid-1x2 me-2"></i>Dashboard</h1>
    <span class="text-muted">Visão geral · <?= $periodLabels[$filterPeriod] ?? '3 anos' ?></span>
</div>

<!-- Filtros do Dashboard -->
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted text-nowrap">Paciente:</small>
                    <select name="patient_id" class="form-select form-select-sm">
                        <option value="">Todos os pacientes</option>
                        <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filterPatient == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-1">
                    <small class="text-muted text-nowrap me-1">Período:</small>
                    <?php foreach ($periodLabels as $key => $label): ?>
                    <button type="submit" name="period" value="<?= $key ?>" class="btn btn-sm <?= $filterPeriod === $key ? 'btn-primary' : 'btn-outline-secondary' ?>">
                        <?= $label ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <a href="<?= baseUrl('pages/dashboard.php') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Limpar</a>
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary fade-in">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-number"><?= $totalPatients ?></div>
            <div class="stat-label">Pacientes</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success fade-in">
            <i class="bi bi-journal-medical stat-icon"></i>
            <div class="stat-number"><?= $totalRecords ?></div>
            <div class="stat-label">Consultas</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-info fade-in">
            <i class="bi bi-clipboard2-pulse stat-icon"></i>
            <div class="stat-number"><?= $totalExams ?></div>
            <div class="stat-label">Exames</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-warning fade-in">
            <i class="bi bi-capsule stat-icon"></i>
            <div class="stat-number"><?= $totalMeds ?></div>
            <div class="stat-label">Medicamentos Ativos</div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Exames por Mês
            </div>
            <div class="card-body">
                <canvas id="examsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Exames por Tipo
            </div>
            <div class="card-body">
                <canvas id="typesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Medicamentos em uso -->
<?php if (!empty($activeMeds)): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-capsule me-2"></i>Medicamentos em Uso</span>
        <a href="<?= baseUrl('pages/medications/list.php?active=1') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Paciente</th>
                        <th>Dosagem</th>
                        <th>Frequência</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($activeMeds as $med): ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= sanitize($med['name']) ?></div>
                        <?php if ($med['active_ingredient']): ?>
                        <small class="text-muted"><?= sanitize($med['active_ingredient']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= baseUrl('pages/patients/view.php?id=' . $med['patient_id']) ?>"><?= sanitize($med['patient_name']) ?></a></td>
                    <td><?= sanitize($med['dosage'] ?? '-') ?></td>
                    <td><?= sanitize($med['frequency'] ?? '-') ?></td>
                    <td>
                        <?php if ($med['is_continuous']): ?>
                        <span class="badge bg-info">Contínuo</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Temporário</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Data -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-medical me-2"></i>Últimas Consultas</span>
                <a href="<?= baseUrl('pages/records/list.php') ?>" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentRecords)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-journal-medical" style="font-size:40px;"></i>
                    <p class="mt-2 mb-0">Nenhuma consulta registrada</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                        <?php foreach ($recentRecords as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= sanitize($r['title']) ?></div>
                                    <small class="text-muted"><?= sanitize($r['patient_name']) ?><?= $r['specialty'] ? ' · ' . sanitize($r['specialty']) : '' ?></small>
                                </td>
                                <td class="text-muted"><?= formatDate($r['record_date']) ?></td>
                                <td class="text-end">
                                    <a href="<?= baseUrl('pages/records/view.php?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2"></i>Exames Recentes</span>
                <a href="<?= baseUrl('pages/exams/list.php') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentExams)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-clipboard2-pulse" style="font-size:40px;"></i>
                    <p class="mt-2 mb-0">Nenhum exame cadastrado</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                        <?php foreach ($recentExams as $e): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= sanitize($e['title']) ?></div>
                                    <small class="text-muted"><?= sanitize($e['patient_name']) ?> · <?= sanitize($e['exam_type']) ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= getStatusBadgeClass($e['status']) ?>"><?= $e['status'] ?></span>
                                </td>
                                <td class="text-muted"><?= formatDate($e['exam_date']) ?></td>
                                <td class="text-end">
                                    <a href="<?= baseUrl('pages/exams/view.php?id=' . $e['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Exames por mês
    const ctx1 = document.getElementById('examsChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Exames',
                    data: <?= json_encode($monthCounts) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // Exames por tipo
    const ctx2 = document.getElementById('typesChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($typeLabels) ?>,
                datasets: [{
                    data: <?= json_encode($typeCounts) ?>,
                    backgroundColor: ['#667eea','#38ef7d','#6dd5ed','#f2c94c','#eb5757','#bb6bd9','#f093fb','#4facfe']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>