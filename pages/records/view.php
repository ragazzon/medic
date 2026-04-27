<?php
$pageTitle = 'Prontuário';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT mr.*, p.name as patient_name FROM medical_records mr JOIN patients p ON mr.patient_id = p.id WHERE mr.id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    setFlash('danger', 'Prontuário não encontrado.');
    redirect(baseUrl('pages/records/list.php'));
}

// Controle de acesso
if (!canAccessPatient($record['patient_id'])) {
    setFlash('danger', 'Você não tem permissão para acessar este prontuário.');
    redirect(baseUrl('pages/records/list.php'));
}

$pageTitle = $record['title'];

$filesStmt = $pdo->prepare("SELECT * FROM record_files WHERE record_id = ?");
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

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-file-medical me-2"></i><?= sanitize($record['title']) ?></h1>
    <div>
        <?php if (isAdmin()): ?>
        <a href="<?= baseUrl('pages/records/form.php?id=' . $id) ?>" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= baseUrl('pages/records/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Detalhes</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Paciente</strong>
                        <a href="<?= baseUrl('pages/patients/view.php?id=' . $record['patient_id']) ?>"><?= sanitize($record['patient_name']) ?></a>
                    </div>
                    <div class="col-sm-3">
                        <strong class="d-block text-muted small">Data</strong>
                        <?= formatDate($record['record_date']) ?>
                    </div>
                    <div class="col-sm-3">
                        <strong class="d-block text-muted small">Especialidade</strong>
                        <?= sanitize($record['specialty'] ?: '-') ?>
                    </div>
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Médico</strong>
                        <?= sanitize($record['doctor_name'] ?: '-') ?>
                    </div>
                    <?php if (!empty($record['clinic_hospital'])): ?>
                    <div class="col-sm-6">
                        <strong class="d-block text-muted small">Clínica/Hospital</strong>
                        <?= sanitize($record['clinic_hospital']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($record['category'])): ?>
                    <div class="col-sm-3">
                        <strong class="d-block text-muted small">Categoria</strong>
                        <span class="badge bg-primary"><?= sanitize($record['category']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($record['visit_reason'])): ?>
                    <div class="col-12">
                        <strong class="d-block text-muted small">Motivo da Consulta</strong>
                        <?= nl2br(sanitize($record['visit_reason'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($record['description']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-text-paragraph me-2"></i>Descrição</div>
            <div class="card-body"><?= nl2br(sanitize($record['description'])) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($record['symptoms']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-thermometer-half me-2"></i>Sintomas</div>
            <div class="card-body"><?= nl2br(sanitize($record['symptoms'])) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($record['diagnosis']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-search me-2"></i>Diagnóstico</div>
            <div class="card-body"><?= nl2br(sanitize($record['diagnosis'])) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($record['prescription']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-capsule me-2"></i>Prescrição</div>
            <div class="card-body"><?= nl2br(sanitize($record['prescription'])) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($record['notes']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-journal-text me-2"></i>Observações</div>
            <div class="card-body"><?= nl2br(sanitize($record['notes'])) ?></div>
        </div>
        <?php endif; ?>

        <!-- Galeria de Imagens -->
        <?php if (!empty($images)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-images me-2"></i>Imagens (<?= count($images) ?>)</div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach ($images as $idx => $img): ?>
                    <div class="col-md-4 col-6">
                        <a href="<?= baseUrl($img['file_path']) ?>" class="gallery-item" data-gallery="record-gallery" data-index="<?= $idx ?>">
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
            <div class="card-header"><i class="bi bi-paperclip me-2"></i>Arquivos (<?= count($files) ?>)</div>
            <div class="card-body">
                <?php if (empty($files)): ?>
                <p class="text-muted text-center mb-0">Nenhum arquivo anexado</p>
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