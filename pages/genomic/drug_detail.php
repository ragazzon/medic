<?php
$pageTitle = 'Detalhe do Medicamento';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();

$patientId = intval($_GET['patient_id'] ?? 0);
$drugName = $_GET['drug'] ?? '';

if (!$patientId || !canAccessPatient($patientId) || !$drugName) {
    redirect(baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId));
}

$pdo = getConnection();

// Dados do paciente
$patient = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$patient->execute([$patientId]);
$patient = $patient->fetch();

// Análise genética do medicamento
$drugAnalysis = getDrugAnalysis($patientId, $drugName);
$drug = $drugAnalysis[$drugName] ?? null;

if (!$drug) {
    setFlash('warning', 'Medicamento não encontrado na análise genética.');
    redirect(baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId));
}

// Buscar detalhes do medicamento (tabela pgx_drug_details)
$detail = null;
try {
    $stmt = $pdo->prepare("SELECT dd.*, dc.name as class_name, dc.icon as class_icon, dc.color as class_color 
                           FROM pgx_drug_details dd 
                           LEFT JOIN pgx_drug_classes dc ON dd.class_id = dc.id 
                           WHERE dd.drug_name = ? AND dd.is_active = 1 
                           LIMIT 1");
    $stmt->execute([$drugName]);
    $detail = $stmt->fetch();
} catch (Exception $e) {
    // Tabela pode não existir ainda - não é erro crítico
}

// Buscar glossário para tooltips
$glossary = [];
try {
    $glossaryStmt = $pdo->query("SELECT term, definition FROM pgx_glossary");
    foreach ($glossaryStmt->fetchAll() as $g) {
        $glossary[strtolower($g['term'])] = $g['definition'];
    }
} catch (Exception $e) {
    // Tabela pode não existir ainda
}

// Buscar ancestralidade do paciente
$ancestry = null;
try {
    $ancStmt = $pdo->prepare("SELECT * FROM patient_ancestry WHERE patient_id = ?");
    $ancStmt->execute([$patientId]);
    $ancestry = $ancStmt->fetch();
} catch (Exception $e) {
    // Tabela pode não existir ainda
}

$pageTitle = $drugName . ' — Análise Genética';
require_once __DIR__ . '/../../includes/header.php';

/**
 * Helper: adiciona tooltip em termos técnicos
 */
function addTooltips($text, $glossary) {
    if (empty($text) || empty($glossary)) return sanitize($text);
    $safeText = sanitize($text);
    foreach ($glossary as $term => $definition) {
        $defSafe = htmlspecialchars($definition, ENT_QUOTES, 'UTF-8');
        $pattern = '/\b(' . preg_quote(ucfirst($term), '/') . '|' . preg_quote(strtoupper($term), '/') . '|' . preg_quote($term, '/') . ')\b/u';
        $replacement = '<span class="glossary-term" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $defSafe . '">$1 <i class="bi bi-info-circle-fill" style="font-size:0.7em;opacity:0.6;"></i></span>';
        $safeText = preg_replace($pattern, $replacement, $safeText, 1); // Apenas primeira ocorrência
    }
    return $safeText;
}
?>

<div class="page-header">
    <h1><i class="bi bi-capsule me-2"></i><?= sanitize($drugName) ?></h1>
    <div>
        <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Voltar ao Dashboard</a>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>
</div>

<!-- Status geral do medicamento -->
<div class="card mb-4 border-start border-4 <?= $drug['worst_status'] === 'risk' ? 'border-danger' : ($drug['worst_status'] === 'attention' ? 'border-warning' : 'border-success') ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><?= sanitize($drugName) ?></h4>
                <?php if ($detail && $detail['class_name']): ?>
                <span class="badge" style="background-color:<?= $detail['class_color'] ?? '#6c757d' ?>">
                    <i class="bi <?= $detail['class_icon'] ?? 'bi-capsule' ?> me-1"></i><?= sanitize($detail['class_name']) ?>
                </span>
                <?php elseif ($drug['class']): ?>
                <span class="badge bg-secondary"><?= sanitize($drug['class']) ?></span>
                <?php endif; ?>
                <?php if ($detail && $detail['commercial_names']): ?>
                <div class="mt-2 text-muted small"><i class="bi bi-tag me-1"></i><strong>Nomes comerciais:</strong> <?= sanitize($detail['commercial_names']) ?></div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <?= genomicStatusBadge($drug['worst_status']) ?>
                <div class="small text-muted mt-1">Paciente: <?= sanitize($patient['name']) ?></div>
            </div>
        </div>
    </div>
</div>

<?php if ($detail && $detail['description']): ?>
<!-- Seção 1: Descrição do Medicamento -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-info-circle me-2"></i><strong>Sobre o Medicamento</strong>
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(addTooltips($detail['description'], $glossary)) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($detail && $detail['understanding_result']): ?>
<!-- Seção 2: Entendendo seu Resultado -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-lightbulb me-2 text-warning"></i><strong>Entendendo seu Resultado</strong>
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(addTooltips($detail['understanding_result'], $glossary)) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Seção 3: Dados Técnicos (sempre visível - vem da análise genética) -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-cpu me-2"></i><strong>Dados Genéticos</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th><span class="glossary-term" data-bs-toggle="tooltip" title="<?= htmlspecialchars($glossary['snp'] ?? 'Polimorfismo de Nucleotídeo Único', ENT_QUOTES) ?>">SNP <i class="bi bi-info-circle-fill" style="font-size:0.7em;opacity:0.6;"></i></span></th>
                        <th><span class="glossary-term" data-bs-toggle="tooltip" title="<?= htmlspecialchars($glossary['cromossomo'] ?? '', ENT_QUOTES) ?>">Cromossomo <i class="bi bi-info-circle-fill" style="font-size:0.7em;opacity:0.6;"></i></span></th>
                        <th>Gene</th>
                        <th><span class="glossary-term" data-bs-toggle="tooltip" title="<?= htmlspecialchars($glossary['genótipo'] ?? 'Combinação de alelos herdados', ENT_QUOTES) ?>">Seu Genótipo <i class="bi bi-info-circle-fill" style="font-size:0.7em;opacity:0.6;"></i></span></th>
                        <th>Tipo de Interação</th>
                        <th><span class="glossary-term" data-bs-toggle="tooltip" title="<?= htmlspecialchars($glossary['fenótipo'] ?? '', ENT_QUOTES) ?>">Fenótipo <i class="bi bi-info-circle-fill" style="font-size:0.7em;opacity:0.6;"></i></span></th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($drug['genes'] as $g): ?>
                    <tr>
                        <td><code><?= $g['rsid'] ?? '-' ?></code></td>
                        <td><?= $detail['chromosome'] ?? '-' ?></td>
                        <td><strong><?= $g['gene_symbol'] ?></strong></td>
                        <td><span class="fs-5 fw-bold"><?= $g['patient_genotype'] ?? 'N/D' ?></span></td>
                        <td><?= sanitize($g['interaction_type'] ?? '') ?></td>
                        <td><?= sanitize($g['phenotype'] ?? '') ?></td>
                        <td><?= genomicStatusBadge($g['status'] ?? 'unknown') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($detail && $detail['study_population']): ?>
        <div class="mt-3 small text-muted">
            <i class="bi bi-globe me-1"></i><strong>População de estudo:</strong> <?= sanitize($detail['study_population']) ?>
            <?php if ($ancestry && $ancestry['primary_population']): ?>
            | <strong>Ancestralidade do paciente:</strong> <?= sanitize($ancestry['primary_population']) ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($detail && $detail['genotype_results']): ?>
<!-- Seção 4: Resultado conforme genótipos -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-clipboard-data me-2"></i><strong>Resultado conforme Genótipos</strong>
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(addTooltips($detail['genotype_results'], $glossary)) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($detail && $detail['suggestions']): ?>
<!-- Seção 5: Sugestões -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-hand-thumbs-up me-2 text-success"></i><strong>Sugestões</strong>
    </div>
    <div class="card-body">
        <p><?= nl2br(addTooltips($detail['suggestions'], $glossary)) ?></p>
        
        <!-- Disclaimer -->
        <div class="alert alert-warning mb-0 mt-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Importante:</strong> 
            <?php if ($detail['disclaimer']): ?>
                <?= sanitize($detail['disclaimer']) ?>
            <?php else: ?>
                Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!$detail): ?>
