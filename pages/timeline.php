<?php
$pageTitle = 'Linha do Tempo';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Controle de acesso
list($accessWhere, $accessParams) = getPatientAccessFilter('p');

// Filtros
$filterPatient = intval($_GET['patient_id'] ?? 0);
$filterYear = intval($_GET['year'] ?? 0);
$filterSpecialty = trim($_GET['specialty'] ?? '');

// Pacientes disponíveis
if (isAdmin()) {
    $patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
} else {
    $ids = getAllowedPatientIds();
    if (!empty($ids)) {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stmtP = $pdo->prepare("SELECT id, name FROM patients WHERE id IN ({$ph}) ORDER BY name");
        $stmtP->execute($ids);
        $patients = $stmtP->fetchAll();
    } else {
        $patients = [];
    }
}

// Se não tem filtro de paciente e só tem um, seleciona automaticamente
if (!$filterPatient && count($patients) === 1) {
    $filterPatient = $patients[0]['id'];
}

$events = [];

if ($filterPatient) {
    // Verificar acesso
    if (!canAccessPatient($filterPatient)) {
        setFlash('danger', 'Sem permissão para acessar este paciente.');
        redirect(baseUrl('pages/dashboard.php'));
    }

    $specFilter = $filterSpecialty ? "AND specialty = ?" : '';

    // Consultas/Prontuários
    $yearFilterRec = $filterYear ? "AND " . sqlYear('record_date') . " = {$filterYear}" : '';
    $sql = "SELECT id, title, record_date as event_date, 'record' as event_type, specialty, doctor_name,
                   COALESCE(visit_reason, diagnosis, symptoms, '') as detail
            FROM medical_records 
            WHERE patient_id = ? {$yearFilterRec} {$specFilter}";
    $stmt = $pdo->prepare($sql);
    $execParams = [$filterPatient];
    if ($filterSpecialty) $execParams[] = $filterSpecialty;
    $stmt->execute($execParams);
    $events = array_merge($events, $stmt->fetchAll());

    // Exames
    $yearFilterExam = $filterYear ? "AND " . sqlYear('exam_date') . " = {$filterYear}" : '';
    $specFilterExam = $filterSpecialty ? "AND (specialty = ? OR exam_type = ?)" : '';
    $sql = "SELECT id, title, exam_date as event_date, 'exam' as event_type, COALESCE(specialty, exam_type) as specialty, doctor_name,
                   COALESCE(results, '') as detail, status
            FROM exams 
            WHERE patient_id = ? {$yearFilterExam} {$specFilterExam}";
    $stmt = $pdo->prepare($sql);
    $execParams = [$filterPatient];
    if ($filterSpecialty) { $execParams[] = $filterSpecialty; $execParams[] = $filterSpecialty; }
    $stmt->execute($execParams);
    $events = array_merge($events, $stmt->fetchAll());

    // Medicamentos (início)
    $yearFilterMed = $filterYear ? "AND " . sqlYear('start_date') . " = {$filterYear}" : '';
    $concatDetail = isSQLite() ? "(COALESCE(dosage,'') || ' ' || COALESCE(frequency,''))" : "CONCAT(COALESCE(dosage,''), ' ', COALESCE(frequency,''))";
    $sql = "SELECT id, name as title, start_date as event_date, 'medication_start' as event_type, specialty, prescriber as doctor_name,
                   {$concatDetail} as detail, is_active, is_continuous
            FROM medications 
            WHERE patient_id = ? AND start_date IS NOT NULL {$yearFilterMed} {$specFilter}";
    $stmt = $pdo->prepare($sql);
    $execParams = [$filterPatient];
    if ($filterSpecialty) $execParams[] = $filterSpecialty;
    $stmt->execute($execParams);
    $events = array_merge($events, $stmt->fetchAll());

    // Ordenar por data desc
    usort($events, function($a, $b) {
        return strcmp($b['event_date'], $a['event_date']);
    });

    // Anos disponíveis para filtro
    $yrRec = sqlYear('record_date');
    $yrExam = sqlYear('exam_date');
    $yrMed = sqlYear('start_date');
    $yearsSQL = "SELECT DISTINCT y FROM (
        SELECT {$yrRec} as y FROM medical_records WHERE patient_id = ?
        UNION SELECT {$yrExam} FROM exams WHERE patient_id = ?
        UNION SELECT {$yrMed} FROM medications WHERE patient_id = ? AND start_date IS NOT NULL
    ) years WHERE y IS NOT NULL ORDER BY y DESC";
    $stmtY = $pdo->prepare($yearsSQL);
    $stmtY->execute([$filterPatient, $filterPatient, $filterPatient]);
    $availableYears = $stmtY->fetchAll(PDO::FETCH_COLUMN);

    // Especialidades disponíveis para filtro
    $specSQL = "SELECT DISTINCT s FROM (
        SELECT specialty as s FROM medical_records WHERE patient_id = ? AND specialty IS NOT NULL AND specialty != ''
        UNION SELECT specialty FROM exams WHERE patient_id = ? AND specialty IS NOT NULL AND specialty != ''
        UNION SELECT exam_type FROM exams WHERE patient_id = ? AND exam_type IS NOT NULL AND exam_type != ''
        UNION SELECT specialty FROM medications WHERE patient_id = ? AND specialty IS NOT NULL AND specialty != ''
    ) specs ORDER BY s";
    $stmtS = $pdo->prepare($specSQL);
    $stmtS->execute([$filterPatient, $filterPatient, $filterPatient, $filterPatient]);
    $availableSpecialties = $stmtS->fetchAll(PDO::FETCH_COLUMN);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-clock-history me-2"></i>Linha do Tempo</h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">Paciente</label>
                <select name="patient_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Selecione um paciente</option>
                    <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filterPatient == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($filterPatient): ?>
            <div class="col-md-2">
                <label class="form-label small">Ano</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php if (!empty($availableYears)): foreach ($availableYears as $y): ?>
                    <option value="<?= $y ?>" <?= $filterYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Especialidade</label>
                <select name="specialty" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php if (!empty($availableSpecialties)): foreach ($availableSpecialties as $s): ?>
                    <option value="<?= sanitize($s) ?>" <?= $filterSpecialty === $s ? 'selected' : '' ?>><?= sanitize($s) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
                <?php if ($filterYear || $filterSpecialty): ?>
                <a href="<?= baseUrl('pages/timeline.php?patient_id=' . $filterPatient) ?>" class="btn btn-outline-secondary btn-sm ms-1"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!$filterPatient): ?>
