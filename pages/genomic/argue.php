<?php
$pageTitle = 'Argumente com o Médico';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();
$patientId = intval($_GET['patient_id'] ?? 0);
$drugName = $_GET['drug'] ?? null;
if (!$patientId || !canAccessPatient($patientId)) {
    redirect(baseUrl('pages/patients/list.php'));
}
$pdo = getConnection();
$patient = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$patient->execute([$patientId]);
$patient = $patient->fetch();
$drugAnalysis = getDrugAnalysis($patientId, $drugName);
$allDrugs = getDrugAnalysis($patientId);
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi bi-chat-left-quote me-2"></i>Argumente com o Médico — <?= sanitize($patient['name']) ?></h1>
    <div>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <a href="<?= baseUrl("pages/genomic/dashboard.php?patient_id=" . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<?php foreach ($drugAnalysis as $drug): ?>
<div class="card mb-3 border-start border-4 <?= $drug["worst_status"] === "risk" ? "border-danger" : ($drug["worst_status"] === "attention" ? "border-warning" : "border-success") ?>">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0"><?= genomicStatusIcon($drug['worst_status']) ?> <?= sanitize($drug['name']) ?></h5>
        <?= genomicStatusBadge($drug['worst_status']) ?>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Gene</th>
                    <th>SNP</th>
                    <th>Genótipo</th>
                    <th>Tipo</th>
                    <th>Efeito</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($drug['genes'] as $g): ?>
                <tr>
                    <td><b><?= $g['gene_symbol'] ?></b></td>
                    <td><code><?= $g['rsid'] ?? '-' ?></code></td>
                    <td><b><?= $g['patient_genotype'] ?? 'N/D' ?></b></td>
                    <td><?= $g['interaction_type'] ?></td>
                    <td><?= sanitize($g['effect_description'] ?? '') ?></td>
                    <td><?= genomicStatusBadge($g['status'] ?? 'unknown') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($drugAnalysis)): ?>
<div class="alert alert-warning">Nenhuma interação encontrada.</div>
<?php endif; ?>

<style>
@media print {
    .sidebar, .page-header > div:last-child, nav { display: none !important; }
    .card { break-inside: avoid; }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>