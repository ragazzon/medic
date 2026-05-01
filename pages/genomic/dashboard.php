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
$medPanels = [];
$riskPanels = [];
$medCodes = ['pharmaco', 'neuro'];
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
        <a href="<?= baseUrl('pages/genomic/report.php?patient_id=' . $patientId) ?>" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-medical me-1"></i>Relatório Médico</a>
        <a href="<?= baseUrl('pages/genomic/documents.php?patient_id=' . $patientId) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-folder2-open me-1"></i>Documentos</a>
        <a href="<?= baseUrl('pages/genomic/argue.php?patient_id=' . $patientId) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-chat-left-quote me-1"></i>Argumente com o Médico</a>
        <a href="<?= baseUrl('pages/genomic/ancestry.php?patient_id=' . $patientId) ?>" class="btn btn-outline-info btn-sm"><i class="bi bi-globe-americas me-1"></i>Ancestralidade</a>
        <a href="<?= baseUrl('pages/genomic/upload.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-upload me-1"></i>Re-importar</a>
    </div>
</div>

<?php if ($summary['import']): ?>
<div class="alert alert-light border mb-3">
    <i class="bi bi-info-circle me-1"></i>
    <strong><?= sanitize($summary['import']['file_name']) ?></strong> |
    <?= number_format($summary['import']['imported_snps'], 0, ',', '.') ?> SNPs |
    <?= $summary['import']['genome_build'] ?? '' ?> |
    <?= formatDateTime($summary['import']['imported_at']) ?>
</div>
<?php endif; ?>

<!-- ===== NAVEGAÇÃO POR BOTÕES ===== -->
<div class="btn-group w-100 mb-4" role="group">
    <button type="button" class="btn btn-outline-primary active" onclick="showSection('overview')" id="btn-overview">
        <i class="bi bi-speedometer2 me-1"></i>Visão Geral
    </button>
    <button type="button" class="btn btn-outline-primary" onclick="showSection('medications')" id="btn-medications">
        <i class="bi bi-capsule me-1"></i>Medicamentos
    </button>
    <button type="button" class="btn btn-outline-primary" onclick="showSection('risks')" id="btn-risks">
        <i class="bi bi-shield-exclamation me-1"></i>Riscos Genéticos
    </button>
    <button type="button" class="btn btn-outline-primary" onclick="showSection('panels')" id="btn-panels">
        <i class="bi bi-grid-3x3 me-1"></i>Análise por Gene
    </button>
</div>

<!-- ===== SEÇÃO: VISÃO GERAL ===== -->
<div id="section-overview" class="dashboard-section">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Visão Geral — Achados Principais</h5>
        </div>
        <div class="card-body">
<?php
// NOTA: A seção abaixo contém dados ESPECÍFICOS do Eric (patient_id=1).
// Para outros pacientes, mostramos uma visão geral genérica baseada nos dados do sistema.
$isEric = ($patientId == 1); // Eric é o primeiro paciente cadastrado
if (!$isEric):
?>
            <p class="text-muted mb-3">Resumo dos achados farmacogenéticos. Para detalhes completos, acesse o <a href="<?= baseUrl('pages/genomic/report.php?patient_id=' . $patientId) ?>">Relatório Médico</a> ou clique na aba <strong>Medicamentos</strong>.</p>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Dados genéticos importados com sucesso!</strong><br>
                Use as abas acima para navegar entre:<br>
                • <strong>Medicamentos</strong> — como o corpo processa cada remédio<br>
                • <strong>Riscos Genéticos</strong> — predisposições identificadas<br>
                • <strong>Análise por Gene</strong> — detalhes de cada gene/enzima
            </div>
            
            <div class="row g-3">
                <?php foreach ($drugsByClass as $className => $classDrugs): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body py-2">
                            <h6 class="mb-1"><i class="bi bi-tag me-1 text-primary"></i><?= sanitize($className) ?></h6>
                            <small class="text-muted"><?= count($classDrugs) ?> medicamento(s) analisado(s)</small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="<?= baseUrl('pages/genomic/report.php?patient_id=' . $patientId) ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-medical me-1"></i>Ver Relatório Completo para Médicos
                </a>
            </div>
