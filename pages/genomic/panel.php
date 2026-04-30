<?php
$pageTitle = 'Painel Genético';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();
$patientId = intval($_GET['patient_id'] ?? 0);
$panelCode = $_GET['panel'] ?? '';
if (!$patientId || !canAccessPatient($patientId) || !$panelCode) {
    redirect(baseUrl('pages/patients/list.php'));
}
$pdo = getConnection();
$patient = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$patient->execute([$patientId]);
$patient = $patient->fetch();
$panel = $pdo->prepare('SELECT * FROM pgx_panels WHERE code=?');
$panel->execute([$panelCode]);
$panel = $panel->fetch();
if (!$panel) {
    redirect(baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId));
}
$results = getPanelResults($patientId, $panelCode);

// Dicionário de nomes claros para genes (para quem não é médico)
$geneNames = [
    'ACE' => 'Enzima Conversora de Angiotensina (controle da pressão)',
    'ADD1' => 'Alfa-Aducina (sensibilidade ao sal)',
    'ADRB2' => 'Receptor Beta-2 (resposta ao salbutamol/broncodilatadores)',
    'AGT' => 'Angiotensinogênio (pressão arterial)',
    'F2' => 'Protrombina (coagulação do sangue)',
    'F5' => 'Fator V de Leiden (risco de trombose)',
    'VKORC1' => 'Alvo da Varfarina (sensibilidade a anticoagulantes)',
    'CYP2C9' => 'Enzima do Fígado CYP2C9 (metaboliza anti-inflamatórios e varfarina)',
    'CYP2C19' => 'Enzima do Fígado CYP2C19 (metaboliza omeprazol, antidepressivos)',
    'CYP2D6' => 'Enzima do Fígado CYP2D6 (metaboliza 40+ medicamentos)',
    'CYP3A4' => 'Enzima do Fígado CYP3A4 (metaboliza maioria dos medicamentos)',
    'CYP3A5' => 'Enzima do Fígado CYP3A5 (tacrolimo, midazolam)',
    'CYP1A2' => 'Enzima do Fígado CYP1A2 (metaboliza cafeína, melatonina)',
    'SLCO1B1' => 'Transportador Hepático OATP1B1 (estatinas/colesterol)',
    'DPYD' => 'Enzima DPD (metaboliza quimioterápicos 5-FU)',
    'TPMT' => 'Enzima TPMT (metaboliza imunossupressores)',
    'COMT' => 'Enzima COMT (degrada dopamina — dor e humor)',
    'HTR1A' => 'Receptor de Serotonina 1A (resposta a antidepressivos)',
    'HTR2A' => 'Receptor de Serotonina 2A (resposta a antidepressivos)',
    'BDNF' => 'Fator Neurotrófico BDNF (crescimento neuronal)',
    'FKBP5' => 'Proteína FKBP5 (eixo do estresse)',
    'DRD2' => 'Receptor de Dopamina D2 (resposta a antipsicóticos)',
    'OPRM1' => 'Receptor Opioide Mu (resposta à morfina/opioides)',
    'MTHFR' => 'Enzima MTHFR (metabolismo do ácido fólico)',
    'MTRR' => 'Enzima MTRR (reciclagem de vitamina B12)',
    'ABCB1' => 'Transportador P-glicoproteína (barreira cerebral)',
    'G6PD' => 'Enzima G6PD (proteção dos glóbulos vermelhos)',
    'NOS1AP' => 'Proteína NOS1AP (intervalo QT do coração)',
    'MC4R' => 'Receptor MC4R (controle do apetite/peso)',
    'HTR2C' => 'Receptor de Serotonina 2C (ganho de peso com antipsicóticos)',
    'CLOCK' => 'Gene CLOCK (ritmo circadiano/sono)',
    'ACTN3' => 'Proteína Alfa-Actinina 3 (fibras musculares rápidas)',
    'COL1A1' => 'Colágeno Tipo 1 (ossos e tendões)',
    'HLA-B' => 'Antígeno HLA-B (reações alérgicas graves a medicamentos)',
    'TCF7L2' => 'Fator TCF7L2 (risco de diabetes tipo 2)',
    'GNB3' => 'Subunidade Beta-3 da Proteína G (resposta a diuréticos)',
    'SLC6A4' => 'Transportador de Serotonina (resposta a ISRS)',
];