<div class="card">
    <div class="card-body empty-state py-5">
        <i class="bi bi-clock-history" style="font-size:60px;"></i>
        <h5 class="mt-3">Selecione um paciente</h5>
        <p class="text-muted">Escolha um paciente para ver sua linha do tempo médica completa.</p>
    </div>
</div>
<?php elseif (empty($events)): ?>
<div class="card">
    <div class="card-body empty-state py-5">
        <i class="bi bi-calendar-x" style="font-size:60px;"></i>
        <h5 class="mt-3">Nenhum evento encontrado</h5>
        <p class="text-muted">Não há registros médicos para o período selecionado.</p>
    </div>
</div>
<?php else: ?>

<!-- Resumo -->
<div class="row g-3 mb-4">
    <?php
    $countRecords = count(array_filter($events, fn($e) => $e['event_type'] === 'record'));
    $countExams = count(array_filter($events, fn($e) => $e['event_type'] === 'exam'));
    $countMeds = count(array_filter($events, fn($e) => $e['event_type'] === 'medication_start'));
    ?>
    <div class="col-md-3">
        <div class="card bg-primary bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-primary"><?= count($events) ?></div>
                <small class="text-muted">Total de Eventos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-info"><?= $countRecords ?></div>
                <small class="text-muted">Consultas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-warning"><?= $countExams ?></div>
                <small class="text-muted">Exames</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-success"><?= $countMeds ?></div>
                <small class="text-muted">Medicamentos</small>
            </div>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="timeline-wrapper">
    <?php 
    $lastMonth = '';
    foreach ($events as $event):
        $eventMonth = date('F Y', strtotime($event['event_date']));
        $monthNames = ['January'=>'Janeiro','February'=>'Fevereiro','March'=>'Março','April'=>'Abril','May'=>'Maio','June'=>'Junho','July'=>'Julho','August'=>'Agosto','September'=>'Setembro','October'=>'Outubro','November'=>'Novembro','December'=>'Dezembro'];
        $eventMonthPt = preg_replace_callback('/(\w+)/', function($m) use ($monthNames) { return $monthNames[$m[1]] ?? $m[1]; }, $eventMonth);
        
        if ($eventMonth !== $lastMonth):
            $lastMonth = $eventMonth;
    ?>
    <h6 class="text-muted fw-semibold mt-4 mb-3 border-bottom pb-2">
        <i class="bi bi-calendar3 me-2"></i><?= $eventMonthPt ?>
    </h6>
    <?php endif; ?>
    
    <div class="card mb-2 border-start border-4 
        <?php if ($event['event_type'] === 'record'): ?>border-info<?php elseif ($event['event_type'] === 'exam'): ?>border-warning<?php else: ?>border-success<?php endif; ?>">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <?php if ($event['event_type'] === 'record'): ?>
                        <span class="badge bg-info"><i class="bi bi-journal-medical"></i></span>
                    <?php elseif ($event['event_type'] === 'exam'): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clipboard2-pulse"></i></span>
                    <?php else: ?>
                        <span class="badge bg-success"><i class="bi bi-capsule"></i></span>
                    <?php endif; ?>
                    
                    <div>
                        <span class="fw-semibold"><?= sanitize($event['title']) ?></span>
                        <?php if (!empty($event['specialty'])): ?>
                            <small class="text-muted ms-2"><?= sanitize($event['specialty']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($event['doctor_name'])): ?>
                            <small class="text-muted ms-1">· Dr(a). <?= sanitize($event['doctor_name']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted"><?= formatDate($event['event_date']) ?></small>
                    <?php if ($event['event_type'] === 'record'): ?>
                        <a href="<?= baseUrl('pages/records/view.php?id=' . $event['id']) ?>" class="btn btn-sm btn-outline-info py-0"><i class="bi bi-eye"></i></a>
                    <?php elseif ($event['event_type'] === 'exam'): ?>
                        <?php if (!empty($event['status'])): ?>
                            <span class="badge <?= getStatusBadgeClass($event['status']) ?> small"><?= $event['status'] ?></span>
                        <?php endif; ?>
                        <a href="<?= baseUrl('pages/exams/view.php?id=' . $event['id']) ?>" class="btn btn-sm btn-outline-warning py-0"><i class="bi bi-eye"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty(trim($event['detail'] ?? ''))): ?>
                <small class="text-muted d-block mt-1 text-truncate" style="max-width:600px;"><?= sanitize(substr($event['detail'], 0, 150)) ?></small>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>