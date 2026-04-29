<?php
$pageTitle = 'Relatório para Médicos';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();

$patientId = intval($_GET['patient_id'] ?? 0);
if (!$patientId || !canAccessPatient($patientId)) {
    redirect(baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId));
}

$pdo = getConnection();
$patient = $pdo->prepare('SELECT * FROM patients WHERE id=?');
$patient->execute([$patientId]);
$patient = $patient->fetch();

$pageTitle = 'Relatório Médico — ' . $patient['name'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-file-earmark-medical me-2"></i>Relatório para Médicos — <?= sanitize($patient['name']) ?></h1>
    <div>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <a href="<?= baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
    </div>
</div>

<!-- Informações do paciente -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Paciente:</strong> <?= sanitize($patient['name']) ?><br>
                <strong>Data de Nascimento:</strong> <?= formatDate($patient['birth_date']) ?><br>
                <strong>Ancestralidade:</strong> Europeia 92% (Ibérica 51%, Italiana 13%)
            </div>
            <div class="col-md-6">
                <strong>Fonte dos dados:</strong> Genera (chip GSA v3.0 / GRCh37)<br>
                <strong>Data do relatório:</strong> <?= date('d/m/Y') ?><br>
                <strong>Grau de confiança geral:</strong> <span class="badge bg-success">Alto</span> (baseado em CPIC/DPWG)
            </div>
        </div>
    </div>
</div>

<!-- SEÇÃO 1: ACHADOS COM IMPACTO CLÍNICO -->
<div class="card mb-4 border-start border-4 border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Achados com Impacto Clínico Direto</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Gene/SNP</th><th>Genótipo</th><th>Impacto</th><th>Medicamentos Afetados</th><th>Confiança</th></tr>
                </thead>
                <tbody>
                    <tr class="table-danger">
                        <td><strong>VKORC1</strong> rs9923231</td>
                        <td><code>TT</code></td>
                        <td>Muito sensível à varfarina (dose -25-50%)</td>
                        <td>Varfarina → Preferir DOACs (rivaroxabana, apixabana)</td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>SLCO1B1</strong> rs4149056</td>
                        <td><code>TC</code></td>
                        <td>Risco de miopatia 4.5x com sinvastatina</td>
                        <td>Sinvastatina max 20mg → Preferir rosuvastatina/pravastatina</td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP2C19</strong> rs12248560</td>
                        <td><code>CT (*1/*17)</code></td>
                        <td>Metabolizador rápido — eliminação acelerada</td>
                        <td>Omeprazol (-eficácia), Sertralina (-30-40%), Escitalopram, Clopidogrel, Voriconazol</td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP3A5</strong> rs776746</td>
                        <td><code>CT (*1/*3)</code></td>
                        <td>Expressor parcial — metabolismo aumentado</td>
                        <td>Tacrolimo (dose 1.5x), Midazolam (duração menor)</td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>COMT</strong> rs4680</td>
                        <td><code>AG (Val/Met)</code></td>
                        <td>Resposta intermediária a opioides</td>
                        <td>Morfina, Oxicodona — pode necessitar dose ~20% maior</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>HTR1A</strong> rs6295</td>
                        <td><code>CG</code></td>
                        <td>Resposta a ISRS reduzida ~30%</td>
                        <td>Sertralina, Paroxetina, Fluoxetina — considerar IRSN</td>
                        <td><span class="badge bg-warning text-dark">2B</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP1A2</strong> rs762551</td>
                        <td><code>CA (*1/*1F)</code></td>
                        <td>Metabolizador ultra-rápido (induzível)</td>
                        <td>Melatonina (efeito curto), Olanzapina, Rasagilina</td>
                        <td><span class="badge bg-warning text-dark">2B</span></td>
                    </tr>
                    <tr>
                        <td><strong>ADRB2</strong> rs1042713</td>
                        <td><code>GA (Arg/Gly)</code></td>
                        <td>Resposta intermediária a beta-2 agonistas</td>
                        <td>Salbutamol, Salmeterol — monitorar controle asma</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SEÇÃO 2: GENES FALTANTES -->
<div class="card mb-4 border-start border-4 border-info">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-puzzle me-2"></i>Genes NÃO Cobertos pelo Chip (Exame Adicional Recomendado)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            O chip GSA v3.0 da Genera cobre ~700.000 SNPs mas <strong>não inclui o CYP2D6*4</strong> (rs3892097), que é o gene farmacogenético MAIS importante para medicamentos psiquiátricos, analgésicos e cardiológicos.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-danger">
                    <tr><th>Gene/SNP Faltante</th><th>Importância</th><th>Medicamentos Afetados (>30)</th><th>Exame Necessário</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>CYP2D6</strong><br><small>rs3892097, rs16947, rs1065852 + CNV</small></td>
                        <td><span class="badge bg-danger">CRÍTICO</span></td>
                        <td>Tramadol, Codeína, Tamoxifeno, Risperidona, Venlafaxina, Metoprolol, Atomoxetina, Ondansetrona, Vortioxetina, Nortriptilina, Propafenona, Tioridazina + 20 outros</td>
                        <td><strong>Painel CYP2D6 completo com CNV</strong></td>
                    </tr>
                    <tr>
                        <td><strong>ABCB1</strong><br><small>rs1045642 (C3435T)</small></td>
                        <td><span class="badge bg-warning text-dark">ALTO</span></td>
                        <td>Vincristina, Venlafaxina, Digoxina, Fenitoína, Ciclosporina</td>
                        <td>Painel farmacogenético expandido</td>
                    </tr>
                    <tr>
                        <td><strong>CYP2B6</strong><br><small>rs3211371</small></td>
                        <td><span class="badge bg-secondary">MODERADO</span></td>
                        <td>Efavirenz, Nevirapina, Bupropiona, Metadona</td>
                        <td>Painel farmacogenético expandido</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card bg-light mt-3">
            <div class="card-body">
                <h6><i class="bi bi-clipboard-check me-2"></i>Sugestão para o Médico:</h6>
                <p class="mb-2">Solicitar <strong>Painel Farmacogenético com tipagem completa do CYP2D6</strong> (incluindo Copy Number Variation).</p>
                <p class="mb-2"><strong>Justificativa clínica:</strong> Paciente adolescente autista com múltiplas condições que necessita de cirurgia maxilar e pode precisar de medicamentos metabolizados pelo CYP2D6 (analgésicos, antieméticos, antidepressivos).</p>
                <p class="mb-0"><strong>Laboratórios disponíveis:</strong> Genera (painel PGx expandido), Mendelics, Fleury/Dasa, Hospital Israelita Albert Einstein.</p>
            </div>
        </div>
    </div>
</div>

<!-- SEÇÃO 3: RECOMENDAÇÕES PARA CIRURGIA -->
<div class="card mb-4 border-start border-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-hospital me-2"></i>Recomendações para Cirurgia Maxilar</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="text-success"><i class="bi bi-check-circle me-1"></i> USAR (perfil genético favorável):</h6>
                <ul>
                    <li><strong>Anestesia:</strong> Remifentanila / Sufentanila (OPRM1 AA, não CYP2D6)</li>
                    <li><strong>Anestésico local:</strong> Ropivacaína / Mepivacaína (G6PD normal)</li>
                    <li><strong>Analgesia pós-op:</strong> Paracetamol + Oxicodona ou Morfina</li>
                    <li><strong>Antiemético:</strong> Ondansetrona (menos CYP2D6-dependente)</li>
                    <li><strong>Protetor gástrico:</strong> Rabeprazol (menos CYP2C19-dependente)</li>
                    <li><strong>AINE (se necessário):</strong> Naproxeno ou Meloxicam (CYP2C9 normal)</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-danger"><i class="bi bi-x-circle me-1"></i> EVITAR:</h6>
                <ul>
                    <li><strong>Tramadol / Codeína</strong> — CYP2D6 desconhecido (pode ser ineficaz)</li>
                    <li><strong>Omeprazol</strong> — CYP2C19 rápido (eficácia reduzida)</li>
                    <li><strong>Varfarina</strong> — VKORC1 TT (muito sensível). Usar rivaroxabana</li>
                    <li><strong>Metoclopramida</strong> — CYP2D6 desconhecido. Usar ondansetrona</li>
                    <li><strong>Meperidina</strong> — Metabólito neurotóxico. Usar morfina</li>
                </ul>
            </div>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Atenção:</strong> COMT AG (Val/Met) + rs2952768 TC → Opioides podem necessitar dose ~20% maior para analgesia adequada. Midazolam pode ter duração menor (CYP3A5 expressor parcial). Titular e monitorar.
        </div>
    </div>
</div>

<!-- SEÇÃO 4: OBSERVAÇÕES E LIMITAÇÕES -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Observações e Limitações</h5>
    </div>
    <div class="card-body">
        <ul>
            <li><strong>CYP1A2 (*1F, rs762551 CA):</strong> Classificado como "ultra-rápido" mas tecnicamente é alelo de <em>alta indutibilidade</em>. O efeito é mais pronunciado em fumantes e com dieta rica em carnes grelhadas. Em não-fumantes, o efeito pode ser menos marcado. Confiança: 2B.</li>
            <li><strong>HTR1A (rs6295 CG):</strong> Associação com resposta reduzida a ISRS é estatisticamente significativa em meta-análises, mas não é certeza individual. Não deve contraindicar ISRS, apenas justificar monitoramento mais cuidadoso e dose potencialmente maior. Confiança: 2B.</li>
            <li><strong>rs2952768 (morfina/opioides):</strong> Achado de GWAS com evidência nível 3. É uma associação estatística populacional, não um mecanismo comprovado individualmente. Usar apenas como informação complementar, não para decisão clínica isolada.</li>
            <li><strong>DRD1 rs4532 e SLC6A2 rs5569 (metilfenidato):</strong> Evidência nível 3. Não contraindicam metilfenidato. Servem apenas para lembrar de monitorar resposta e considerar ajustes se necessário.</li>
            <li><strong>MTHFR/MTRR e metotrexato:</strong> MTHFR 677 é NORMAL (GG). O 1298 heterozigoto + MTRR AG sugerem comprometimento PARCIAL (não grave) do metabolismo do folato. Suplementação com folato é prudente mas não indica contraindicação ao metotrexato.</li>
            <li><strong>DPYD (rs3918290 CC, rs67376798 TT):</strong> Interpretados como NORMAIS. NOTA DE CAUTELA: A orientação de fita (strand) no chip Genera deve ser confirmada com o laudo oficial antes de usar fluoropirimidinas (5-FU, capecitabina, tegafur). Risco de toxicidade fatal se interpretação estiver invertida.</li>
        </ul>
        
        <div class="alert alert-secondary mt-3 mb-0">
            <small><strong>Disclaimer:</strong> Este relatório é baseado em dados genéticos extraídos do chip GSA v3.0 (Genera) e interpretados segundo guidelines CPIC e DPWG. Não substitui avaliação clínica. Decisões terapêuticas devem ser tomadas pelo médico considerando o quadro clínico completo.</small>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .sidebar-overlay, .page-header > div:last-child { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { break-inside: avoid; border: 1px solid #dee2e6 !important; }
    .content-wrapper { padding: 0 !important; }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
