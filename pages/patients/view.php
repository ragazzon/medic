<?php
$pageTitle = 'Paciente';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    setFlash('danger', 'Paciente não encontrado.');
    redirect(baseUrl('pages/patients/list.php'));
}

// Controle de acesso
if (!canAccessPatient($id)) {
    setFlash('danger', 'Você não tem permissão para acessar este paciente.');
    redirect(baseUrl('pages/patients/list.php'));
}

$pageTitle = $patient['name'];

// Arquivos genéricos do paciente
$patientFilesStmt = $pdo->prepare("SELECT * FROM patient_files WHERE patient_id = ? ORDER BY created_at DESC");
$patientFilesStmt->execute([$id]);
$patientFiles = $patientFilesStmt->fetchAll();

// Prontuários
$records = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY record_date DESC");
$records->execute([$id]);
$records = $records->fetchAll();

// Exames
$exams = $pdo->prepare("SELECT * FROM exams WHERE patient_id = ? ORDER BY exam_date DESC");
$exams->execute([$id]);
$exams = $exams->fetchAll();

// Medicamentos ativos
$medsStmt = $pdo->prepare("SELECT * FROM medications WHERE patient_id = ? AND is_active = 1 ORDER BY is_continuous DESC, name ASC");
$medsStmt->execute([$id]);
$activeMeds = $medsStmt->fetchAll();

