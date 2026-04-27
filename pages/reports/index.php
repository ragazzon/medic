<?php
$pageTitle = 'Relatórios';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Controle de acesso
list($accessWhere, $accessParams) = getPatientAccessFilter('p');
$patientJoinWhere = $accessWhere ? "WHERE {$accessWhere}" : '';

// Total de pacientes
if (isAdmin()) {
    $totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
} else {
    $ids = getAllowedPatientIds();
    $totalPatients = count($ids);
}

// Exames por mês (últimos 12 meses)
$dfMonth = sqlDateFormat('e.exam_date', '%Y-%m');
$ds12m = sqlDateSub('12 MONTH');
$examsByMonthSQL = "
    SELECT {$dfMonth} as month, COUNT(*) as total
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    " . ($accessWhere ? "WHERE {$accessWhere} AND " : "WHERE ") . "
    e.exam_date >= {$ds12m}
    GROUP BY {$dfMonth}
    ORDER BY month ASC
";
$stmt = $pdo->prepare($examsByMonthSQL);
$stmt->execute($accessParams);
$examsByMonth = $stmt->fetchAll();

$months = [];
$monthCounts = [];
foreach ($examsByMonth as $row) {
    $months[] = formatMonthYear($row['month']);
    $monthCounts[] = (int)$row['total'];
}

// Exames por tipo
$examsByTypeSQL = "
    SELECT e.exam_type, COUNT(*) as total
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    {$patientJoinWhere}
    GROUP BY e.exam_type
    ORDER BY total DESC
";
$stmt = $pdo->prepare($examsByTypeSQL);
$stmt->execute($accessParams);
$examsByType = $stmt->fetchAll();

$typeLabels = [];
$typeCounts = [];
foreach ($examsByType as $row) {
    $typeLabels[] = $row['exam_type'];
    $typeCounts[] = (int)$row['total'];
}

// Exames por status
$examsByStatusSQL = "
    SELECT e.status, COUNT(*) as total
    FROM exams e
    JOIN patients p ON e.patient_id = p.id
    {$patientJoinWhere}
    GROUP BY e.status
    ORDER BY total DESC
";
$stmt = $pdo->prepare($examsByStatusSQL);
$stmt->execute($accessParams);
$examsByStatus = $stmt->fetchAll();

$statusLabels = [];
$statusCounts = [];
foreach ($examsByStatus as $row) {
    $statusLabels[] = $row['status'] ?: 'Sem status';
    $statusCounts[] = (int)$row['total'];
}

// Prontuários por categoria
$recordsByCatSQL = "
    SELECT mr.category, COUNT(*) as total
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.id
    {$patientJoinWhere}
    GROUP BY mr.category
    ORDER BY total DESC
";
$stmt = $pdo->prepare($recordsByCatSQL);
$stmt->execute($accessParams);
$recordsByCat = $stmt->fetchAll();

$catLabels = [];
$catCounts = [];
foreach ($recordsByCat as $row) {
    $catLabels[] = $row['category'] ?: 'Sem categoria';
    $catCounts[] = (int)$row['total'];
}

// Pacientes por tipo sanguíneo
$bloodTypeSQL = "
    SELECT blood_type, COUNT(*) as total
    FROM patients p
    {$patientJoinWhere}
    GROUP BY blood_type
    HAVING blood_type IS NOT NULL AND blood_type != ''
    ORDER BY total DESC
";
$stmt = $pdo->prepare($bloodTypeSQL);
$stmt->execute($accessParams);
$bloodTypes = $stmt->fetchAll();

$bloodLabels = [];
$bloodCounts = [];
foreach ($bloodTypes as $row) {
    $bloodLabels[] = $row['blood_type'];
    $bloodCounts[] = (int)$row['total'];
}

// Totais gerais
$totalExamsSQL = "SELECT COUNT(*) FROM exams e JOIN patients p ON e.patient_id = p.id {$patientJoinWhere}";
$stmt = $pdo->prepare($totalExamsSQL);
$stmt->execute($accessParams);
$totalExams = $stmt->fetchColumn();

$totalRecordsSQL = "SELECT COUNT(*) FROM medical_records mr JOIN patients p ON mr.patient_id = p.id {$patientJoinWhere}";
$stmt = $pdo->prepare($totalRecordsSQL);
$stmt->execute($accessParams);
$totalRecords = $stmt->fetchColumn();

$totalFilesSQL = "SELECT COUNT(*) FROM exam_files ef JOIN exams e ON ef.exam_id = e.id JOIN patients p ON e.patient_id = p.id {$patientJoinWhere}";
$stmt = $pdo->prepare($totalFilesSQL);
$stmt->execute($accessParams);
$totalFiles = $stmt->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart-line me-2"></i>Relatórios</h1>
</div>

<!-- Estatísticas -->
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
            <i class="bi bi-file-medical stat-icon"></i>
            <div class="stat-number"><?= $totalRecords ?></div>
            <div class="stat-label">Prontuários</div>
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
            <i class="bi bi-file-earmark-image stat-icon"></i>
            <div class="stat-number"><?= $totalFiles ?></div>
            <div class="stat-label">Arquivos</div>
        </div>
    </div>
</div>

<!-- Gráficos linha 1 -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Exames por Mês (últimos 12 meses)</div>
            <div class="card-body">
                <canvas id="examsMonthChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Exames por Tipo</div>
            <div class="card-body">
                <canvas id="examsTypeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos linha 2 -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-circle-half me-2"></i>Exames por Status</div>
            <div class="card-body">
                <canvas id="examsStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-medical me-2"></i>Prontuários por Categoria</div>
            <div class="card-body">
                <canvas id="recordsCatChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-droplet me-2"></i>Tipo Sanguíneo</div>
            <div class="card-body">
                <canvas id="bloodTypeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = ['#667eea','#38ef7d','#6dd5ed','#f2c94c','#eb5757','#bb6bd9','#2d9cdb','#f2994a','#27ae60','#e74c3c'];
    const statusColors = {'Normal':'#38ef7d','Alterado':'#eb5757','Aguardando':'#f2c94c','Indefinido':'#6c757d','Sem status':'#adb5bd'};

    // Exames por mês
    new Chart(document.getElementById('examsMonthChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Exames',
                data: <?= json_encode($monthCounts) ?>,
                backgroundColor: 'rgba(102, 126, 234, 0.7)',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Exames por tipo
    new Chart(document.getElementById('examsTypeChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($typeLabels) ?>,
            datasets: [{ data: <?= json_encode($typeCounts) ?>, backgroundColor: colors }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
    });

    // Exames por status
    const sLabels = <?= json_encode($statusLabels) ?>;
    const sColors = sLabels.map(l => statusColors[l] || '#6c757d');
    new Chart(document.getElementById('examsStatusChart'), {
        type: 'doughnut',
        data: {
            labels: sLabels,
            datasets: [{ data: <?= json_encode($statusCounts) ?>, backgroundColor: sColors }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
    });

    // Prontuários por categoria
    new Chart(document.getElementById('recordsCatChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($catLabels) ?>,
            datasets: [{ data: <?= json_encode($catCounts) ?>, backgroundColor: colors }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
    });

    // Tipo sanguíneo
    new Chart(document.getElementById('bloodTypeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($bloodLabels) ?>,
            datasets: [{
                label: 'Pacientes',
                data: <?= json_encode($bloodCounts) ?>,
                backgroundColor: '#eb5757',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>