<?php else: ?>
            <p class="text-muted mb-3">Resumo dos achados farmacogenéticos mais importantes. Para detalhes completos, acesse o <a href="<?= baseUrl('pages/genomic/report.php?patient_id=' . $patientId) ?>">Relatório Médico</a>.</p>
            
            <div class="row g-3">
                <!-- Alertas Críticos -->
                <div class="col-md-6">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white py-2">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i>Alertas Críticos</strong>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">🔴 <strong>CYP2D6 NÃO TESTADO</strong> — afeta 40+ medicamentos</li>
                                <li class="mb-2">🔴 <strong>VKORC1 TT</strong> — muito sensível à varfarina</li>
                                <li class="mb-2">🔴 <strong>SLCO1B1 TC</strong> — risco miopatia com sinvastatina</li>
                                <li class="mb-2">🔴 <strong>RARG GA</strong> — cardiotoxicidade com antraciclinas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Atenções -->
                <div class="col-md-6">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark py-2">
                            <strong><i class="bi bi-exclamation-circle me-1"></i>Atenções Importantes</strong>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">🟡 <strong>CYP2C19 *17</strong> — omeprazol/ISRS eficácia reduzida</li>
                                <li class="mb-2">🟡 <strong>COMT AG</strong> — opioides dose ~20% maior</li>
                                <li class="mb-2">🟡 <strong>CYP1A2 CA</strong> — melatonina efeito curto</li>
                                <li class="mb-2">🟡 <strong>HTR1A CG</strong> — ISRS resposta -30%</li>
                                <li class="mb-2">🟡 <strong>ADRB2 GA</strong> — salbutamol resposta intermediária</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Para Cirurgia -->
                <div class="col-md-6">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white py-2">
                            <strong><i class="bi bi-hospital me-1"></i>Para Cirurgia — USAR</strong>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1">✅ Remifentanila / Fentanil / Sufentanila</li>
                                <li class="mb-1">✅ Ropivacaína / Bupivacaína / Mepivacaína</li>
                                <li class="mb-1">✅ Paracetamol + Oxicodona/Morfina</li>
                                <li class="mb-1">✅ Ondansetrona (antiemético)</li>
                                <li class="mb-1">✅ Rabeprazol (protetor gástrico)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Para Cirurgia - EVITAR -->
                <div class="col-md-6">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-light text-danger py-2">
                            <strong><i class="bi bi-x-circle me-1"></i>Para Cirurgia — EVITAR</strong>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1">❌ Tramadol / Codeína (CYP2D6 desconhecido)</li>
                                <li class="mb-1">❌ Omeprazol (CYP2C19 rápido)</li>
                                <li class="mb-1">❌ Varfarina (VKORC1 TT)</li>
                                <li class="mb-1">❌ Meperidina (neurotóxica)</li>
                                <li class="mb-1">⚠️ Opioides: dose ~20% maior (COMT AG)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investigação Prioritária -->
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white py-2">
                        <strong><i class="bi bi-search me-1"></i>Investigação Prioritária (riscos genéticos + sintomas)</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="small mb-1"><strong>🔴 Lactose:</strong> CONFIRMADA geneticamente (consome leite + dor abdominal)</p>
                                <p class="small mb-1"><strong>🔴 Celíaca:</strong> HLA-DQ8+ (come massa + baixo peso + dor)</p>
                                <p class="small mb-1"><strong>🟡 Crohn/DII:</strong> ATG16L1 AA + IL23R (perfil intestinal)</p>
                                <p class="small mb-0"><strong>🟡 B12 funcional:</strong> MTRR het + B12 sérica ALTA (pode não estar funcionando)</p>
                            </div>
                            <div class="col-md-6">
                                <p class="small mb-1">→ Anti-transglutaminase IgA + IgA total</p>
                                <p class="small mb-1">→ Calprotectina fecal</p>
                                <p class="small mb-1">→ Teste de lactose (suspender 2 semanas)</p>
                                <p class="small mb-1">→ Hemograma + ferritina + folato</p>
                                <p class="small mb-1">→ Ácido metilmalônico (B12 funcional)</p>
                                <p class="small mb-1">→ Homocisteína (ciclo folato/B12)</p>
                                <p class="small mb-1">→ Holotranscobalamina (B12 ativa)</p>
                                <p class="small mb-1">→ <strong>Perfil lipídico completo</strong> (colesterol total, LDL, HDL, triglicerídeos)</p>
                                <p class="small mb-0">→ <strong>Painel genético hipercolesterolemia familiar</strong> (LDLR + APOB + PCSK9)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="<?= baseUrl('pages/genomic/report.php?patient_id=' . $patientId) ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-medical me-1"></i>Ver Relatório Completo para Médicos
                </a>
            </div>
