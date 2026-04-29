<?php
$pageTitle = 'Dashboard Genético';
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
$summary = getGenomicSummary($patientId);
if (!$summary) {
    setFlash('info', 'Sem dados genéticos.');
    redirect(baseUrl('pages/genomic/upload.php?patient_id=' . $patientId));
}
$drugs = getDrugAnalysis($patientId);

// Separate panels into categories
$medPanels = []; // Panels about drug metabolism
$riskPanels = []; // Panels about disease risk
$medCodes = ['pharmaco', 'neuro']; // Panels primarily about medications
$riskCodes = ['cardio', 'onco', 'nutri', 'musculo', 'derma', 'immuno', 'endocrino', 'sleep'];
foreach ($summary['panels'] as $pnl) {
    if (in_array($pnl['code'], $medCodes)) $medPanels[] = $pnl;
    else $riskPanels[] = $pnl;
}

// Group drugs by class
$drugsByClass = [];
foreach ($drugs as $d) {
    $cls = $d['class'] ?: 'Outros';
    if (!isset($drugsByClass[$cls])) $drugsByClass[$cls] = [];
    $drugsByClass[$cls][] = $d;
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi bi-dna me-2"></i>Análise Genética — <?= sanitize($patient['name']) ?></h1>
    <div>
        <a href="<?= baseUrl('pages/genomic/ancestry.php?patient_id=' . $patientId) ?>" class="btn btn-outline-info btn-sm"><i class="bi bi-globe-americas me-1"></i>Ancestralidade</a>
        <a href="<?= baseUrl('pages/genomic/argue.php?patient_id=' . $patientId) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-chat-left-quote me-1"></i>Argumente com o Médico</a>
        <a href="<?= baseUrl('pages/genomic/upload.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-upload me-1"></i>Re-importar</a>
        <a href="<?= baseUrl('pages/patients/view.php?id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<?php if ($summary['import']): ?>
<div class="alert alert-light border mb-4">
    <i class="bi bi-info-circle me-1"></i>
    <strong><?= sanitize($summary['import']['file_name']) ?></strong> |
    <?= number_format($summary['import']['imported_snps'], 0, ',', '.') ?> SNPs |
    <?= $summary['import']['genome_build'] ?? '' ?> |
    <?= formatDateTime($summary['import']['imported_at']) ?>
</div>
<?php endif; ?>

<!-- ===== SEÇÃO 1: MEDICAMENTOS ===== -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-capsule me-2"></i>Análise de Medicamentos por Categoria</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">Como o organismo metaboliza cada medicamento com base no perfil genético.</p>

        <?php foreach ($drugsByClass as $className => $classDrugs): ?>
        <h6 class="mt-3 mb-2 text-primary"><i class="bi bi-tag me-1"></i><?= sanitize($className) ?></h6>
        <div class="row g-3 mb-3">
            <?php foreach ($classDrugs as $d): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= baseUrl('pages/genomic/drug_detail.php?patient_id=' . $patientId . '&drug=' . urlencode($d['name'])) ?>" class="text-decoration-none">
                <div class="card h-100 border-start border-4 <?= $d['worst_status'] === 'risk' ? 'border-danger' : ($d['worst_status'] === 'attention' ? 'border-warning' : 'border-success') ?> drug-card-link">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0 text-dark"><?= sanitize($d['name']) ?></h6>
                            <?= genomicStatusBadge($d['worst_status']) ?>
                        </div>
                        <div class="mt-2">
                            <?php foreach ($d['genes'] as $g): ?>
                            <div class="d-flex justify-content-between align-items-center py-1 border-top">
                                <div>
                                    <small><strong class="text-dark"><?= $g['gene_symbol'] ?></strong></small>
                                    <?php if ($g['phenotype']): ?><br><small class="text-muted"><?= sanitize($g['phenotype']) ?></small><?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <small><?= genomicStatusIcon($g['status'] ?? 'unknown') ?></small>
                                    <small class="fw-bold text-dark"><?= $g['patient_genotype'] ?? 'N/D' ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-primary"><i class="bi bi-arrow-right-circle me-1"></i>Ver detalhes</small>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <!-- Sub-painéis de medicamentos -->
        <h6 class="mt-4 mb-2"><i class="bi bi-grid me-1"></i>Painéis Detalhados de Metabolismo</h6>
        <div class="row g-2">
            <?php foreach ($medPanels as $pnl): ?>
            <div class="col-md-6">
                <a href="<?= baseUrl('pages/genomic/panel.php?patient_id=' . $patientId . '&panel=' . $pnl['code']) ?>" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi <?= $pnl['icon'] ?> me-2" style="color:<?= $pnl['color'] ?>"></i>
                                <span><?= sanitize($pnl['name']) ?></span>
                            </div>
                            <div class="d-flex gap-1">
                                <?php if ($pnl['risk_count']): ?><span class="badge bg-danger"><?= $pnl['risk_count'] ?></span><?php endif; ?>
                                <?php if ($pnl['attention_count']): ?><span class="badge bg-warning text-dark"><?= $pnl['attention_count'] ?></span><?php endif; ?>
                                <span class="badge bg-success"><?= $pnl['normal_count'] ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ===== SEÇÃO 2: RISCOS DE DOENÇAS ===== -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-shield-exclamation me-2"></i>Riscos Genéticos para Doenças</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">Predisposições genéticas identificadas. <strong>NÃO</strong> significam que a doença vai ocorrer.</p>
        <div class="row g-3">
            <?php foreach ($riskPanels as $pnl): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= baseUrl('pages/genomic/panel.php?patient_id=' . $patientId . '&panel=' . $pnl['code']) ?>" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background:<?= $pnl['color'] ?>20;">
                                    <i class="bi <?= $pnl['icon'] ?>" style="color:<?= $pnl['color'] ?>"></i>
                                </div>
                                <h6 class="mb-0"><?= sanitize($pnl['name']) ?></h6>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($pnl['risk_count']): ?><span class="badge bg-danger"><?= $pnl['risk_count'] ?> risco</span><?php endif; ?>
                                <?php if ($pnl['attention_count']): ?><span class="badge bg-warning text-dark"><?= $pnl['attention_count'] ?> atenção</span><?php endif; ?>
                                <span class="badge bg-success"><?= $pnl['normal_count'] ?> normal</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>