// Total de medicamentos (ativos + inativos)
$totalMedsStmt = $pdo->prepare("SELECT COUNT(*) FROM medications WHERE patient_id = ?");
$totalMedsStmt->execute([$id]);
$totalMeds = $totalMedsStmt->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-person me-2"></i><?= sanitize($patient['name']) ?></h1>
    <div>
        <?php if (isAdmin()): ?>
        <a href="<?= baseUrl('pages/patients/form.php?id=' . $id) ?>" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= baseUrl('pages/timeline.php?patient_id=' . $id) ?>" class="btn btn-outline-info"><i class="bi bi-clock-history me-1"></i>Linha do Tempo</a>
        <a href="<?= baseUrl('pages/patients/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<!-- Resumo rápido -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card bg-primary bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-4 fw-bold text-primary"><?= count($records) ?></div>
                <small class="text-muted">Consultas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-info bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-4 fw-bold text-info"><?= count($exams) ?></div>
                <small class="text-muted">Exames</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-success bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-4 fw-bold text-success"><?= count($activeMeds) ?></div>
                <small class="text-muted">Medicamentos Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-warning bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-4 fw-bold text-warning"><?= $totalMeds ?></div>
                <small class="text-muted">Total Medicamentos</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Info Card -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body patient-card">
                <?php if (!empty($patient['photo']) && file_exists(__DIR__ . '/../../' . $patient['photo'])): ?>
                <img src="<?= baseUrl($patient['photo']) ?>" alt="<?= sanitize($patient['name']) ?>" class="patient-avatar">
                <?php else: ?>
                <div class="patient-avatar-placeholder">
                    <i class="bi bi-person"></i>
                </div>
                <?php endif; ?>
                <h5 class="fw-bold"><?= sanitize($patient['name']) ?></h5>
                <?php if (!empty($patient['relationship'])): ?>
                <span class="badge bg-primary mb-2"><?= sanitize($patient['relationship']) ?></span>
                <?php endif; ?>
                <div class="text-muted small">
                    <?php if (!empty($patient['birth_date'])): ?>
                    <div><i class="bi bi-calendar3 me-1"></i><?= formatDate($patient['birth_date']) ?> (<?= calculateAge($patient['birth_date']) ?> anos)</div>
                    <?php endif; ?>
                    <?php if (!empty($patient['gender'])): ?>
                    <div><i class="bi bi-gender-ambiguous me-1"></i><?= $patient['gender'] === 'M' ? 'Masculino' : ($patient['gender'] === 'F' ? 'Feminino' : 'Outro') ?></div>
                    <?php endif; ?>
                    <?php if (!empty($patient['blood_type'])): ?>
                    <div><i class="bi bi-droplet me-1"></i>Tipo <?= sanitize($patient['blood_type']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($patient['phone'])): ?>
                    <div><i class="bi bi-telephone me-1"></i><?= sanitize($patient['phone']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($patient['email'])): ?>
                    <div><i class="bi bi-envelope me-1"></i><?= sanitize($patient['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($patient['allergies']) || !empty($patient['chronic_conditions']) || !empty($patient['medications'])): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-heart-pulse me-2"></i>Info Médica</div>
            <div class="card-body">
                <?php if (!empty($patient['allergies'])): ?>
                <div class="mb-3">
                    <strong class="d-block text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Alergias</strong>
                    <span class="small"><?= nl2br(sanitize($patient['allergies'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($patient['chronic_conditions'])): ?>
                <div class="mb-3">
                    <strong class="d-block text-warning"><i class="bi bi-bandaid me-1"></i>Condições Crônicas</strong>
                    <span class="small"><?= nl2br(sanitize($patient['chronic_conditions'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($patient['medications'])): ?>
                <div>
                    <strong class="d-block text-info"><i class="bi bi-capsule me-1"></i>Medicamentos</strong>
                    <span class="small"><?= nl2br(sanitize($patient['medications'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($patient['health_insurance'])): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-shield-check me-2"></i>Convênio</div>
            <div class="card-body">
                <div><strong><?= sanitize($patient['health_insurance']) ?></strong></div>
                <?php if (!empty($patient['insurance_number'])): ?>
                <small class="text-muted">Carteirinha: <?= sanitize($patient['insurance_number']) ?></small>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Prontuários e Exames -->
    <div class="col-lg-8">
        <!-- Prontuários -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-medical me-2"></i>Prontuários (<?= count($records) ?>)</span>
                <?php if (isAdmin()): ?>
                <a href="<?= baseUrl('pages/records/form.php?patient_id=' . $id) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Novo
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($records)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-file-medical" style="font-size:36px;"></i>
                    <p class="mt-2 mb-0">Nenhum prontuário registrado</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Data</th><th>Título</th><th>Especialidade</th><th>Médico</th><th class="text-end">Ações</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?= formatDate($r['record_date']) ?></td>
                                <td><a href="<?= baseUrl('pages/records/view.php?id=' . $r['id']) ?>"><?= sanitize($r['title']) ?></a></td>
                                <td><?= !empty($r['specialty']) ? sanitize($r['specialty']) : '<span class="text-muted">-</span>' ?></td>
                                <td><?= sanitize($r['doctor_name'] ?? '-') ?></td>
                                <td class="text-end">
                                    <a href="<?= baseUrl('pages/records/view.php?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medicamentos Ativos -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-capsule me-2"></i>Medicamentos Ativos (<?= count($activeMeds) ?>)</span>
                <?php if (isAdmin()): ?>
                <a href="<?= baseUrl('pages/medications/form.php?patient_id=' . $id) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Novo
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($activeMeds)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-capsule" style="font-size:36px;"></i>
                    <p class="mt-2 mb-0">Nenhum medicamento ativo</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Medicamento</th><th>Dosagem</th><th>Frequência</th><th>Tipo</th></tr>
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
                <?php if ($totalMeds > count($activeMeds)): ?>
                <div class="text-center py-2 border-top">
                    <a href="<?= baseUrl('pages/medications/list.php?patient_id=' . $id) ?>" class="btn btn-sm btn-outline-primary">
                        Ver todos os medicamentos (<?= $totalMeds ?>)
                    </a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Exames -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2"></i>Exames (<?= count($exams) ?>)</span>
                <?php if (isAdmin()): ?>
                <a href="<?= baseUrl('pages/exams/form.php?patient_id=' . $id) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Novo
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($exams)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-clipboard2-pulse" style="font-size:36px;"></i>
                    <p class="mt-2 mb-0">Nenhum exame registrado</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Data</th><th>Tipo</th><th>Laboratório</th><th class="text-end">Ações</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($exams as $e): ?>
                            <tr>
                                <td><?= formatDate($e['exam_date']) ?></td>
                                <td><span class="badge bg-info"><?= sanitize($e['exam_type']) ?></span></td>
                                <td><?= sanitize($e['lab_clinic'] ?? '-') ?></td>
                                <td class="text-end">
                                    <a href="<?= baseUrl('pages/exams/view.php?id=' . $e['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Arquivos Genéricos do Paciente -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-folder2-open me-2"></i>Arquivos Extras (<span id="patientFileCount"><?= count($patientFiles) ?></span>)</span>
                <?php if (isAdmin()): ?>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#uploadSection">
                    <i class="bi bi-cloud-upload me-1"></i>Enviar Arquivo
                </button>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
            <!-- Upload form -->
            <div class="collapse" id="uploadSection">
                <div class="card-body border-bottom bg-light">
                    <form id="patientFileUploadForm" enctype="multipart/form-data">
                        <input type="hidden" name="patient_id" value="<?= $id ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Arquivo</label>
                                <input type="file" name="file" class="form-control form-control-sm" required id="pfFileInput">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Comentário <span class="text-muted">(opcional)</span></label>
                                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Descreva o arquivo..." id="pfCommentInput">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100" id="pfUploadBtn">
                                    <i class="bi bi-upload me-1"></i>Enviar
                                </button>
                            </div>
                        </div>
                        <div id="pfUploadProgress" class="mt-2" style="display:none;">
                            <div class="progress" style="height:20px;">
                                <div id="pfProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
                            </div>
                        </div>
                        <div id="pfUploadMsg" class="mt-2"></div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card-body p-0">
                <div id="patientFilesList">
                <?php if (empty($patientFiles)): ?>
                    <div class="empty-state py-4" id="pfEmptyState">
                        <i class="bi bi-folder2-open" style="font-size:36px;"></i>
                        <p class="mt-2 mb-0">Nenhum arquivo extra</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                    <?php foreach ($patientFiles as $pf):
                        $pfIcon = getFileIcon($pf['file_type'], $pf['file_name'] ?? '', $pf['file_path'] ?? '');
                        $pfCat = getFileCategory($pf['file_type'], $pf['file_name'] ?? '', $pf['file_path'] ?? '');
                        $pfSize = $pf['file_size'] < 1048576 
                            ? round($pf['file_size'] / 1024) . ' KB' 
                            : number_format($pf['file_size'] / 1048576, 1) . ' MB';
                    ?>
                        <div class="list-group-item" id="pf-row-<?= $pf['id'] ?>">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Ícone / Thumbnail -->
                                <div class="flex-shrink-0 text-center" style="width:48px;">
                                    <?php if ($pf['is_image'] && file_exists(__DIR__ . '/../../' . $pf['file_path'])): ?>
                                        <img src="<?= baseUrl($pf['file_path']) ?>" class="rounded" style="width:48px;height:48px;object-fit:cover;" alt="">
                                    <?php else: ?>
                                        <i class="bi <?= $pfIcon ?>" style="font-size:2rem;"></i>
                                    <?php endif; ?>
                                </div>
                                <!-- Info -->
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-semibold text-truncate"><?= sanitize($pf['original_name']) ?></div>
                                    <?php if (!empty($pf['comment'])): ?>
                                        <div class="text-muted small"><i class="bi bi-chat-left-text me-1"></i><?= sanitize($pf['comment']) ?></div>
                                    <?php endif; ?>
                                    <div class="text-muted small mt-1">
                                        <span class="badge bg-light text-dark"><?= strtoupper($pfCat) ?></span>
                                        <span class="ms-2"><?= $pfSize ?></span>
                                        <span class="ms-2"><?= date('d/m/Y H:i', strtotime($pf['created_at'])) ?></span>
                                    </div>
                                </div>
                                <!-- Ações -->
                                <div class="flex-shrink-0 d-flex gap-1">
                                    <a href="<?= baseUrl($pf['file_path']) ?>" download="<?= sanitize($pf['original_name']) ?>" class="btn btn-sm btn-outline-primary" title="Baixar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger pf-delete-btn" data-id="<?= $pf['id'] ?>" data-name="<?= sanitize($pf['original_name']) ?>" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Files JS -->
<script>
(function() {
    const uploadUrl = '<?= baseUrl("pages/patients/upload_file.php") ?>';
    const deleteUrl = '<?= baseUrl("pages/patients/delete_file.php") ?>';
    const baseUrlPath = '<?= baseUrl("") ?>';
    const form = document.getElementById('patientFileUploadForm');
    const fileInput = document.getElementById('pfFileInput');
    const commentInput = document.getElementById('pfCommentInput');
    const uploadBtn = document.getElementById('pfUploadBtn');
    const progressDiv = document.getElementById('pfUploadProgress');
    const progressBar = document.getElementById('pfProgressBar');
    const msgDiv = document.getElementById('pfUploadMsg');
    const filesList = document.getElementById('patientFilesList');
    const fileCount = document.getElementById('patientFileCount');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!fileInput || !fileInput.files.length) return;

            const fd = new FormData(form);
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
            progressDiv.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            msgDiv.innerHTML = '';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl, true);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = pct + '%';
                    progressBar.textContent = pct + '%';
                }
            });

            xhr.onload = function() {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Enviar';
                progressDiv.style.display = 'none';

                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success && data.file) {
                        msgDiv.innerHTML = '<div class="alert alert-success alert-sm py-1 px-2 small mb-0"><i class="bi bi-check-circle me-1"></i>Arquivo enviado!</div>';
                        addFileToList(data.file);
                        form.reset();
                        setTimeout(function() { msgDiv.innerHTML = ''; }, 3000);
                    } else {
                        msgDiv.innerHTML = '<div class="alert alert-danger alert-sm py-1 px-2 small mb-0">' + (data.error || 'Erro no upload') + '</div>';
                    }
                } catch(err) {
                    msgDiv.innerHTML = '<div class="alert alert-danger alert-sm py-1 px-2 small mb-0">Erro inesperado no upload.</div>';
                }
            };

            xhr.onerror = function() {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Enviar';
                progressDiv.style.display = 'none';
                msgDiv.innerHTML = '<div class="alert alert-danger alert-sm py-1 px-2 small mb-0">Erro de conexão.</div>';
            };

            xhr.send(fd);
        });
    }

    function addFileToList(f) {
        // Remove empty state if present
        const empty = document.getElementById('pfEmptyState');
        if (empty) empty.remove();

        // Ensure list-group wrapper exists
        let listGroup = filesList.querySelector('.list-group');
        if (!listGroup) {
            listGroup = document.createElement('div');
            listGroup.className = 'list-group list-group-flush';
            filesList.appendChild(listGroup);
        }

        const sizeTxt = f.file_size < 1048576
            ? Math.round(f.file_size / 1024) + ' KB'
            : (f.file_size / 1048576).toFixed(1) + ' MB';

        const isImg = parseInt(f.is_image);
        const iconHtml = isImg
            ? '<img src="' + baseUrlPath + f.file_path + '" class="rounded" style="width:48px;height:48px;object-fit:cover;" alt="">'
            : '<i class="bi bi-file-earmark" style="font-size:2rem;"></i>';

        const commentHtml = f.comment
            ? '<div class="text-muted small"><i class="bi bi-chat-left-text me-1"></i>' + escHtml(f.comment) + '</div>'
            : '';

        const now = new Date();
        const dateStr = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'});

        const html = '<div class="list-group-item" id="pf-row-' + f.id + '">' +
            '<div class="d-flex align-items-start gap-3">' +
                '<div class="flex-shrink-0 text-center" style="width:48px;">' + iconHtml + '</div>' +
                '<div class="flex-grow-1 min-width-0">' +
                    '<div class="fw-semibold text-truncate">' + escHtml(f.original_name) + '</div>' +
                    commentHtml +
                    '<div class="text-muted small mt-1">' +
                        '<span class="badge bg-light text-dark">FILE</span>' +
                        '<span class="ms-2">' + sizeTxt + '</span>' +
                        '<span class="ms-2">' + dateStr + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="flex-shrink-0 d-flex gap-1">' +
                    '<a href="' + baseUrlPath + f.file_path + '" download="' + escHtml(f.original_name) + '" class="btn btn-sm btn-outline-primary" title="Baixar"><i class="bi bi-download"></i></a>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger pf-delete-btn" data-id="' + f.id + '" data-name="' + escHtml(f.original_name) + '" title="Excluir"><i class="bi bi-trash"></i></button>' +
                '</div>' +
            '</div>' +
        '</div>';

        listGroup.insertAdjacentHTML('afterbegin', html);
        updateCount(1);
        bindDeleteBtns();
    }

    function updateCount(delta) {
        if (fileCount) {
            fileCount.textContent = parseInt(fileCount.textContent || 0) + delta;
        }
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function bindDeleteBtns() {
        document.querySelectorAll('.pf-delete-btn').forEach(function(btn) {
            btn.onclick = function() {
                const fId = this.getAttribute('data-id');
                const fName = this.getAttribute('data-name');
                if (!confirm('Excluir o arquivo "' + fName + '"?')) return;

                const fd = new FormData();
                fd.append('file_id', fId);

                fetch(deleteUrl, { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            const row = document.getElementById('pf-row-' + fId);
                            if (row) row.remove();
                            updateCount(-1);
                            // Show empty state if no more files
                            const listGroup = filesList.querySelector('.list-group');
                            if (listGroup && listGroup.children.length === 0) {
                                filesList.innerHTML = '<div class="empty-state py-4" id="pfEmptyState"><i class="bi bi-folder2-open" style="font-size:36px;"></i><p class="mt-2 mb-0">Nenhum arquivo extra</p></div>';
                            }
                        } else {
                            alert(data.error || 'Erro ao excluir arquivo.');
                        }
                    })
                    .catch(function() { alert('Erro de conexão ao excluir.'); });
            };
        });
    }

    bindDeleteBtns();
})();
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