<?php endif; // fim do if ($isEric) ?>
        </div>
    </div>
</div>

<!-- ===== SEÇÃO: MEDICAMENTOS ===== -->
<div id="section-medications" class="dashboard-section" style="display:none;">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-capsule me-2"></i>Análise de Medicamentos por Categoria</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Como o organismo metaboliza cada medicamento com base no perfil genético. Clique em um medicamento para ver detalhes.</p>

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
        </div>
    </div>
</div>

<!-- ===== SEÇÃO: RISCOS GENÉTICOS ===== -->
<div id="section-risks" class="dashboard-section" style="display:none;">
    <!-- Riscos ALTOS (ação necessária) -->
    <div class="card mb-4 border-start border-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Riscos que Precisam de Investigação</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <h6 class="text-danger">🔴 Intolerância à Lactose — CONFIRMADA</h6>
                            <p class="small mb-1">Gene MCM6/LCT: GG = sem persistência da lactase</p>
                            <p class="small mb-0"><strong>Ação:</strong> Suspender lactose 2 semanas como teste</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <h6 class="text-danger">🔴 Doença Celíaca — HLA-DQ8 positivo</h6>
                            <p class="small mb-1">Come massa + dor abdominal + baixo peso</p>
                            <p class="small mb-0"><strong>Ação:</strong> Anti-transglutaminase IgA URGENTE</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riscos MODERADOS (monitorar) -->
    <div class="card mb-4 border-start border-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Riscos que Merecem Monitoramento</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light"><tr><th>Condição</th><th>Evidência</th><th>Contexto</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Doença de Crohn</strong></td><td>Gene de autofagia intestinal alterado (2x risco)</td><td>Tem sintomas intestinais agora</td></tr>
                        <tr><td><strong>Psoríase / Artrite Psoriásica</strong></td><td>Gene do sistema imune alterado</td><td>Pai tem artrite psoriásica!</td></tr>
                        <tr><td><strong>Asma</strong></td><td>Genes de asma + resposta ao salbutamol intermediária</td><td>Avó materna tem asma</td></tr>
                        <tr><td><strong>Colite Ulcerativa</strong></td><td>Risco poligênico aumentado</td><td>Perfil autoimune/inflamatório</td></tr>
                        <tr><td><strong>Fibrilação Atrial</strong></td><td>Risco poligênico 20,55%</td><td>Doença de idosos (monitorar no futuro)</td></tr>
                        <tr><td><strong>Melanoma (câncer de pele)</strong></td><td>Gene de pele clara confirmado</td><td>Avô teve câncer de pele!</td></tr>
                        <tr><td><strong>Doença Coronariana</strong></td><td>Genética protetora, MAS...</td><td>Avô teve infarto + avó morreu de AVC</td></tr>
                        <tr><td><strong>Diabetes Tipo 1</strong></td><td>Genes de regulação imune alterados</td><td>Tio tem diabetes tipo 1</td></tr>
                        <tr><td><strong>Carcinoma Basocelular</strong></td><td>Risco padrão pela genética</td><td>Avô teve câncer de pele + pele muito clara</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riscos BAIXOS/Normais -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Riscos Normais ou Reduzidos (sem preocupação)</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Trombofilia (F5 + F2 normais)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Hemocromatose (normal)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Obesidade (FTO + MC4R normais)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Diabetes Tipo 2 (reduzido)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Alzheimer (reduzido)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Dupuytren (reduzido)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Câncer Próstata (reduzido)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Câncer Hereditário (normais)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Linfoma Hodgkin (padrão)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> LLC (padrão)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Gota (padrão)</div>
                <div class="col-md-4"><span class="badge bg-success me-1">✅</span> Câncer Bexiga (padrão)</div>
            </div>
        </div>
    </div>

    <!-- Painéis do sistema (legado) -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-grid me-2"></i>Painéis Detalhados por Área</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach ($riskPanels as $pnl): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?= baseUrl('pages/genomic/panel.php?patient_id=' . $patientId . '&panel=' . $pnl['code']) ?>" class="text-decoration-none">
                        <div class="card h-100 drug-card-link">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi <?= $pnl['icon'] ?> me-2" style="color:<?= $pnl['color'] ?>"></i>
                                    <span><?= sanitize($pnl['name']) ?></span>
                                </div>
                                <div class="d-flex gap-1 mt-1">
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
</div>

