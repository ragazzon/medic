<?php
$pageTitle = 'Agenda';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = getConnection();

// Controle de acesso
$filterPatient = intval($_GET['patient_id'] ?? 0);

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

if (!$filterPatient && count($patients) === 1) {
    $filterPatient = $patients[0]['id'];
}

$upcoming = [];
$today = date('Y-m-d');

if ($filterPatient) {
    if (!canAccessPatient($filterPatient)) {
        setFlash('danger', 'Sem permissão para acessar este paciente.');
        redirect(baseUrl('pages/dashboard.php'));
    }

    // Próximas consultas (data >= hoje)
    $stmt = $pdo->prepare("SELECT id, title, record_date as event_date, 'record' as event_type, specialty, doctor_name,
                                   COALESCE(visit_reason, '') as detail, clinic_hospital
                            FROM medical_records 
                            WHERE patient_id = ? AND record_date >= ?
                            ORDER BY record_date ASC");
    $stmt->execute([$filterPatient, $today]);
    $upcoming = array_merge($upcoming, $stmt->fetchAll());

    // Próximos exames (data >= hoje)
    $stmt = $pdo->prepare("SELECT id, title, exam_date as event_date, 'exam' as event_type, 
                                   COALESCE(specialty, exam_type) as specialty, doctor_name,
                                   lab_clinic as detail, status
                            FROM exams 
                            WHERE patient_id = ? AND exam_date >= ?
                            ORDER BY exam_date ASC");
    $stmt->execute([$filterPatient, $today]);
    $upcoming = array_merge($upcoming, $stmt->fetchAll());

    // Ordenar por data asc
    usort($upcoming, function($a, $b) {
        return strcmp($a['event_date'], $b['event_date']);
    });

    // Buscar nome do paciente
    $stmtName = $pdo->prepare("SELECT name FROM patients WHERE id = ?");
    $stmtName->execute([$filterPatient]);
    $patientName = $stmtName->fetchColumn();
}

require_once __DIR__ . '/../includes/header.php';

// Helpers para o calendário
$currentMonth = intval($_GET['month'] ?? date('n'));
$currentYear = intval($_GET['year'] ?? date('Y'));
if ($currentMonth < 1) { $currentMonth = 12; $currentYear--; }
if ($currentMonth > 12) { $currentMonth = 1; $currentYear++; }

$firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$daysInMonth = date('t', $firstDay);
$startDow = date('w', $firstDay); // 0=Sun
$monthNames = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

// Indexar eventos por data para o calendário
$eventsByDate = [];
if ($filterPatient) {
    foreach ($upcoming as $ev) {
        $d = $ev['event_date'];
        if (!isset($eventsByDate[$d])) $eventsByDate[$d] = [];
        $eventsByDate[$d][] = $ev;
    }
    // Também buscar eventos do mês corrente (incluindo passados desse mês)
    $monthStart = sprintf('%04d-%02d-01', $currentYear, $currentMonth);
    $monthEnd = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $daysInMonth);
    
    $stmt = $pdo->prepare("SELECT id, title, record_date as event_date, 'record' as event_type, specialty, doctor_name
                            FROM medical_records 
                            WHERE patient_id = ? AND record_date BETWEEN ? AND ?");
    $stmt->execute([$filterPatient, $monthStart, $monthEnd]);
    foreach ($stmt->fetchAll() as $ev) {
        $d = $ev['event_date'];
        if (!isset($eventsByDate[$d])) $eventsByDate[$d] = [];
        // Evitar duplicatas
        $exists = false;
        foreach ($eventsByDate[$d] as $existing) {
            if ($existing['event_type'] === $ev['event_type'] && $existing['id'] === $ev['id']) { $exists = true; break; }
        }
        if (!$exists) $eventsByDate[$d][] = $ev;
    }
    
    $stmt = $pdo->prepare("SELECT id, title, exam_date as event_date, 'exam' as event_type, COALESCE(specialty, exam_type) as specialty, doctor_name, status
                            FROM exams 
                            WHERE patient_id = ? AND exam_date BETWEEN ? AND ?");
    $stmt->execute([$filterPatient, $monthStart, $monthEnd]);
    foreach ($stmt->fetchAll() as $ev) {
        $d = $ev['event_date'];
        if (!isset($eventsByDate[$d])) $eventsByDate[$d] = [];
        $exists = false;
        foreach ($eventsByDate[$d] as $existing) {
            if ($existing['event_type'] === $ev['event_type'] && $existing['id'] === $ev['id']) { $exists = true; break; }
        }
        if (!$exists) $eventsByDate[$d][] = $ev;
    }
}

$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>

<div class="page-header">
    <h1><i class="bi bi-calendar2-week me-2"></i>Agenda</h1>
</div>

<!-- Filtro de paciente -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="month" value="<?= $currentMonth ?>">
            <input type="hidden" name="year" value="<?= $currentYear ?>">
            <div class="col-md-5">
                <label class="form-label small">Paciente</label>
                <select name="patient_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Selecione um paciente</option>
                    <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filterPatient == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if (!$filterPatient): ?>
<div class="card">
    <div class="card-body empty-state py-5">
        <i class="bi bi-calendar2-week" style="font-size:60px;"></i>
        <h5 class="mt-3">Selecione um paciente</h5>
        <p class="text-muted">Escolha um paciente para ver sua agenda de consultas e exames.</p>
    </div>
</div>
<?php else: ?>

<!-- Calendário Visual -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <a href="?patient_id=<?= $filterPatient ?>&month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h5 class="mb-0 fw-bold"><?= $monthNames[$currentMonth] ?> <?= $currentYear ?></h5>
        <a href="?patient_id=<?= $filterPatient ?>&month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 planner-calendar">
                <thead>
                    <tr class="text-center">
                        <th class="text-danger bg-light" style="width:14.28%">Dom</th>
                        <th class="bg-light" style="width:14.28%">Seg</th>
                        <th class="bg-light" style="width:14.28%">Ter</th>
                        <th class="bg-light" style="width:14.28%">Qua</th>
                        <th class="bg-light" style="width:14.28%">Qui</th>
                        <th class="bg-light" style="width:14.28%">Sex</th>
                        <th class="text-primary bg-light" style="width:14.28%">Sáb</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $day = 1;
                    $totalCells = $startDow + $daysInMonth;
                    $rows = ceil($totalCells / 7);
                    
                    for ($row = 0; $row < $rows; $row++):
                    ?>
                    <tr>
                        <?php for ($col = 0; $col < 7; $col++):
                            $cellIdx = $row * 7 + $col;
                            $isValidDay = ($cellIdx >= $startDow && $day <= $daysInMonth);
                            $dateStr = $isValidDay ? sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day) : '';
                            $isToday = ($dateStr === $today);
                            $isPast = ($dateStr && $dateStr < $today);
                            $dayEvents = $isValidDay ? ($eventsByDate[$dateStr] ?? []) : [];
                        ?>
                        <td class="planner-cell <?= $isToday ? 'planner-today' : '' ?> <?= $isPast ? 'planner-past' : '' ?>" style="vertical-align:top;height:100px;padding:4px;">
                            <?php if ($isValidDay): ?>
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="planner-day-number <?= $isToday ? 'badge bg-primary rounded-pill' : '' ?>"><?= $day ?></span>
                                    <?php if (isAdmin() && !$isPast): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm p-0 text-muted" type="button" data-bs-toggle="dropdown" title="Adicionar">
                                            <i class="bi bi-plus-circle" style="font-size:.75rem;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="font-size:.8rem;">
                                            <li><a class="dropdown-item" href="<?= baseUrl('pages/records/form.php?patient_id=' . $filterPatient) ?>"><i class="bi bi-journal-medical me-1"></i>Consulta</a></li>
                                            <li><a class="dropdown-item" href="<?= baseUrl('pages/exams/form.php?patient_id=' . $filterPatient) ?>"><i class="bi bi-clipboard2-pulse me-1"></i>Exame</a></li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php foreach ($dayEvents as $ev): ?>
                                <a href="<?= baseUrl($ev['event_type'] === 'record' ? 'pages/records/view.php?id=' . $ev['id'] : 'pages/exams/view.php?id=' . $ev['id']) ?>"
                                   class="d-block text-decoration-none mb-1 planner-event <?= $ev['event_type'] === 'record' ? 'planner-event-record' : 'planner-event-exam' ?>"
                                   title="<?= sanitize($ev['title']) ?><?= !empty($ev['specialty']) ? ' — ' . sanitize($ev['specialty']) : '' ?>">
                                    <i class="bi <?= $ev['event_type'] === 'record' ? 'bi-journal-medical' : 'bi-clipboard2-pulse' ?>"></i>
                                    <span class="text-truncate"><?= sanitize(mb_strimwidth($ev['title'], 0, 18, '…')) ?></span>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if ($isValidDay) $day++; ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Lista de Próximos Eventos -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check me-2"></i>Próximos Compromissos — <?= sanitize($patientName) ?>
    </div>
    <div class="card-body">
        <?php if (empty($upcoming)): ?>
        <div class="text-center py-4">
            <i class="bi bi-calendar-check text-muted" style="font-size:48px;"></i>
            <h6 class="mt-3 text-muted">Nenhum compromisso futuro</h6>
            <p class="text-muted small">Não há consultas ou exames agendados.</p>
            <?php if (isAdmin()): ?>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?= baseUrl('pages/records/form.php?patient_id=' . $filterPatient) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nova Consulta
                </a>
                <a href="<?= baseUrl('pages/exams/form.php?patient_id=' . $filterPatient) ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-plus-lg me-1"></i>Novo Exame
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        
        <?php
        $lastDate = '';
        foreach ($upcoming as $ev):
            $evDate = $ev['event_date'];
            $isEventToday = ($evDate === $today);
            $isTomorrow = ($evDate === date('Y-m-d', strtotime('+1 day')));
            $isThisWeek = ($evDate <= date('Y-m-d', strtotime('+7 days')));
            
            // Agrupar por data
            if ($evDate !== $lastDate):
                $lastDate = $evDate;
                $dayLabel = formatDate($evDate);
                if ($isEventToday) $dayLabel = '🔴 Hoje — ' . $dayLabel;
                elseif ($isTomorrow) $dayLabel = '🟡 Amanhã — ' . $dayLabel;
        ?>
        <h6 class="fw-semibold mt-3 mb-2 <?= $isEventToday ? 'text-danger' : ($isTomorrow ? 'text-warning' : 'text-muted') ?>">
            <i class="bi bi-calendar-event me-1"></i><?= $dayLabel ?>
            <?php
                $daysUntil = (strtotime($evDate) - strtotime($today)) / 86400;
                if ($daysUntil > 1): ?>
                <small class="text-muted fw-normal">(em <?= intval($daysUntil) ?> dias)</small>
            <?php endif; ?>
        </h6>
        <?php endif; ?>

        <div class="card mb-2 border-start border-4 <?= $ev['event_type'] === 'record' ? 'border-info' : 'border-warning' ?> <?= $isEventToday ? 'bg-light' : '' ?>">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($ev['event_type'] === 'record'): ?>
                            <span class="badge bg-info"><i class="bi bi-journal-medical"></i></span>
                            <span class="badge bg-info bg-opacity-25 text-info small">Consulta</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="bi bi-clipboard2-pulse"></i></span>
                            <span class="badge bg-warning bg-opacity-25 text-warning small">Exame</span>
                        <?php endif; ?>
                        <div>
                            <span class="fw-semibold"><?= sanitize($ev['title']) ?></span>
                            <?php if (!empty($ev['specialty'])): ?>
                                <small class="text-muted ms-2"><?= sanitize($ev['specialty']) ?></small>
                            <?php endif; ?>
                            <?php if (!empty($ev['doctor_name'])): ?>
                                <small class="text-muted ms-1">· Dr(a). <?= sanitize($ev['doctor_name']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($ev['event_type'] === 'exam' && !empty($ev['status'])): ?>
                            <span class="badge <?= getStatusBadgeClass($ev['status']) ?> small"><?= $ev['status'] ?></span>
                        <?php endif; ?>
                        <a href="<?= baseUrl($ev['event_type'] === 'record' ? 'pages/records/view.php?id=' . $ev['id'] : 'pages/exams/view.php?id=' . $ev['id']) ?>" 
                           class="btn btn-sm btn-outline-<?= $ev['event_type'] === 'record' ? 'info' : 'warning' ?> py-0">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
                <?php if (!empty(trim($ev['detail'] ?? ''))): ?>
                    <small class="text-muted d-block mt-1"><?= sanitize(substr($ev['detail'], 0, 120)) ?></small>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<style>
.planner-calendar { table-layout: fixed; }
.planner-cell { transition: background .15s; min-height: 90px; }
.planner-cell:hover { background: #f8f9fa; }
.planner-today { background: #e8f4fd !important; }
.planner-past { opacity: .55; }
.planner-day-number { font-size: .85rem; font-weight: 600; color: #495057; }
.planner-event { 
    font-size: .7rem; 
    padding: 2px 4px; 
    border-radius: 4px; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis;
    display: flex !important;
    align-items: center;
    gap: 3px;
}
.planner-event-record { 
    background: #d1ecf1; 
    color: #0c5460; 
    border-left: 3px solid #17a2b8; 
}
.planner-event-record:hover { background: #bee5eb; color: #0c5460; }
.planner-event-exam { 
    background: #fff3cd; 
    color: #856404; 
    border-left: 3px solid #ffc107; 
}
.planner-event-exam:hover { background: #ffeeba; color: #856404; }

@media (max-width: 768px) {
    .planner-cell { height: 70px !important; padding: 2px !important; }
    .planner-day-number { font-size: .75rem; }
    .planner-event { font-size: .6rem; padding: 1px 2px; }
    .planner-event span { display: none; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>