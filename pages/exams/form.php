<?php
$pageTitle = 'Exame';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$patientIdDefault = intval($_GET['patient_id'] ?? 0);
$exam = null;
$existingFiles = [];

$examSpecialties = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$id]);
    $exam = $stmt->fetch();
    if (!$exam) {
        setFlash('danger', 'Exame não encontrado.');
        redirect(baseUrl('pages/exams/list.php'));
    }
    $pageTitle = 'Editar Exame';
    $patientIdDefault = $exam['patient_id'];
    
    $filesStmt = $pdo->prepare("SELECT * FROM exam_files WHERE exam_id = ?");
    $filesStmt->execute([$id]);
    $existingFiles = $filesStmt->fetchAll();
    
    // Carregar especialidades do exame
    $specStmt = $pdo->prepare("SELECT specialty_name FROM exam_specialties WHERE exam_id = ? ORDER BY specialty_name");
    $specStmt->execute([$id]);
    $examSpecialties = $specStmt->fetchAll(PDO::FETCH_COLUMN);
    // Fallback: se não tem na tabela junction mas tem no campo legado
    if (empty($examSpecialties) && !empty($exam['specialty'])) {
        $examSpecialties = [$exam['specialty']];
    }
} else {
    $pageTitle = 'Novo Exame';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'patient_id' => intval($_POST['patient_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'exam_type' => trim($_POST['exam_type'] ?? ''),
        'exam_date' => dateToDb($_POST['exam_date'] ?? date('d/m/Y')),
        'lab_clinic' => trim($_POST['lab_clinic'] ?? ''),
        'doctor_name' => trim($_POST['doctor_name'] ?? ''),
        'status' => $_POST['status'] ?? 'Indefinido',
        'results' => trim($_POST['results'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'specialty' => trim($_POST['specialty'] ?? ''),
    ];
    
    $postedSpecialties = $_POST['specialties'] ?? [];
    if (!is_array($postedSpecialties)) $postedSpecialties = [$postedSpecialties];
    $postedSpecialties = array_filter(array_map('trim', $postedSpecialties));
    // Usar primeira especialidade como campo legado
    if (!empty($postedSpecialties) && empty($data['specialty'])) {
        $data['specialty'] = $postedSpecialties[0];
    }

    if (empty($data['patient_id']) || empty($data['title']) || empty($data['exam_type'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Paciente, título e tipo de exame são obrigatórios.']);
            exit;
        }
        setFlash('danger', 'Paciente, título e tipo de exame são obrigatórios.');
    } else {
        if ($id) {
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
            $values[] = $id;
            $pdo->prepare("UPDATE exams SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        } else {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $fields[] = 'created_by';
            $placeholders[] = '?';
            $values = array_values($data);
            $values[] = getCurrentUserId();
            $pdo->prepare("INSERT INTO exams (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")")->execute($values);
            $id = $pdo->lastInsertId();
        }

        // Salvar especialidades na tabela junction
        $pdo->prepare("DELETE FROM exam_specialties WHERE exam_id = ?")->execute([$id]);
        if (!empty($postedSpecialties)) {
            $specInsert = $pdo->prepare("INSERT IGNORE INTO exam_specialties (exam_id, specialty_name) VALUES (?, ?)");
            foreach ($postedSpecialties as $specName) {
                $specInsert->execute([$id, $specName]);
            }
        }

        // Upload de arquivos
        if (!empty($_FILES['files']['name'][0])) {
            $uploadDir = __DIR__ . '/../../uploads/exams/' . $id . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $safeName = time() . '_' . $i . '.' . $ext;
                    $destPath = $uploadDir . $safeName;
                    $relativePath = 'uploads/exams/' . $id . '/' . $safeName;
                    
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destPath)) {
                        $mimeType = mime_content_type($destPath);
                        $fileSize = filesize($destPath);
                        $isImage = str_starts_with($mimeType, 'image/') ? 1 : 0;
                        $pdo->prepare("INSERT INTO exam_files (exam_id, file_name, original_name, file_path, file_type, file_size, is_image) VALUES (?, ?, ?, ?, ?, ?, ?)")
                            ->execute([$id, $safeName, $name, $relativePath, $mimeType, $fileSize, $isImage]);
                    }
                }
            }
        }

        // Remover arquivos marcados
        if (!empty($_POST['remove_files'])) {
            foreach ($_POST['remove_files'] as $fileId) {
                $fStmt = $pdo->prepare("SELECT * FROM exam_files WHERE id = ? AND exam_id = ?");
                $fStmt->execute([$fileId, $id]);
                $file = $fStmt->fetch();
                if ($file) {
                    $filePath = __DIR__ . '/../../' . $file['file_path'];
                    if (file_exists($filePath)) unlink($filePath);
                    $pdo->prepare("DELETE FROM exam_files WHERE id = ?")->execute([$fileId]);
                }
            }
        }

        // Se é requisição AJAX (batch upload), retorna JSON com o ID
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'exam_id' => $id]);
            exit;
        }

        setFlash('success', $exam ? 'Exame atualizado!' : 'Exame cadastrado!');
        redirect(baseUrl('pages/exams/view.php?id=' . $id));
    }
}

