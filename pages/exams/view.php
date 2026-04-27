<?php
$pageTitle = 'Exame';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

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

$pageTitle = $exam['exam_type'] . ' - ' . $exam['patient_name'];

// Carregar especialidades do exame
$specStmt = $pdo->prepare("SELECT specialty_name FROM exam_specialties WHERE exam_id = ? ORDER BY specialty_name");
$specStmt->execute([$id]);
$examSpecialties = $specStmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($examSpecialties) && !empty($exam['specialty'])) {
    $examSpecialties = [$exam['specialty']];
}

$filesStmt = $pdo->prepare("SELECT * FROM exam_files WHERE exam_id = ? ORDER BY id");
$filesStmt->execute([$id]);
$files = $filesStmt->fetchAll();

// Categorizar arquivos
$images = [];
$pdfs = [];
$audios = [];
$videos = [];
$words = [];
$others = [];

foreach ($files as $f) {
    $cat = getFileCategory($f['file_type'], $f['file_name'] ?? '', $f['file_path'] ?? '');
    switch ($cat) {
        case 'image': $images[] = $f; break;
        case 'pdf':   $pdfs[] = $f; break;
        case 'audio': $audios[] = $f; break;
        case 'video': $videos[] = $f; break;
        case 'word':  $words[] = $f; break;
        default:      $others[] = $f; break;
    }
}

