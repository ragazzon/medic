<?php
$pageTitle = 'Prontuário';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$patientIdDefault = intval($_GET['patient_id'] ?? 0);
$record = null;
$existingFiles = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) {
        setFlash('danger', 'Prontuário não encontrado.');
        redirect(baseUrl('pages/records/list.php'));
    }
    $pageTitle = 'Editar Prontuário';
    $patientIdDefault = $record['patient_id'];
    
    $filesStmt = $pdo->prepare("SELECT * FROM record_files WHERE record_id = ?");
    $filesStmt->execute([$id]);
    $existingFiles = $filesStmt->fetchAll();
} else {
    $pageTitle = 'Novo Prontuário';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'patient_id' => intval($_POST['patient_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'visit_reason' => trim($_POST['visit_reason'] ?? ''),
        'record_date' => dateToDb($_POST['record_date'] ?? date('d/m/Y')),
        'doctor_name' => trim($_POST['doctor_name'] ?? ''),
        'specialty' => trim($_POST['specialty'] ?? ''),
        'diagnosis' => trim($_POST['diagnosis'] ?? ''),
        'symptoms' => trim($_POST['symptoms'] ?? ''),
        'prescription' => trim($_POST['prescription'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if (empty($data['patient_id']) || empty($data['title'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Paciente e título são obrigatórios.']);
            exit;
        }
        setFlash('danger', 'Paciente e título são obrigatórios.');
    } else {
        if ($id) {
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
            $values[] = $id;
            $pdo->prepare("UPDATE medical_records SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        } else {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $fields[] = 'created_by';
            $placeholders[] = '?';
            $values = array_values($data);
            $values[] = getCurrentUserId();
            $pdo->prepare("INSERT INTO medical_records (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")")->execute($values);
            $id = $pdo->lastInsertId();
        }

        // Upload de arquivos
        if (!empty($_FILES['files']['name'][0])) {
            $uploadDir = __DIR__ . '/../../uploads/records/' . $id . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $safeName = time() . '_' . $i . '.' . $ext;
                    $destPath = $uploadDir . $safeName;
                    $relativePath = 'uploads/records/' . $id . '/' . $safeName;
                    
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destPath)) {
                        $mimeType = mime_content_type($destPath);
                        $fileSize = filesize($destPath);
                        $pdo->prepare("INSERT INTO record_files (record_id, file_name, original_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)")
                            ->execute([$id, $safeName, $name, $relativePath, $mimeType, $fileSize]);
                    }
                }
            }
        }

        // Remover arquivos marcados
        if (!empty($_POST['remove_files'])) {
            foreach ($_POST['remove_files'] as $fileId) {
                $fStmt = $pdo->prepare("SELECT * FROM record_files WHERE id = ? AND record_id = ?");
                $fStmt->execute([$fileId, $id]);
                $file = $fStmt->fetch();
                if ($file) {
                    $filePath = __DIR__ . '/../../' . $file['file_path'];
                    if (file_exists($filePath)) unlink($filePath);
                    $pdo->prepare("DELETE FROM record_files WHERE id = ?")->execute([$fileId]);
                }
            }
        }

        // Se é requisição AJAX (batch upload), retorna JSON com o ID
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'record_id' => $id]);
            exit;
        }

        setFlash('success', $record ? 'Prontuário atualizado!' : 'Prontuário cadastrado!');
        redirect(baseUrl('pages/records/view.php?id=' . $id));
    }
}