$d = $exam ?? $_POST ?? [];
$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-clipboard2-pulse me-2"></i><?= $id ? 'Editar' : 'Novo' ?> Exame</h1>
    <a href="<?= baseUrl('pages/exams/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
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
                    <input type="text" name="exam_date" class="form-control date-br" value="<?= dateToForm($d['exam_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['Indefinido','Normal','Alterado','Aguardando'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($d['status'] ?? 'Indefinido') === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" class="form-control" value="<?= sanitize($d['title'] ?? '') ?>" placeholder="Ex: Hemograma completo" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de exame *</label>
                    <input type="text" name="exam_type" class="form-control" value="<?= sanitize($d['exam_type'] ?? '') ?>" placeholder="Hemograma, Raio-X, Ultrassom..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Laboratório / Clínica</label>
                    <input type="text" name="lab_clinic" class="form-control" value="<?= sanitize($d['lab_clinic'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Médico solicitante</label>
                    <input type="text" name="doctor_name" class="form-control" value="<?= sanitize($d['doctor_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Especialidades</label>
                    <div id="specialtyMultiWidgetExam" class="position-relative">
                        <div class="specialty-tags mb-1"></div>
                        <input type="text" class="form-control specialty-search" placeholder="Digite para buscar e adicionar especialidades..." autocomplete="off">
                        <div class="specialty-hidden-inputs"></div>
                        <div class="specialty-dropdown"></div>
                    </div>
                    <small class="text-muted">Selecione uma ou mais especialidades. Pressione Enter para adicionar.</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Resultados</label>
                    <textarea name="results" class="form-control" rows="5" placeholder="Resultados do exame..."><?= sanitize($d['results'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" class="form-control" rows="5"><?= sanitize($d['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Arquivos existentes -->
            <?php if (!empty($existingFiles)): ?>
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-images me-2"></i>Arquivos do Exame</h5>
            <div class="row g-2 mb-3">
                <?php foreach ($existingFiles as $f): 
                    $fCat = getFileCategory($f['file_type'], $f['file_name'] ?? '', $f['file_path'] ?? '');
                    $fIcon = getFileIcon($f['file_type'], $f['file_name'] ?? '', $f['file_path'] ?? '');
                ?>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center">
                        <?php if ($fCat === 'image'): ?>
                        <img src="<?= baseUrl($f['file_path']) ?>" class="img-fluid rounded mb-2" style="max-height:100px;object-fit:cover;">
                        <?php else: ?>
                        <i class="bi <?= $fIcon ?> d-block" style="font-size:40px;"></i>
                        <?php endif; ?>
                        <small class="d-block text-truncate"><?= sanitize($f['original_name'] ?? $f['file_name']) ?></small>
                        <div class="form-check mt-1 d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="remove_files[]" value="<?= $f['id'] ?>" id="ef_<?= $f['id'] ?>">
                            <label class="form-check-label small text-danger ms-1" for="ef_<?= $f['id'] ?>">Remover</label>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Upload -->
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-cloud-upload me-2"></i>Anexar Arquivos</h5>
            <div class="upload-wrapper mb-4">
                <div class="batch-upload-area" id="batchUploadArea" style="border:2px dashed #ccc;border-radius:8px;padding:2rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;" onmouseover="this.style.borderColor='#0d6efd'" onmouseout="this.style.borderColor='#ccc'">
                    <input type="file" id="batchFileInput" multiple accept="image/*,.pdf,.doc,.docx" style="display:none">
                    <i class="bi bi-cloud-arrow-up d-block" style="font-size:2rem;color:#6c757d;"></i>
                    <p class="mb-0">Clique ou arraste imagens e documentos<br><small class="text-muted">JPG, PNG, PDF, DOC — sem limite de quantidade</small></p>
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
                <button type="submit" class="btn btn-primary" id="examSubmitBtn"><i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Cadastrar' ?></button>
                <a href="<?= baseUrl('pages/exams/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const BATCH_SIZE = 20; // arquivos por lote
    const uploadUrl = '<?= baseUrl("pages/exams/upload_files.php") ?>';
    const fileInput = document.getElementById('batchFileInput');
    const uploadArea = document.getElementById('batchUploadArea');
    const fileListEl = document.getElementById('batchFileList');
    const progressDiv = document.getElementById('batchProgress');
    const progressBar = document.getElementById('batchProgressBar');
    const progressText = document.getElementById('batchProgressText');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('examSubmitBtn');
    let pendingFiles = [];

    // Click to select files
    uploadArea.addEventListener('click', function(e) {
        if (e.target === fileInput) return;
        fileInput.click();
    });

    // Drag & drop
    uploadArea.addEventListener('dragover', function(e) { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', function() { uploadArea.classList.remove('drag-over'); });
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        addFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', function() {
        addFiles(fileInput.files);
        fileInput.value = '';
    });

    function addFiles(fileList) {
        for (let i = 0; i < fileList.length; i++) {
            pendingFiles.push(fileList[i]);
        }
        updateFileList();
    }

    function updateFileList() {
        if (pendingFiles.length === 0) {
            fileListEl.innerHTML = '';
            return;
        }
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

    // Intercept form submission
    form.addEventListener('submit', function(e) {
        if (pendingFiles.length === 0) return; // normal submit, no files to batch upload

        e.preventDefault();

        // First submit the form data (without files) via fetch
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';

        const formData = new FormData(form);
        // Remove any file inputs from formData
        formData.delete('files[]');

        fetch(form.action || window.location.href, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(response) {
            return response.json();
        }).then(function(result) {
            if (result.success && result.exam_id) {
                uploadFilesBatch(result.exam_id, 0);
            } else {
                alert('Erro ao salvar exame. Verifique os campos obrigatórios.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Salvar';
            }
        }).catch(function(err) {
            alert('Erro ao salvar exame: ' + err.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Salvar';
        });
    });

    function uploadFilesBatch(examId, startIndex) {
        progressDiv.style.display = 'block';
        const totalFiles = pendingFiles.length;
        const endIndex = Math.min(startIndex + BATCH_SIZE, totalFiles);
        const batch = pendingFiles.slice(startIndex, endIndex);

        const pct = Math.round((startIndex / totalFiles) * 100);
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        progressText.textContent = 'Enviando arquivos ' + (startIndex + 1) + ' a ' + endIndex + ' de ' + totalFiles + '...';

        const fd = new FormData();
        fd.append('exam_id', examId);
        batch.forEach(function(file) {
            fd.append('files[]', file);
        });

        fetch(uploadUrl, {
            method: 'POST',
            body: fd
        }).then(function(r) { return r.json(); })
        .then(function(data) {
            if (endIndex < totalFiles) {
                uploadFilesBatch(examId, endIndex);
            } else {
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                progressText.textContent = totalFiles + ' arquivo(s) enviado(s) com sucesso!';
                
                setTimeout(function() {
                    window.location.href = '<?= baseUrl("pages/exams/view.php") ?>?id=' + examId;
                }, 1000);
            }
        }).catch(function(err) {
            progressBar.classList.add('bg-danger');
            progressText.textContent = 'Erro no lote ' + (startIndex + 1) + '-' + endIndex + ': ' + err.message + '. Tentando novamente...';
            // Retry after 2 seconds
            setTimeout(function() { uploadFilesBatch(examId, startIndex); }, 2000);
        });
    }
})();
</script>

<script src="<?= baseUrl('assets/js/specialty-multi-widget.js') ?>"></script>
<script>
initSpecialtyMultiWidget(
    'specialtyMultiWidgetExam', 
    '<?= baseUrl("pages/specialties/ajax.php") ?>', 
    <?= json_encode(!empty($examSpecialties) ? $examSpecialties : (isset($d['specialty']) && $d['specialty'] ? [$d['specialty']] : [])) ?>
);
</script>
<style>
.specialty-dropdown{position:absolute;top:100%;left:0;right:0;z-index:1050;background:#fff;border:1px solid #dee2e6;border-top:0;border-radius:0 0 .375rem .375rem;max-height:220px;overflow-y:auto;display:none;box-shadow:0 4px 12px rgba(0,0,0,.1)}
.specialty-option{padding:8px 12px;cursor:pointer;font-size:.9rem;border-bottom:1px solid #f0f0f0}
.specialty-option:hover{background:#e8f0fe}
.specialty-create{color:#198754;font-style:italic}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