<!-- ===== SEÇÃO: ANÁLISE POR GENE ===== -->
<div id="section-panels" class="dashboard-section" style="display:none;">
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-grid-3x3 me-2"></i>Análise por Gene (Enzimas e Transportadores)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                Esta seção mostra a análise <strong>gene a gene</strong> — como cada enzima/transportador do organismo está funcionando. 
                Diferente da aba "Medicamentos" (que olha para cada remédio), aqui olhamos para cada <strong>gene</strong> e entendemos seu impacto global.
            </p>
            
            <div class="row g-3">
                <?php foreach ($medPanels as $pnl): ?>
                <div class="col-md-6">
                    <a href="<?= baseUrl('pages/genomic/panel.php?patient_id=' . $patientId . '&panel=' . $pnl['code']) ?>" class="text-decoration-none">
                        <div class="card h-100 drug-card-link">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi <?= $pnl['icon'] ?> me-2 fs-4" style="color:<?= $pnl['color'] ?>"></i>
                                    <div>
                                        <h6 class="mb-0"><?= sanitize($pnl['name']) ?></h6>
                                        <small class="text-muted">Análise detalhada de genes e variantes</small>
                                    </div>
                                </div>
                                <p class="text-muted small mb-2">
                                    <?php if ($pnl['code'] === 'pharmaco'): ?>
                                        Enzimas CYP450 (CYP2D6, CYP2C19, CYP3A4, CYP2C9, etc.), transportadores (SLCO1B1, ABCB1) e alvos (VKORC1, DPYD, TPMT). Determina como o corpo processa medicamentos.
                                    <?php elseif ($pnl['code'] === 'neuro'): ?>
                                        Genes relacionados a neurotransmissores (COMT, HTR1A, HTR2A, BDNF, FKBP5, DRD2). Influencia resposta a psicofármacos, dor e humor.
                                    <?php else: ?>
                                        <?= sanitize($pnl['description'] ?? '') ?>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <?php if ($pnl['risk_count']): ?><span class="badge bg-danger"><?= $pnl['risk_count'] ?> alterado</span><?php endif; ?>
                                    <?php if ($pnl['attention_count']): ?><span class="badge bg-warning text-dark"><?= $pnl['attention_count'] ?> atenção</span><?php endif; ?>
                                    <span class="badge bg-success"><?= $pnl['normal_count'] ?> normal</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>

                <?php foreach ($riskPanels as $pnl): ?>
                <div class="col-md-6">
                    <a href="<?= baseUrl('pages/genomic/panel.php?patient_id=' . $patientId . '&panel=' . $pnl['code']) ?>" class="text-decoration-none">
                        <div class="card h-100 drug-card-link">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi <?= $pnl['icon'] ?> me-2 fs-4" style="color:<?= $pnl['color'] ?>"></i>
                                    <div>
                                        <h6 class="mb-0"><?= sanitize($pnl['name']) ?></h6>
                                        <small class="text-muted">Variantes genéticas de risco/predisposição</small>
                                    </div>
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
</div>

<!-- JavaScript para navegação -->
<script>
function showSection(section) {
    // Esconder todas as seções
    document.querySelectorAll('.dashboard-section').forEach(el => el.style.display = 'none');
    // Mostrar a seção selecionada
    document.getElementById('section-' + section).style.display = 'block';
    // Atualizar botões
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btn-' + section).classList.add('active');
    // Salvar no localStorage
    localStorage.setItem('genomic_dashboard_tab', section);
}

// Sempre começar na Visão Geral ao abrir o dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Não restaurar última aba - sempre começa em "overview"
    showSection('overview');
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