$d = $record ?? $_POST ?? [];
$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
// Admin vê todos os pacientes - já coberto pelo requireAdmin acima

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-file-medical me-2"></i><?= $id ? 'Editar' : 'Novo' ?> Prontuário</h1>
    <a href="<?= baseUrl('pages/records/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($d['patient_id'] ?? $patientIdDefault) == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data *</label>
                    <input type="text" name="record_date" class="form-control date-br" value="<?= dateToForm($d['record_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Especialidade</label>
                    <div id="specialtyWidgetRecord" class="position-relative">
                        <input type="text" class="form-control specialty-search" placeholder="Buscar ou criar..." value="<?= sanitize($d['specialty'] ?? '') ?>" autocomplete="off">
                        <input type="hidden" name="specialty" value="<?= sanitize($d['specialty'] ?? '') ?>">
                        <div class="specialty-dropdown"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" class="form-control" value="<?= sanitize($d['title'] ?? '') ?>" placeholder="Ex: Consulta de rotina" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Médico</label>
                    <input type="text" name="doctor_name" class="form-control" value="<?= sanitize($d['doctor_name'] ?? '') ?>" placeholder="Nome do médico">
                </div>
                <div class="col-12">
                    <label class="form-label">Motivo da Consulta</label>
                    <textarea name="visit_reason" class="form-control" rows="3" placeholder="Descreva o motivo da consulta..."><?= sanitize($d['visit_reason'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sintomas</label>
                    <textarea name="symptoms" class="form-control" rows="4" placeholder="Descreva os sintomas..."><?= sanitize($d['symptoms'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Diagnóstico</label>
                    <textarea name="diagnosis" class="form-control" rows="4" placeholder="Diagnóstico médico..."><?= sanitize($d['diagnosis'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prescrição</label>
                    <textarea name="prescription" class="form-control" rows="4" placeholder="Medicamentos prescritos..."><?= sanitize($d['prescription'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Notas adicionais..."><?= sanitize($d['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Arquivos existentes -->
            <?php if (!empty($existingFiles)): ?>
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-paperclip me-2"></i>Arquivos Anexados</h5>
            <div class="row g-2 mb-3">
                <?php foreach ($existingFiles as $f): ?>
                <div class="col-md-4">
                    <div class="border rounded p-2 d-flex align-items-center justify-content-between">
                        <div class="text-truncate me-2">
                            <i class="bi bi-file-earmark me-1"></i>
                            <small><?= sanitize($f['file_name']) ?></small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remove_files[]" value="<?= $f['id'] ?>" id="rf_<?= $f['id'] ?>">
                            <label class="form-check-label small text-danger" for="rf_<?= $f['id'] ?>">Remover</label>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Upload novos arquivos -->
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-cloud-upload me-2"></i>Anexar Arquivos</h5>
            <div class="upload-wrapper mb-4">
                <div class="batch-upload-area" id="batchUploadArea" style="border:2px dashed #ccc;border-radius:8px;padding:2rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;" onmouseover="this.style.borderColor='#0d6efd'" onmouseout="this.style.borderColor='#ccc'">
                    <input type="file" id="batchFileInput" multiple accept="image/*,.pdf,.doc,.docx" style="display:none">
                    <i class="bi bi-cloud-arrow-up d-block" style="font-size:2rem;color:#6c757d;"></i>
                    <p class="mb-0">Clique ou arraste arquivos aqui<br><small class="text-muted">Imagens, PDFs, documentos — sem limite de quantidade</small></p>
                </div>
                <div id="batchFileList"></div>
                <div id="batchProgress" style="display:none;" class="mt-2">
                    <div class="progress" style="height:24px;">
                        <div id="batchProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:0%">0%</div>
                    </div>
                    <small id="batchProgressText" class="text-muted mt-1 d-block"></small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="recordSubmitBtn"><i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Cadastrar' ?></button>
                <a href="<?= baseUrl('pages/records/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const BATCH_SIZE = 20;
    const uploadUrl = '<?= baseUrl("pages/records/upload_files.php") ?>';
    const fileInput = document.getElementById('batchFileInput');
    const uploadArea = document.getElementById('batchUploadArea');
    const fileListEl = document.getElementById('batchFileList');
    const progressDiv = document.getElementById('batchProgress');
    const progressBar = document.getElementById('batchProgressBar');
    const progressText = document.getElementById('batchProgressText');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('recordSubmitBtn');
    let pendingFiles = [];

    uploadArea.addEventListener('click', function(e) { if (e.target !== fileInput) fileInput.click(); });
    uploadArea.addEventListener('dragover', function(e) { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', function() { uploadArea.classList.remove('drag-over'); });
    uploadArea.addEventListener('drop', function(e) { e.preventDefault(); uploadArea.classList.remove('drag-over'); addFiles(e.dataTransfer.files); });
    fileInput.addEventListener('change', function() { addFiles(fileInput.files); fileInput.value = ''; });

    function addFiles(fileList) { for (let i = 0; i < fileList.length; i++) pendingFiles.push(fileList[i]); updateFileList(); }

    function updateFileList() {
        if (!pendingFiles.length) { fileListEl.innerHTML = ''; return; }
        let totalSize = 0;
        pendingFiles.forEach(f => totalSize += f.size);
        const sizeMB = (totalSize / 1048576).toFixed(1);

        let html = '<div class="mt-2 border rounded p-2" style="max-height:300px;overflow-y:auto;">';
        html += '<div class="d-flex justify-content-between align-items-center mb-2 px-1">';
        html += '<span class="fw-semibold text-primary"><i class="bi bi-files me-1"></i>' + pendingFiles.length + ' arquivo(s) — ' + sizeMB + ' MB</span>';
        html += '<button type="button" class="btn btn-sm btn-outline-danger" id="clearPendingFiles"><i class="bi bi-x-lg me-1"></i>Limpar tudo</button>';
        html += '</div>';
        html += '<div class="list-group list-group-flush">';
        pendingFiles.forEach(function(f, idx) {
            const fSize = f.size < 1048576 ? (f.size / 1024).toFixed(0) + ' KB' : (f.size / 1048576).toFixed(1) + ' MB';
            const isImg = f.type && f.type.startsWith('image/');
            const icon = isImg ? 'bi-file-image text-success' : 'bi-file-earmark text-secondary';
            html += '<div class="list-group-item list-group-item-action d-flex align-items-center py-1 px-2" style="font-size:.85rem;">';
            html += '<i class="bi ' + icon + ' me-2"></i>';
            html += '<span class="text-truncate flex-grow-1" title="' + f.name.replace(/"/g, '&quot;') + '">' + f.name + '</span>';
            html += '<span class="text-muted ms-2 me-2" style="white-space:nowrap;">' + fSize + '</span>';
            html += '<button type="button" class="btn btn-sm btn-link text-danger p-0 remove-pending-file" data-idx="' + idx + '" title="Remover"><i class="bi bi-x-circle"></i></button>';
            html += '</div>';
        });
        html += '</div></div>';
        fileListEl.innerHTML = html;

        document.getElementById('clearPendingFiles').addEventListener('click', function(e) {
            e.preventDefault();
            pendingFiles = [];
            updateFileList();
        });
        fileListEl.querySelectorAll('.remove-pending-file').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const idx = parseInt(this.getAttribute('data-idx'));
                pendingFiles.splice(idx, 1);
                updateFileList();
            });
        });
    }

    form.addEventListener('submit', function(e) {
        if (!pendingFiles.length) return;
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';
        const formData = new FormData(form);
        formData.delete('files[]');
        fetch(form.action || window.location.href, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(result => {
            if (result.success && result.record_id) { uploadBatch(result.record_id, 0); }
            else { alert(result.error || 'Erro ao salvar. Verifique os campos.'); submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Salvar'; }
        }).catch(err => { alert('Erro: ' + err.message); submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Salvar'; });
    });

    function uploadBatch(recordId, start) {
        progressDiv.style.display = 'block';
        const total = pendingFiles.length, end = Math.min(start + BATCH_SIZE, total);
        progressBar.style.width = Math.round(start/total*100) + '%';
        progressBar.textContent = Math.round(start/total*100) + '%';
        progressText.textContent = 'Enviando ' + (start+1) + ' a ' + end + ' de ' + total + '...';
        const fd = new FormData();
        fd.append('record_id', recordId);
        pendingFiles.slice(start, end).forEach(f => fd.append('files[]', f));
        fetch(uploadUrl, { method: 'POST', body: fd }).then(r => r.json()).then(data => {
            if (end < total) { uploadBatch(recordId, end); }
            else { progressBar.style.width = '100%'; progressBar.textContent = '100%'; progressBar.classList.remove('progress-bar-animated'); progressBar.classList.add('bg-success'); progressText.textContent = total + ' arquivo(s) enviado(s)!'; setTimeout(() => { window.location.href = '<?= baseUrl("pages/records/view.php") ?>?id=' + recordId; }, 1000); }
        }).catch(err => { progressBar.classList.add('bg-danger'); progressText.textContent = 'Erro no lote. Tentando novamente...'; setTimeout(() => uploadBatch(recordId, start), 2000); });
    }
})();
</script>

<script src="<?= baseUrl('assets/js/specialty-widget.js') ?>"></script>
<script>initSpecialtyWidget('specialtyWidgetRecord', '<?= baseUrl("pages/specialties/ajax.php") ?>');</script>
<style>
.specialty-dropdown{position:absolute;top:100%;left:0;right:0;z-index:1050;background:#fff;border:1px solid #dee2e6;border-top:0;border-radius:0 0 .375rem .375rem;max-height:220px;overflow-y:auto;display:none;box-shadow:0 4px 12px rgba(0,0,0,.1)}
.specialty-option{padding:8px 12px;cursor:pointer;font-size:.9rem;border-bottom:1px solid #f0f0f0}
.specialty-option:hover{background:#e8f0fe}
.specialty-create{color:#198754;font-style:italic}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
