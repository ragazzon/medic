<?php
$pageTitle = 'Ancestralidade';
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

// Buscar ancestralidade existente
$ancestry = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM patient_ancestry WHERE patient_id = ?");
    $stmt->execute([$patientId]);
    $ancestry = $stmt->fetch();
} catch (Exception $e) {
    // Tabela pode não existir ainda
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $data = [
        'patient_id' => $patientId,
        'ancestry_source' => trim($_POST['ancestry_source'] ?? ''),
        'european_pct' => floatval($_POST['european_pct'] ?? 0),
        'african_pct' => floatval($_POST['african_pct'] ?? 0),
        'east_asian_pct' => floatval($_POST['east_asian_pct'] ?? 0),
        'south_asian_pct' => floatval($_POST['south_asian_pct'] ?? 0),
        'native_american_pct' => floatval($_POST['native_american_pct'] ?? 0),
        'middle_eastern_pct' => floatval($_POST['middle_eastern_pct'] ?? 0),
        'oceanian_pct' => floatval($_POST['oceanian_pct'] ?? 0),
        'other_pct' => floatval($_POST['other_pct'] ?? 0),
        'primary_population' => trim($_POST['primary_population'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    try {
        if ($ancestry) {
            // Update
            $sql = "UPDATE patient_ancestry SET 
                    ancestry_source=?, european_pct=?, african_pct=?, east_asian_pct=?, 
                    south_asian_pct=?, native_american_pct=?, middle_eastern_pct=?, 
                    oceanian_pct=?, other_pct=?, primary_population=?, notes=?
                    WHERE patient_id=?";
            $pdo->prepare($sql)->execute([
                $data['ancestry_source'], $data['european_pct'], $data['african_pct'],
                $data['east_asian_pct'], $data['south_asian_pct'], $data['native_american_pct'],
                $data['middle_eastern_pct'], $data['oceanian_pct'], $data['other_pct'],
                $data['primary_population'], $data['notes'], $patientId
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO patient_ancestry 
                    (patient_id, ancestry_source, european_pct, african_pct, east_asian_pct, 
                     south_asian_pct, native_american_pct, middle_eastern_pct, oceanian_pct, 
                     other_pct, primary_population, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([
                $patientId, $data['ancestry_source'], $data['european_pct'], $data['african_pct'],
                $data['east_asian_pct'], $data['south_asian_pct'], $data['native_american_pct'],
                $data['middle_eastern_pct'], $data['oceanian_pct'], $data['other_pct'],
                $data['primary_population'], $data['notes']
            ]);
        }
        setFlash('success', 'Ancestralidade atualizada com sucesso!');
        redirect(baseUrl('pages/genomic/ancestry.php?patient_id=' . $patientId));
    } catch (Exception $e) {
        setFlash('danger', 'Erro ao salvar: ' . $e->getMessage());
    }
}

$d = $ancestry ?? [];
$pageTitle = 'Ancestralidade — ' . $patient['name'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-globe-americas me-2"></i>Ancestralidade — <?= sanitize($patient['name']) ?></h1>
    <div>
        <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Dashboard Genético</a>
    </div>
</div>

<div class="row g-4">
    <!-- Gráfico / Visualização -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Composição Ancestral
            </div>
            <div class="card-body">
                <?php if ($ancestry): ?>
                <canvas id="ancestryChart" height="250"></canvas>
                <div class="mt-3 text-center">
                    <?php if ($ancestry['primary_population']): ?>
                    <div class="badge bg-primary fs-6 px-3 py-2">
                        <i class="bi bi-geo-alt me-1"></i>População predominante: <?= sanitize($ancestry['primary_population']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($ancestry['ancestry_source']): ?>
                    <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Fonte: <?= sanitize($ancestry['ancestry_source']) ?></div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-globe-americas text-muted" style="font-size:4rem;"></i>
                    <h5 class="mt-3 text-muted">Ancestralidade não informada</h5>
                    <p class="text-muted">Preencha o formulário ao lado para registrar a composição ancestral do paciente.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($ancestry): ?>
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Por que a ancestralidade importa?
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">A frequência de variantes genéticas varia entre populações. Isso significa que:</p>
                <ul class="small text-muted mb-0">
                    <li>Um resultado considerado "normal" em uma população pode ser "raro" em outra</li>
                    <li>Os riscos genéticos podem ser maiores ou menores dependendo da ancestralidade</li>
                    <li>As doses de medicamentos recomendadas podem variar entre populações</li>
                    <li>A interpretação correta depende de saber a qual população o paciente pertence geneticamente</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Formulário -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2"></i>Dados de Ancestralidade
            </div>
            <div class="card-body">
                <?php if (isAdmin()): ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fonte dos dados</label>
                            <select name="ancestry_source" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="Genera" <?= ($d['ancestry_source'] ?? '') === 'Genera' ? 'selected' : '' ?>>Genera</option>
                                <option value="23andMe" <?= ($d['ancestry_source'] ?? '') === '23andMe' ? 'selected' : '' ?>>23andMe</option>
                                <option value="AncestryDNA" <?= ($d['ancestry_source'] ?? '') === 'AncestryDNA' ? 'selected' : '' ?>>AncestryDNA</option>
                                <option value="MyHeritage" <?= ($d['ancestry_source'] ?? '') === 'MyHeritage' ? 'selected' : '' ?>>MyHeritage</option>
                                <option value="Informado pelo paciente" <?= ($d['ancestry_source'] ?? '') === 'Informado pelo paciente' ? 'selected' : '' ?>>Informado pelo paciente</option>
                                <option value="Outro" <?= ($d['ancestry_source'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">População predominante (para análise)</label>
                            <select name="primary_population" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="Europeia" <?= ($d['primary_population'] ?? '') === 'Europeia' ? 'selected' : '' ?>>Europeia</option>
                                <option value="Africana" <?= ($d['primary_population'] ?? '') === 'Africana' ? 'selected' : '' ?>>Africana</option>
                                <option value="Leste Asiática" <?= ($d['primary_population'] ?? '') === 'Leste Asiática' ? 'selected' : '' ?>>Leste Asiática</option>
                                <option value="Sul Asiática" <?= ($d['primary_population'] ?? '') === 'Sul Asiática' ? 'selected' : '' ?>>Sul Asiática</option>
                                <option value="Nativa Americana" <?= ($d['primary_population'] ?? '') === 'Nativa Americana' ? 'selected' : '' ?>>Nativa Americana</option>
                                <option value="Oriente Médio" <?= ($d['primary_population'] ?? '') === 'Oriente Médio' ? 'selected' : '' ?>>Oriente Médio</option>
                                <option value="Mista (Latino-americana)" <?= ($d['primary_population'] ?? '') === 'Mista (Latino-americana)' ? 'selected' : '' ?>>Mista (Latino-americana)</option>
                                <option value="Global / Mista" <?= ($d['primary_population'] ?? '') === 'Global / Mista' ? 'selected' : '' ?>>Global / Mista</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr>
                            <label class="form-label fw-semibold">Composição Ancestral (%)</label>
                            <small class="text-muted d-block mb-2">Preencha as porcentagens conforme o resultado do teste genético.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Europeu</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="european_pct" class="form-control" value="<?= $d['european_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Africano</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="african_pct" class="form-control" value="<?= $d['african_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Leste Asiático</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="east_asian_pct" class="form-control" value="<?= $d['east_asian_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Sul Asiático</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="south_asian_pct" class="form-control" value="<?= $d['south_asian_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Nativo Americano</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="native_american_pct" class="form-control" value="<?= $d['native_american_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Oriente Médio</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="middle_eastern_pct" class="form-control" value="<?= $d['middle_eastern_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Oceania</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="oceanian_pct" class="form-control" value="<?= $d['oceanian_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Outros</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="other_pct" class="form-control" value="<?= $d['other_pct'] ?? 0 ?>" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                            <label class="form-label">Observações</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Informações adicionais sobre a ancestralidade..."><?= sanitize($d['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                        <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
                <?php else: ?>
                <!-- Visualização para não-admin -->
                <?php if ($ancestry): ?>
                <div class="row g-3">
                    <div class="col-md-6"><strong>Fonte:</strong> <?= sanitize($ancestry['ancestry_source'] ?? 'Não informado') ?></div>
                    <div class="col-md-6"><strong>População:</strong> <?= sanitize($ancestry['primary_population'] ?? 'Não definida') ?></div>
                    <?php if ($ancestry['notes']): ?>
                    <div class="col-12"><strong>Observações:</strong> <?= nl2br(sanitize($ancestry['notes'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="text-muted">Ancestralidade não informada. Solicite ao administrador.</p>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($ancestry): ?>
<!-- Gráfico de ancestralidade -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('ancestryChart');
    if (ctx) {
        var data = [
            <?= floatval($ancestry['european_pct'] ?? 0) ?>,
            <?= floatval($ancestry['african_pct'] ?? 0) ?>,
            <?= floatval($ancestry['east_asian_pct'] ?? 0) ?>,
            <?= floatval($ancestry['south_asian_pct'] ?? 0) ?>,
            <?= floatval($ancestry['native_american_pct'] ?? 0) ?>,
            <?= floatval($ancestry['middle_eastern_pct'] ?? 0) ?>,
            <?= floatval($ancestry['oceanian_pct'] ?? 0) ?>,
            <?= floatval($ancestry['other_pct'] ?? 0) ?>
        ];
        var labels = ['Europeu', 'Africano', 'Leste Asiático', 'Sul Asiático', 'Nativo Americano', 'Oriente Médio', 'Oceania', 'Outros'];
        var colors = ['#667eea', '#38ef7d', '#f2c94c', '#eb5757', '#bb6bd9', '#6dd5ed', '#20c997', '#6c757d'];

        // Filtrar apenas valores > 0
        var filtered = [];
        var filteredLabels = [];
        var filteredColors = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] > 0) {
                filtered.push(data[i]);
                filteredLabels.push(labels[i] + ' (' + data[i] + '%)');
                filteredColors.push(colors[i]);
            }
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: filteredLabels,
                datasets: [{ data: filtered, backgroundColor: filteredColors }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
            }
        });
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
