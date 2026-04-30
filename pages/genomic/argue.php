<?php
$pageTitle = 'Argumente com o Médico';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();
$patientId = intval($_GET['patient_id'] ?? 0);
if (!$patientId || !canAccessPatient($patientId)) {
    redirect(baseUrl('pages/patients/list.php'));
}
$pdo = getConnection();
$patient = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$patient->execute([$patientId]);
$patient = $patient->fetch();

// Busca
$search = trim($_GET['q'] ?? '');
$drugAnalysis = [];

if ($search) {
    // Buscar por nome do medicamento, nome comercial ou classe
    $searchLike = '%' . $search . '%';
    
    // Busca na pgx_drug_genes (todos os medicamentos)
    $stmt = $pdo->prepare("
        SELECT DISTINCT dg.drug_name, dg.drug_class, dg.gene_symbol, dg.rsid, 
               dg.interaction_type, dg.effect_description, 
               dg.recommendation_normal, dg.recommendation_het, dg.recommendation_risk,
               dg.evidence_level,
               pg.genotype as patient_genotype
        FROM pgx_drug_genes dg
        LEFT JOIN patient_genotypes pg ON pg.rsid = dg.rsid AND pg.patient_id = ?
        WHERE dg.drug_name LIKE ? 
           OR dg.drug_class LIKE ?
           OR dg.drug_name IN (
               SELECT dd.drug_name FROM pgx_drug_details dd 
               WHERE dd.commercial_names LIKE ?
           )
        ORDER BY dg.drug_name, dg.gene_symbol
    ");
    $stmt->execute([$patientId, $searchLike, $searchLike, $searchLike]);
    $results = $stmt->fetchAll();
    
    // Agrupar por medicamento
    foreach ($results as $row) {
        $name = $row['drug_name'];
        if (!isset($drugAnalysis[$name])) {
            $drugAnalysis[$name] = [
                'name' => $name,
                'class' => $row['drug_class'],
                'genes' => []
            ];
        }
        $drugAnalysis[$name]['genes'][] = $row;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi bi-chat-left-quote me-2"></i>Argumente com o Médico — <?= sanitize($patient['name']) ?></h1>
    <div>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <a href="<?= baseUrl("pages/genomic/dashboard.php?patient_id=" . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<!-- BARRA DE BUSCA -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="patient_id" value="<?= $patientId ?>">
            <div class="col-md-8">
                <label class="form-label"><i class="bi bi-search me-1"></i>Buscar medicamento</label>
                <input type="text" name="q" class="form-control form-control-lg" 
                       placeholder="Digite o nome comercial, princípio ativo ou classe (ex: Ritalina, Omeprazol, Antidepressivo...)" 
                       value="<?= sanitize($search) ?>" autofocus>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-search me-1"></i>Buscar</button>
            </div>
            <div class="col-md-2">
                <a href="?patient_id=<?= $patientId ?>" class="btn btn-outline-secondary btn-lg w-100">Limpar</a>
            </div>
        </form>
        <small class="text-muted mt-2 d-block">
            Exemplos: <a href="?patient_id=<?= $patientId ?>&q=Omeprazol">Omeprazol</a> · 
            <a href="?patient_id=<?= $patientId ?>&q=Antidepressivo">Antidepressivos</a> · 
            <a href="?patient_id=<?= $patientId ?>&q=Ritalina">Ritalina</a> · 
            <a href="?patient_id=<?= $patientId ?>&q=Tramadol">Tramadol</a> · 
            <a href="?patient_id=<?= $patientId ?>&q=Estatina">Estatinas</a>
        </small>
    </div>
</div>

<?php if ($search && !empty($drugAnalysis)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Encontrados <strong><?= count($drugAnalysis) ?></strong> medicamento(s) para "<strong><?= sanitize($search) ?></strong>"
    </div>

    <?php foreach ($drugAnalysis as $drug): ?>
    <div class="card mb-3 border-start border-4 border-primary">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-capsule me-2"></i><?= sanitize($drug['name']) ?>
                <?php if ($drug['class']): ?>
                    <small class="text-muted ms-2">(<?= sanitize($drug['class']) ?>)</small>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Gene</th>
                        <th>SNP</th>
                        <th>Genótipo do Paciente</th>
                        <th>Tipo de Interação</th>
                        <th>Efeito</th>
                        <th>Recomendação</th>
                        <th>Evidência</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($drug['genes'] as $g): ?>
                    <tr>
                        <td><strong><?= sanitize($g['gene_symbol']) ?></strong></td>
                        <td><code><?= $g['rsid'] ?? '-' ?></code></td>
                        <td>
                            <?php if ($g['patient_genotype']): ?>
                                <span class="badge bg-dark"><?= sanitize($g['patient_genotype']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">N/D</span>
                            <?php endif; ?>
                        </td>
                        <td><?= sanitize($g['interaction_type'] ?? '') ?></td>
                        <td><?= sanitize($g['effect_description'] ?? '') ?></td>
                        <td>
                            <?php 
                            // Mostrar recomendação baseada no genótipo
                            $geno = $g['patient_genotype'] ?? '';
                            if (!$geno) {
                                echo '<em class="text-muted">Genótipo não disponível no chip</em>';
                            } else {
                                echo sanitize($g['recommendation_normal'] ?? '');
                            }
                            ?>
                        </td>
                        <td><span class="badge bg-<?= in_array($g['evidence_level'], ['1A','1B']) ? 'success' : (in_array($g['evidence_level'], ['2A','2B']) ? 'warning' : 'secondary') ?>"><?= $g['evidence_level'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>

<?php elseif ($search && empty($drugAnalysis)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Nenhum medicamento encontrado para "<strong><?= sanitize($search) ?></strong>". Tente outro nome ou verifique a ortografia.
    </div>

<?php elseif (!$search): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-search display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Pesquise um medicamento</h4>
            <p class="text-muted">Digite o nome comercial (ex: Vonau, Ritalina), princípio ativo (ex: Omeprazol, Sertralina) ou classe terapêutica (ex: Antidepressivo, Estatina) para ver a análise farmacogenética.</p>
            <p class="text-muted"><small>Base de dados: 232 medicamentos × 61 genes analisados</small></p>
        </div>
    </div>
<?php endif; ?>

<style>
@media print {
    .sidebar, .page-header > div:last-child, nav, form { display: none !important; }
    .card { break-inside: avoid; }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>