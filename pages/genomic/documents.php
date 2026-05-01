<?php
$pageTitle = 'Documentos Genéticos';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();

$patientId = intval($_GET['patient_id'] ?? 0);
if (!$patientId || !canAccessPatient($patientId)) {
    redirect(baseUrl('pages/patients/list.php'));
}

$pdo = getConnection();
$p = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$p->execute([$patientId]);
$patient = $p->fetch();

// Buscar arquivos genéticos do paciente
// Tenta com category primeiro, se coluna não existir, busca todos
try {
    $stmt = $pdo->prepare("SELECT * FROM patient_files WHERE patient_id = ? AND category = 'genomic' ORDER BY created_at DESC");
    $stmt->execute([$patientId]);
    $files = $stmt->fetchAll();
} catch (Exception $e) {
    // Se a coluna category não existir ainda, mostra vazio
    $files = [];
}

// Processar upload
$uploadMsg = '';
$uploadType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['file'])) {
    $file = $_FILES['file'];
    $comment = trim($_POST['comment'] ?? '');
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/patients/' . $patientId . '/genomic/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . $safeName;
        $relativePath = 'uploads/patients/' . $patientId . '/genomic/' . $safeName;
        
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $mimeType = mime_content_type($destPath);
            $fileSize = filesize($destPath);
            $isImage = str_starts_with($mimeType, 'image/') ? 1 : 0;
            
            try {
                $stmt = $pdo->prepare("INSERT INTO patient_files (patient_id, file_name, original_name, file_path, file_type, file_size, is_image, comment, category, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'genomic', ?)");
                $stmt->execute([
                    $patientId,
                    $safeName,
                    $file['name'],
                    $relativePath,
                    $mimeType,
                    $fileSize,
                    $isImage,
                    $comment ?: null,
                    getCurrentUserId()
                ]);
                $uploadMsg = 'Documento enviado com sucesso!';
                $uploadType = 'success';
            } catch (Exception $e) {
                // Se category não existir, inserir sem ela
                $stmt = $pdo->prepare("INSERT INTO patient_files (patient_id, file_name, original_name, file_path, file_type, file_size, is_image, comment, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $patientId,
                    $safeName,
                    $file['name'],
                    $relativePath,
                    $mimeType,
                    $fileSize,
                    $isImage,
                    $comment ? '[GENÉTICA] ' . $comment : '[GENÉTICA]',
                    getCurrentUserId()
                ]);
                $uploadMsg = 'Documento enviado com sucesso! (Execute a migração SQL para melhor organização)';
                $uploadType = 'success';
            }
            
            // Recarregar lista
            redirect(baseUrl('pages/genomic/documents.php?patient_id=' . $patientId . '&uploaded=1'));
        } else {
            $uploadMsg = 'Erro ao mover o arquivo. Tente novamente.';
            $uploadType = 'danger';
        }
    } else {
        $uploadMsg = 'Erro no upload do arquivo.';
        $uploadType = 'danger';
    }
}

if (isset($_GET['uploaded'])) {
    $uploadMsg = 'Documento enviado com sucesso!';
    $uploadType = 'success';
}