<!-- Mensagem quando não há detalhes preenchidos ainda -->
<div class="card mb-4">
    <div class="card-body">
        <div class="text-center py-4">
            <i class="bi bi-journal-text text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">Informações detalhadas em breve</h5>
            <p class="text-muted">As informações detalhadas sobre este medicamento ainda estão sendo preenchidas. Os dados genéticos acima são baseados na análise do seu genoma.</p>
        </div>
        
        <!-- Disclaimer padrão -->
        <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Importante:</strong> Nunca inicie, interrompa ou altere tratamentos sem orientação médica. Para isso, indicamos que consulte o seu médico.
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($detail && $detail['references_urls']): ?>
<!-- Referências -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-book me-2"></i><strong>Referências Científicas</strong>
    </div>
    <div class="card-body">
        <small class="text-muted"><?= nl2br(sanitize($detail['references_urls'])) ?></small>
    </div>
</div>
<?php endif; ?>

<!-- Inicializar tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) {
        return new bootstrap.Tooltip(el);
    });
});
</script>

<style>
.glossary-term {
    border-bottom: 1px dotted #6c757d;
    cursor: help;
}
@media print {
    .sidebar, .page-header > div:last-child, nav, .topbar { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { break-inside: avoid; border: 1px solid #dee2e6 !important; }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>