// Buscar exames anteriores do mesmo tipo para comparação
$prevExams = $pdo->prepare("
    SELECT e.id, e.exam_date, e.lab_clinic 
    FROM exams e 
    WHERE e.patient_id = ? AND e.exam_type = ? AND e.id != ?
    ORDER BY e.exam_date DESC LIMIT 10
");
$prevExams->execute([$exam['patient_id'], $exam['exam_type'], $id]);
$prevExams = $prevExams->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-clipboard2-pulse me-2"></i><?= sanitize($exam['exam_type']) ?></h1>
    <div>
        <?php if (!empty($prevExams)): ?>
        <a href="<?= baseUrl('pages/exams/compare.php?id=' . $id) ?>" class="btn btn-info"><i class="bi bi-arrow-left-right me-1"></i>Comparar</a>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
        <a href="<?= baseUrl('pages/exams/form.php?id=' . $id) ?>" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= baseUrl('pages/exams/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Info -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Detalhes do Exame</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Paciente</strong>
                        <a href="<?= baseUrl('pages/patients/view.php?id=' . $exam['patient_id']) ?>"><?= sanitize($exam['patient_name']) ?></a>
                    </div>
                    <div class="col-sm-3">
                        <strong class="d-block text-muted small">Data</strong>
                        <?= formatDate($exam['exam_date']) ?>
                    </div>
                    <div class="col-sm-3">
                        <strong class="d-block text-muted small">Tipo</strong>
                        <span class="badge bg-info"><?= sanitize($exam['exam_type']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Laboratório</strong>
                        <?= sanitize($exam['lab_clinic'] ?: '-') ?>
                    </div>
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Médico Solicitante</strong>
                        <?= sanitize($exam['doctor_name'] ?: '-') ?>
                    </div>
                    <?php if (!empty($examSpecialties)): ?>
                    <div class="col-12">
                        <strong class="d-block text-muted small">Especialidades</strong>
                        <?php foreach ($examSpecialties as $sp): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1"><i class="bi bi-heart-pulse me-1"></i><?= sanitize($sp) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <?php if ($exam['results']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-clipboard-data me-2"></i>Resultados</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <pre class="mb-0" style="white-space:pre-wrap;font-family:inherit;"><?= sanitize($exam['results']) ?></pre>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($exam['notes']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-journal-text me-2"></i>Observações</div>
            <div class="card-body"><?= nl2br(sanitize($exam['notes'])) ?></div>
        </div>
        <?php endif; ?>

        <!-- Galeria de Imagens -->
        <?php if (!empty($images)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-images me-2"></i>Imagens do Exame (<?= count($images) ?>)</div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach ($images as $idx => $img): ?>
                    <div class="col-md-4 col-6">
                        <a href="<?= baseUrl($img['file_path']) ?>" class="gallery-item" data-gallery="exam-gallery" data-index="<?= $idx ?>">
                            <img src="<?= baseUrl($img['file_path']) ?>" class="img-fluid rounded" alt="<?= sanitize($img['file_name']) ?>" style="width:100%;height:180px;object-fit:cover;cursor:pointer;">
                            <div class="gallery-overlay">
                                <i class="bi bi-zoom-in"></i>
                            </div>
                        </a>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <small class="text-muted text-truncate"><?= sanitize($img['file_name']) ?></small>
                            <a href="<?= baseUrl($img['file_path']) ?>" download="<?= sanitize($img['file_name']) ?>" class="btn btn-sm btn-outline-primary py-0 px-1" title="Baixar"><i class="bi bi-download"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- PDFs inline -->
        <?php if (!empty($pdfs)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Documentos PDF (<?= count($pdfs) ?>)</div>
            <div class="card-body">
                <?php foreach ($pdfs as $pdf): ?>
                    <?= renderFilePreview($pdf, 'lg') ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Word documents inline -->
        <?php if (!empty($words)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-earmark-word me-2"></i>Documentos Word (<?= count($words) ?>)</div>
            <div class="card-body">
                <?php foreach ($words as $word): ?>
                    <?= renderFilePreview($word, 'lg') ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Videos inline -->
        <?php if (!empty($videos)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-camera-video me-2"></i>Vídeos (<?= count($videos) ?>)</div>
            <div class="card-body">
                <?php foreach ($videos as $video): ?>
                    <?= renderFilePreview($video, 'lg') ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Audio inline -->
        <?php if (!empty($audios)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-music-note-beamed me-2"></i>Áudios (<?= count($audios) ?>)</div>
            <div class="card-body">
                <?php foreach ($audios as $audio): ?>
                    <?= renderFilePreview($audio, 'lg') ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Resumo de Arquivos -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-paperclip me-2"></i>Todos os Arquivos (<?= count($files) ?>)</div>
            <div class="card-body">
                <?php if (empty($files)): ?>
                <p class="text-muted text-center mb-0">Nenhum arquivo</p>
                <?php else: ?>
                <?php foreach ($files as $f): 
                    $icon = getFileIcon($f['file_type'], $f['file_name'] ?? '', $f['file_path'] ?? '');
                    $cat = getFileCategory($f['file_type'], $f['file_name'] ?? '', $f['file_path'] ?? '');
                ?>
                <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                    <small class="text-truncate me-2">
                        <i class="bi <?= $icon ?> me-1"></i>
                        <?= sanitize($f['file_name']) ?>
                        <span class="badge bg-light text-dark ms-1"><?= strtoupper($cat) ?></span>
                    </small>
                    <a href="<?= baseUrl($f['file_path']) ?>" download="<?= sanitize($f['file_name']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Others -->
        <?php if (!empty($others)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-earmark me-2"></i>Outros Arquivos</div>
            <div class="card-body">
                <?php foreach ($others as $other): ?>
                    <?= renderFilePreview($other, 'sm') ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Exames anteriores -->
        <?php if (!empty($prevExams)): ?>
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Histórico - <?= sanitize($exam['exam_type']) ?></div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($prevExams as $pe): ?>
                    <a href="<?= baseUrl('pages/exams/view.php?id=' . $pe['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><?= formatDate($pe['exam_date']) ?></span>
                            <small class="text-muted"><?= sanitize($pe['lab_clinic'] ?? '') ?></small>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox Modal -->
<?php if (!empty($images)): ?>
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <span class="text-white" id="lightboxTitle"></span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" src="" class="img-fluid" style="max-height:80vh;object-fit:contain;">
            </div>
            <div class="modal-footer border-0 justify-content-between">
                <button class="btn btn-outline-light" id="lightboxPrev"><i class="bi bi-chevron-left"></i> Anterior</button>
                <span class="text-white" id="lightboxCounter"></span>
                <a href="#" id="lightboxDownload" class="btn btn-outline-success" download><i class="bi bi-download me-1"></i>Baixar</a>
                <button class="btn btn-outline-light" id="lightboxNext">Próxima <i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = <?= json_encode(array_values(array_map(fn($img) => ['src' => baseUrl($img['file_path']), 'name' => $img['file_name']], $images))) ?>;
    let currentIdx = 0;

    function showImage(idx) {
        currentIdx = idx;
        document.getElementById('lightboxImage').src = images[idx].src;
        document.getElementById('lightboxTitle').textContent = images[idx].name;
        document.getElementById('lightboxCounter').textContent = (idx + 1) + ' / ' + images.length;
        const dl = document.getElementById('lightboxDownload');
        dl.href = images[idx].src;
        dl.download = images[idx].name;
    }

    document.querySelectorAll('[data-gallery]').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            showImage(parseInt(this.dataset.index));
            new bootstrap.Modal(document.getElementById('lightboxModal')).show();
        });
    });

    document.getElementById('lightboxPrev').addEventListener('click', () => showImage((currentIdx - 1 + images.length) % images.length));
    document.getElementById('lightboxNext').addEventListener('click', () => showImage((currentIdx + 1) % images.length));

    document.getElementById('lightboxModal').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') showImage((currentIdx - 1 + images.length) % images.length);
        if (e.key === 'ArrowRight') showImage((currentIdx + 1) % images.length);
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>