// Se não conseguiu buscar com category, buscar com prefixo no comment
if (empty($files)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM patient_files WHERE patient_id = ? AND (category = 'genomic' OR comment LIKE '[GENÉTICA]%' OR file_path LIKE '%/genomic/%') ORDER BY created_at DESC");
        $stmt->execute([$patientId]);
        $files = $stmt->fetchAll();
    } catch (Exception $e) {
        $stmt = $pdo->prepare("SELECT * FROM patient_files WHERE patient_id = ? AND (comment LIKE '[GENÉTICA]%' OR file_path LIKE '%/genomic/%') ORDER BY created_at DESC");
        $stmt->execute([$patientId]);
        $files = $stmt->fetchAll();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-file-earmark-medical me-2"></i>Documentos Genéticos — <?= sanitize($patient['name']) ?></h1>
    <div>
        <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao Dashboard
        </a>
    </div>
</div>

<?php if ($uploadMsg): ?>
<div class="alert alert-<?= $uploadType ?> alert-dismissible fade show">
    <i class="bi bi-<?= $uploadType === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
    <?= sanitize($uploadMsg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Upload de novo documento -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Enviar Documento</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">Envie laudos genéticos, relatórios da Genera, resultados de painéis farmacogenéticos, cartão de emergência ou qualquer documento relacionado à genética do paciente.</p>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-paperclip me-1"></i>Arquivo</label>
                    <input type="file" name="file" class="form-control" required 
                           accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.txt,.csv,.xlsx,.xls">
                    <small class="text-muted">Formatos aceitos: PDF, imagens, Word, texto, planilhas</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-chat-left-text me-1"></i>Descrição (opcional)</label>
                    <input type="text" name="comment" class="form-control" 
                           placeholder="Ex: Laudo Genera abril/2026, Cartão farmacogenético...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i>Enviar Documento
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de documentos -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Documentos Salvos (<?= count($files) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($files)): ?>
        <div class="text-center py-4">
            <i class="bi bi-folder" style="font-size: 48px; color: #ccc;"></i>
            <p class="text-muted mt-2">Nenhum documento genético salvo ainda.</p>
            <p class="text-muted small">Use o formulário acima para enviar laudos, relatórios ou cartões.</p>
        </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($files as $f): ?>
            <?php
            $ext = strtolower(pathinfo($f['original_name'], PATHINFO_EXTENSION));
            $icon = 'bi-file-earmark';
            $iconColor = 'text-secondary';
            if (in_array($ext, ['pdf'])) { $icon = 'bi-file-earmark-pdf'; $iconColor = 'text-danger'; }
            elseif (in_array($ext, ['jpg','jpeg','png','gif'])) { $icon = 'bi-file-earmark-image'; $iconColor = 'text-success'; }
            elseif (in_array($ext, ['doc','docx'])) { $icon = 'bi-file-earmark-word'; $iconColor = 'text-primary'; }
            elseif (in_array($ext, ['xls','xlsx','csv'])) { $icon = 'bi-file-earmark-spreadsheet'; $iconColor = 'text-success'; }
            elseif (in_array($ext, ['txt'])) { $icon = 'bi-file-earmark-text'; $iconColor = 'text-secondary'; }
            $isViewable = in_array($ext, ['pdf','jpg','jpeg','png','gif','txt']);
            $comment = $f['comment'] ?? '';
            $comment = str_replace('[GENÉTICA] ', '', $comment);
            $comment = str_replace('[GENÉTICA]', '', $comment);
            $size = $f['file_size'] ?? 0;
            if ($size > 1048576) $sizeStr = round($size / 1048576, 1) . ' MB';
            elseif ($size > 1024) $sizeStr = round($size / 1024, 0) . ' KB';
            else $sizeStr = $size . ' B';
            ?>
            <div class="col-md-6 col-lg-4" id="file-card-<?= $f['id'] ?>">
                <div class="card h-100">
                    <?php if ($f['is_image'] ?? false): ?>
                    <!-- Prévia da imagem -->
                    <a href="<?= baseUrl($f['file_path']) ?>" target="_blank">
                        <img src="<?= baseUrl($f['file_path']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;" alt="<?= sanitize($f['original_name']) ?>">
                    </a>
                    <?php else: ?>
                    <!-- Ícone grande do tipo de arquivo -->
                    <div class="card-img-top text-center py-4 bg-light">
                        <i class="bi <?= $icon ?> <?= $iconColor ?>" style="font-size: 64px;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h6 class="card-title mb-1" style="word-break: break-all;"><?= sanitize($f['original_name']) ?></h6>
                        <?php if ($comment): ?>
                        <p class="card-text text-muted small mb-1"><?= sanitize($comment) ?></p>
                        <?php endif; ?>
                        <p class="card-text">
                            <small class="text-muted">
                                <?= $sizeStr ?> · <?= isset($f['created_at']) ? date('d/m/Y', strtotime($f['created_at'])) : '' ?>
                            </small>
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <div>
                                <?php if ($isViewable): ?>
                                <a href="<?= baseUrl($f['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                    <i class="bi bi-eye me-1"></i>Ver
                                </a>
                                <?php endif; ?>
                                <a href="<?= baseUrl($f['file_path']) ?>" download="<?= sanitize($f['original_name']) ?>" class="btn btn-sm btn-outline-success" title="Baixar">
                                    <i class="bi bi-download me-1"></i>Baixar
                                </a>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFile(<?= $f['id'] ?>, '<?= sanitize(addslashes($f['original_name'])) ?>')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Excluir Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o documento:</p>
                <p class="fw-bold" id="deleteFileName"></p>
                <p class="text-danger small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFileId = null;

function deleteFile(fileId, fileName) {
    deleteFileId = fileId;
    document.getElementById('deleteFileName').textContent = fileName;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteFileId) return;
    
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Excluindo...';
    
    fetch('<?= baseUrl("pages/patients/delete_file.php") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'file_id=' + deleteFileId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Remover o card do documento
            const card = document.getElementById('file-card-' + deleteFileId);
            if (card) card.remove();
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            // Mostrar mensagem de sucesso
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = '<i class="bi bi-check-circle me-1"></i>Documento excluído com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            document.querySelector('.page-header').after(alert);
        } else {
            alert('Erro ao excluir: ' + (data.error || 'Tente novamente'));
        }
    })
    .catch(() => alert('Erro de conexão. Tente novamente.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-trash me-1"></i>Excluir';
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