// Debug: check if patient has genotype data
$totalSnps = $pdo->prepare('SELECT COUNT(*) FROM patient_genotypes WHERE patient_id=?');
$totalSnps->execute([$patientId]);
$snpCount = $totalSnps->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi <?= $panel['icon'] ?> me-2"></i><?= sanitize($panel['name']) ?> — <?= sanitize($patient['name']) ?></h1>
    <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
</div>

<?php if ($snpCount == 0): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><strong>Nenhum SNP importado!</strong> O upload do CSV pode ter falhado. <a href="<?= baseUrl('pages/genomic/upload.php?patient_id=' . $patientId) ?>">Tente novamente</a>.</div>
<?php else: ?>
<div class="alert alert-light border mb-3"><i class="bi bi-database me-1"></i><?= number_format($snpCount, 0, ',', '.') ?> SNPs no banco para este paciente.</div>
<?php endif; ?>

<?php if (empty($results)): ?>
<div class="alert alert-info">Nenhum resultado para este painel.</div>
<?php else: ?>
<div class="row g-3">
<?php foreach ($results as $r):
    $status = $r['status'] ?? 'unknown';
    $borderClass = $status === 'risk' ? 'border-danger' : ($status === 'attention' ? 'border-warning' : ($status === 'normal' ? 'border-success' : 'border-secondary'));
    $bgClass = $status === 'risk' ? 'bg-danger-subtle' : ($status === 'attention' ? 'bg-warning-subtle' : ($status === 'normal' ? 'bg-success-subtle' : ''));
?>
<div class="col-md-6 col-xl-4">
<div class="card h-100 border-start border-4 <?= $borderClass ?>">
<div class="card-body">
<div class="d-flex justify-content-between align-items-start mb-2">
    <div>
        <h5 class="mb-0"><?= $r['gene_symbol'] ?></h5>
        <small class="text-primary fw-bold"><?= $geneNames[$r['gene_symbol']] ?? ($r['gene_name'] ?? $r['variant_name'] ?? '') ?></small>
        <?php if ($r['gene_desc'] ?? ''): ?><br><small class="text-muted fst-italic"><?= sanitize($r['gene_desc']) ?></small><?php endif; ?>
    </div>
    <?= genomicStatusBadge($status) ?>
</div>

<table class="table table-sm mb-2">
<tr><td class="text-muted" style="width:35%">SNP</td><td><code><?= $r['rsid'] ?></code> <small class="text-muted">(<?= $r['variant_name'] ?? '' ?>)</small></td></tr>
<tr><td class="text-muted">Genótipo</td><td><strong class="fs-5"><?= $r['patient_genotype'] ?? 'N/D' ?></strong></td></tr>
<tr><td class="text-muted">Fenótipo</td><td class="<?= $bgClass ?> rounded px-2"><?= sanitize($r['phenotype'] ?? 'Não avaliado') ?></td></tr>
<tr><td class="text-muted">Evidência</td><td>
<span class="badge bg-secondary"><?= $r['evidence_level'] ?? '?' ?></span>
<span class="badge <?= $r['clinical_significance'] === 'high' ? 'bg-danger' : ($r['clinical_significance'] === 'moderate' ? 'bg-warning text-dark' : 'bg-light text-dark') ?>"><?= $r['clinical_significance'] ?? '' ?></span>
</td></tr>
</table>

<?php if ($r['phenotype_normal'] ?? ''): ?>
<div class="small mb-1"><span class="badge bg-success-subtle text-success">Normal:</span> <?= sanitize($r['phenotype_normal']) ?></div>
<?php endif; ?>
<?php if ($r['phenotype_het'] ?? ''): ?>
<div class="small mb-1"><span class="badge bg-warning-subtle text-warning">Heterozigoto:</span> <?= sanitize($r['phenotype_het']) ?></div>
<?php endif; ?>
<?php if ($r['phenotype_risk'] ?? ''): ?>
<div class="small mb-1"><span class="badge bg-danger-subtle text-danger">Risco:</span> <?= sanitize($r['phenotype_risk']) ?></div>
<?php endif; ?>

<?php if ($r['interpretation'] ?? $r['recommendations'] ?? ''): ?>
<div class="alert alert-light mt-2 mb-0 py-2 px-3">
<i class="bi bi-lightbulb me-1 text-warning"></i>
<strong>Recomendação:</strong> <?= sanitize($r['interpretation'] ?? $r['recommendations'] ?? '') ?>
</div>
<?php endif; ?>
</div></div></div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>