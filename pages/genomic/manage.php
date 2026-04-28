<?php
$pageTitle = 'Gerenciar Regras Genomicas';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();
if (!isAdmin()) { setFlash('danger', 'Acesso restrito a administradores.'); redirect(baseUrl('pages/dashboard.php')); }
$pdo = getConnection();

// Handle actions
$action = $_GET['action'] ?? '';
$ruleId = intval($_GET['rule_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    $id = intval($_POST['rule_id'] ?? 0);
    
    if ($act === 'lock') {
        $pdo->prepare("UPDATE pgx_rules SET is_locked=1, updated_at=NOW() WHERE id=?")->execute([$id]);
        setFlash('success', 'Regra travada.');
    } elseif ($act === 'unlock') {
        $pdo->prepare("UPDATE pgx_rules SET is_locked=0, updated_at=NOW() WHERE id=?")->execute([$id]);
        setFlash('success', 'Regra destravada.');
    } elseif ($act === 'delete' && $id) {
        $pdo->prepare("DELETE FROM pgx_rules WHERE id=? AND is_locked=0")->execute([$id]);
        setFlash('success', 'Regra removida.');
    } elseif ($act === 'save') {
        $data = [
            'gene_symbol' => $_POST['gene_symbol'],
            'rsid' => $_POST['rsid'],
            'variant_name' => $_POST['variant_name'],
            'ref_genotype' => $_POST['ref_genotype'],
            'het_genotypes' => $_POST['het_genotypes'],
            'risk_genotypes' => $_POST['risk_genotypes'],
            'phenotype_normal' => $_POST['phenotype_normal'],
            'phenotype_het' => $_POST['phenotype_het'],
            'phenotype_risk' => $_POST['phenotype_risk'],
            'recommendations' => $_POST['recommendations'],
            'clinical_significance' => $_POST['clinical_significance'],
            'evidence_level' => $_POST['evidence_level'],
            'panel_id' => intval($_POST['panel_id']),
            'notes' => $_POST['notes'] ?? '',
            'is_manual' => 1,
            'is_locked' => intval($_POST['is_locked'] ?? 0),
        ];
        if ($id) {
            $sets = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
            $pdo->prepare("UPDATE pgx_rules SET $sets, updated_at=NOW(), updated_by=? WHERE id=?")->execute([...array_values($data), getCurrentUserId(), $id]);
            setFlash('success', 'Regra atualizada.');
        } else {
            $cols = implode(',', array_keys($data));
            $phs = implode(',', array_fill(0, count($data), '?'));
            $pdo->prepare("INSERT INTO pgx_rules ($cols, source) VALUES ($phs, 'manual')")->execute(array_values($data));
            setFlash('success', 'Regra criada.');
        }
    } elseif ($act === 'lock_result') {
        $rid = intval($_POST['result_id'] ?? 0);
        $notes = $_POST['clinical_notes'] ?? '';
        $pdo->prepare("UPDATE patient_pgx_results SET is_locked=1, clinical_notes=?, updated_by=?, updated_at=NOW() WHERE id=?")->execute([$notes, getCurrentUserId(), $rid]);
        setFlash('success', 'Resultado travado.');
    }
    redirect(baseUrl('pages/genomic/manage.php'));
}

// Get all rules with lock info
$rules = $pdo->query("SELECT r.*, p.name as panel_name FROM pgx_rules r LEFT JOIN pgx_panels p ON r.panel_id=p.id ORDER BY r.panel_id, r.gene_symbol")->fetchAll();
$panels = $pdo->query("SELECT * FROM pgx_panels ORDER BY sort_order")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi bi-gear me-2"></i>Gerenciar Regras Genomicas</h1>
    <a href="<?= baseUrl('pages/genomic/index.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<div class="alert alert-info mb-3">
    <i class="bi bi-lock me-1"></i><strong>Sistema de Trava:</strong> Regras travadas NAO serao sobrescritas quando voce importar novos dados ou re-analisar.
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-list me-1"></i>Regras (<?= count($rules) ?>)</span>
        <a href="<?= baseUrl('pages/genomic/manage.php?action=new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Nova Regra</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr><th>Gene</th><th>SNP</th><th>Painel</th><th>Normal</th><th>Het</th><th>Risco</th><th>Trava</th><th>Acoes</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rules as $r): ?>
                <tr class="<?= $r['is_locked'] ? 'table-warning' : '' ?>">
                    <td><strong><?= sanitize($r['gene_symbol']) ?></strong></td>
                    <td><code><?= $r['rsid'] ?></code></td>
                    <td><small><?= sanitize($r['panel_name'] ?? '') ?></small></td>
                    <td><small><?= sanitize(substr($r['phenotype_normal']??'',0,20)) ?></small></td>
                    <td><small><?= sanitize(substr($r['phenotype_het']??'',0,20)) ?></small></td>
                    <td><small><?= sanitize(substr($r['phenotype_risk']??'',0,20)) ?></small></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="rule_id" value="<?= $r['id'] ?>">
                            <?php if ($r['is_locked']): ?>
                            <button name="action" value="unlock" class="btn btn-warning btn-sm" title="Destravar"><i class="bi bi-lock-fill"></i></button>
                            <?php else: ?>
                            <button name="action" value="lock" class="btn btn-outline-warning btn-sm" title="Travar"><i class="bi bi-unlock"></i></button>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td>
                        <a href="<?= baseUrl('pages/genomic/manage.php?action=edit&rule_id='.$r['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <?php if (!$r['is_locked']): ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Remover esta regra?')">
                            <input type="hidden" name="rule_id" value="<?= $r['id'] ?>">
                            <button name="action" value="delete" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
