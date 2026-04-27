<?php
$pageTitle = 'Comparar Exames';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$compareId = intval($_GET['compare_id'] ?? 0);

// Exame principal
$stmt = $pdo->prepare("SELECT e.*, p.name as patient_name FROM exams e JOIN patients p ON e.patient_id = p.id WHERE e.id = ?");
$stmt->execute([$id]);
$exam = $stmt->fetch();

if (!$exam) {
    setFlash('danger', 'Exame não encontrado.');
    redirect(baseUrl('pages/exams/list.php'));
}

// Controle de acesso
if (!canAccessPatient($exam['patient_id'])) {
    setFlash('danger', 'Você não tem permissão para acessar este exame.');
    redirect(baseUrl('pages/exams/list.php'));
}

// Listar exames do mesmo tipo para comparação
$similar = $pdo->prepare("
    SELECT e.id, e.exam_date, e.lab_clinic 
    FROM exams e WHERE e.patient_id = ? AND e.exam_type = ? AND e.id != ?
    ORDER BY e.exam_date DESC
");
$similar->execute([$exam['patient_id'], $exam['exam_type'], $id]);
$similarExams = $similar->fetchAll();

$examB = null;
$filesA = [];
$filesB = [];
$imagesA = [];
$imagesB = [];

if ($compareId) {
    $stmtB = $pdo->prepare("SELECT e.*, p.name as patient_name FROM exams e JOIN patients p ON e.patient_id = p.id WHERE e.id = ?");
    $stmtB->execute([$compareId]);
    $examB = $stmtB->fetch();
}

// Arquivos do exame A
$fA = $pdo->prepare("SELECT * FROM exam_files WHERE exam_id = ?");
$fA->execute([$id]);
$filesA = $fA->fetchAll();
$imagesA = array_filter($filesA, fn($f) => str_starts_with($f['file_type'], 'image/'));

if ($examB) {
    $fB = $pdo->prepare("SELECT * FROM exam_files WHERE exam_id = ?");
    $fB->execute([$compareId]);
    $filesB = $fB->fetchAll();
    $imagesB = array_filter($filesB, fn($f) => str_starts_with($f['file_type'], 'image/'));
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-arrow-left-right me-2"></i>Comparar Exames</h1>
    <a href="<?= baseUrl('pages/exams/view.php?id=' . $id) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<!-- Seletor de comparação -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="col-md-8">
                <label class="form-label">Selecione o exame para comparar com <strong><?= sanitize($exam['exam_type']) ?> - <?= formatDate($exam['exam_date']) ?></strong></label>
                <select name="compare_id" class="form-select" required>
                    <option value="">Selecione um exame...</option>
                    <?php foreach ($similarExams as $se): ?>
                    <option value="<?= $se['id'] ?>" <?= $compareId == $se['id'] ? 'selected' : '' ?>>
                        <?= formatDate($se['exam_date']) ?> <?= !empty($se['lab_clinic']) ? '- ' . sanitize($se['lab_clinic']) : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-info"><i class="bi bi-arrow-left-right me-1"></i>Comparar</button>
            </div>
        </form>
    </div>
</div>

<?php if ($examB): ?>
<!-- Comparação lado a lado -->
<div class="row g-3">
    <!-- Exame A -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-clipboard2-pulse me-2"></i><?= formatDate($exam['exam_date']) ?>
                <?php if (!empty($exam['lab_clinic'])): ?> - <?= sanitize($exam['lab_clinic']) ?><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($exam['results']): ?>
                <h6 class="fw-semibold">Resultados</h6>
                <pre style="white-space:pre-wrap;font-family:inherit;background:#f8f9fa;padding:12px;border-radius:8px;"><?= sanitize($exam['results']) ?></pre>
                <?php endif; ?>
                <?php if ($exam['notes']): ?>
                <h6 class="fw-semibold">Observações</h6>
                <p><?= nl2br(sanitize($exam['notes'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($imagesA)): ?>
                <h6 class="fw-semibold mt-3">Imagens</h6>
                <div class="row g-2">
                    <?php foreach ($imagesA as $img): ?>
                    <div class="col-6">
                        <a href="<?= baseUrl($img['file_path']) ?>" target="_blank">
                            <img src="<?= baseUrl($img['file_path']) ?>" class="img-fluid rounded" style="width:100%;height:140px;object-fit:cover;">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Exame B -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <i class="bi bi-clipboard2-pulse me-2"></i><?= formatDate($examB['exam_date']) ?>
                <?php if (!empty($examB['lab_clinic'])): ?> - <?= sanitize($examB['lab_clinic']) ?><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($examB['results']): ?>
                <h6 class="fw-semibold">Resultados</h6>
                <pre style="white-space:pre-wrap;font-family:inherit;background:#f8f9fa;padding:12px;border-radius:8px;"><?= sanitize($examB['results']) ?></pre>
                <?php endif; ?>
                <?php if ($examB['notes']): ?>
                <h6 class="fw-semibold">Observações</h6>
                <p><?= nl2br(sanitize($examB['notes'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($imagesB)): ?>
                <h6 class="fw-semibold mt-3">Imagens</h6>
                <div class="row g-2">
                    <?php foreach ($imagesB as $img): ?>
                    <div class="col-6">
                        <a href="<?= baseUrl($img['file_path']) ?>" target="_blank">
                            <img src="<?= baseUrl($img['file_path']) ?>" class="img-fluid rounded" style="width:100%;height:140px;object-fit:cover;">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif (empty($similarExams)): ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <i class="bi bi-arrow-left-right"></i>
            <h4>Não há exames para comparar</h4>
            <p>Este paciente possui apenas um exame do tipo "<?= sanitize($exam['exam_type']) ?>".</p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>