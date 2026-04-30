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
    <h1><i class="bi bi-file-earmark-medical me-2"></i>Relatório Farmacogenético — <?= sanitize($patient['name']) ?></h1>
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
                <strong>Ancestralidade:</strong> Europeia 92% (Ibérica 51%, Italiana 13%, Bálcãs 9%, Ashkenazim 5%)
            </div>
            <div class="col-md-6">
                <strong>Fonte dos dados:</strong> Genera (chip GSA v3.0 / GRCh37)<br>
                <strong>Data do relatório:</strong> <?= date('d/m/Y') ?><br>
                <strong>Total de medicamentos analisados:</strong> <span class="badge bg-primary">232</span><br>
                <strong>Grau de confiança geral:</strong> <span class="badge bg-success">Alto</span> (CPIC/DPWG)
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
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Gene/SNP</th><th>Genótipo</th><th>Impacto</th><th>Medicamentos Afetados</th><th>Confiança</th></tr>
                </thead>
                <tbody>
                    <tr class="table-danger">
                        <td><strong>VKORC1</strong><br><small>rs9923231</small></td>
                        <td><code>TT</code></td>
                        <td>Muito sensível à varfarina/acenocumarol (dose -25-50%)</td>
                        <td>Varfarina, Acenocumarol, Femprocumona → <strong>Preferir DOACs (apixabana, rivaroxabana)</strong></td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>SLCO1B1</strong><br><small>rs4149056</small></td>
                        <td><code>TC</code></td>
                        <td>Risco de miopatia 4.5x com sinvastatina</td>
                        <td>Sinvastatina max 20mg, Lovastatina, Atorvastatina (doses altas) → <strong>Preferir rosuvastatina/pravastatina</strong></td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>RARG</strong><br><small>rs2229774</small></td>
                        <td><code>GA</code></td>
                        <td>Risco AUMENTADO de cardiotoxicidade com antraciclinas</td>
                        <td>Doxorrubicina, Daunorrubicina → Eco de base + monitoramento cardíaco + considerar dexrazoxano</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP2C19</strong><br><small>rs12248560</small></td>
                        <td><code>CT (*1/*17)</code></td>
                        <td>Metabolizador rápido — eliminação acelerada</td>
                        <td>Omeprazol/Lansoprazol/Esomeprazol (-eficácia), Sertralina/Escitalopram/Citalopram (-30-40%), Clobazam, Diazepam, Voriconazol. <strong>Favorável para: Clopidogrel (+eficácia)</strong></td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP3A5</strong><br><small>rs776746</small></td>
                        <td><code>CT (*1/*3)</code></td>
                        <td>Expressor parcial — metabolismo aumentado</td>
                        <td>Tacrolimo/Ciclosporina (dose 1.5x), Midazolam (duração menor), Maraviroque</td>
                        <td><span class="badge bg-success">1A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>COMT</strong><br><small>rs4680</small></td>
                        <td><code>AG (Val/Met)</code></td>
                        <td>Resposta intermediária a opioides + duração intermediária de levodopa</td>
                        <td>Morfina, Oxicodona, Fentanil — dose ~20% maior. THC: risco intermediário psicose</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>HTR1A</strong><br><small>rs6295</small></td>
                        <td><code>CG</code></td>
                        <td>Resposta a ISRS reduzida ~30%</td>
                        <td>Sertralina, Paroxetina, Fluoxetina, Escitalopram, Citalopram → <strong>Considerar IRSN (desvenlafaxina, duloxetina)</strong></td>
                        <td><span class="badge bg-warning text-dark">2B</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>CYP1A2</strong><br><small>rs762551</small></td>
                        <td><code>CA (*1/*1F)</code></td>
                        <td>Metabolizador ultra-rápido (induzível)</td>
                        <td>Melatonina (efeito curto), Olanzapina/Clozapina (dose maior), Agomelatina (eficácia reduzida), Flufenazina, Asenapina</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>ADRB2</strong><br><small>rs1042713</small></td>
                        <td><code>GA (Arg/Gly)</code></td>
                        <td>Resposta intermediária a beta-2 agonistas</td>
                        <td>Salbutamol, Salmeterol — monitorar controle asma. Confirma achado da Genera</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>NOS1AP</strong><br><small>rs12143842</small></td>
                        <td><code>CT</code></td>
                        <td>Risco moderado de prolongamento QTc</td>
                        <td>Amiodarona — monitorar ECG. Cautela com combinações QT-prolongadoras</td>
                        <td><span class="badge bg-warning text-dark">2A</span></td>
                    </tr>
                    <tr>
                        <td><strong>CYP4F2</strong><br><small>rs2108622</small></td>
                        <td><code>CT</code></td>
                        <td>Metabolismo intermediário da vitamina K</td>
                        <td>Contribui para sensibilidade a varfarina (junto com VKORC1)</td>
                        <td><span class="badge bg-warning text-dark">2B</span></td>
                    </tr>
                    <tr>
                        <td><strong>HTR2A</strong><br><small>rs6311</small></td>
                        <td><code>TT</code></td>
                        <td>Variante homozigoto — resposta variável a antidepressivos</td>
                        <td>Escitalopram, Citalopram — perfil complexo (ver ISRS abaixo)</td>
                        <td><span class="badge bg-warning text-dark">2B</span></td>
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
            O chip GSA v3.0 da Genera cobre ~700.000 SNPs mas <strong>não inclui o CYP2D6*4</strong> (rs3892097), que é o gene farmacogenético MAIS importante — afeta <strong>mais de 40 medicamentos</strong> na lista analisada.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-danger">
                    <tr><th>Gene/SNP Faltante</th><th>Importância</th><th>Medicamentos Afetados</th><th>Exame Necessário</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>CYP2D6</strong><br><small>rs3892097, rs16947, rs1065852 + CNV</small></td>
                        <td><span class="badge bg-danger">CRÍTICO</span></td>
                        <td>Tramadol, Codeína, Tamoxifeno, Risperidona, Venlafaxina, Metoprolol, Atomoxetina, Ondansetrona, Vortioxetina, Aripiprazol, Brexpiprazol, Haloperidol, Nortriptilina, Propafenona, Tioridazina, Flecainida, Duloxetina, Fluoxetina, Amitriptilina, Amoxapina + 20 outros</td>
                        <td><strong>Painel CYP2D6 completo com CNV</strong></td>
                    </tr>
                    <tr>
                        <td><strong>ABCB1</strong><br><small>rs1045642 (C3435T)</small></td>
                        <td><span class="badge bg-warning text-dark">ALTO</span></td>
                        <td>Vincristina, Venlafaxina, Digoxina, Fenitoína, Ciclosporina, Fenobarbital</td>
                        <td>Painel farmacogenético expandido</td>
                    </tr>
                    <tr>
                        <td><strong>CYP2B6</strong><br><small>rs3211371</small></td>
                        <td><span class="badge bg-secondary">MODERADO</span></td>
                        <td>Efavirenz, Nevirapina, Bupropiona, Metadona, Cetamina</td>
                        <td>Painel farmacogenético expandido</td>
                    </tr>
                    <tr>
                        <td><strong>TPMT *3C</strong><br><small>rs1142345</small></td>
                        <td><span class="badge bg-secondary">MODERADO</span></td>
                        <td>Azatioprina, Mercaptopurina, Tioguanina (TPMT *3B e *2 estão normais)</td>
                        <td>Dosagem de atividade enzimática TPMT</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card bg-light mt-3">
            <div class="card-body">
                <h6><i class="bi bi-clipboard-check me-2"></i>Sugestão para o Médico:</h6>
                <p class="mb-2">Solicitar <strong>Painel Farmacogenético com tipagem completa do CYP2D6</strong> (incluindo Copy Number Variation).</p>
                <p class="mb-2"><strong>Justificativa clínica:</strong> Paciente adolescente autista com múltiplas condições que necessita de cirurgia maxilar e utiliza/pode necessitar medicamentos metabolizados pelo CYP2D6 (analgésicos, antieméticos, antidepressivos, antipsicóticos).</p>
                <p class="mb-0"><strong>Laboratórios disponíveis:</strong> Genera (painel PGx expandido), Mendelics, Fleury/Dasa, Hospital Israelita Albert Einstein.</p>
            </div>
        </div>
    </div>
</div>

<!-- SEÇÃO 3: RECOMENDAÇÕES PARA CIRURGIA MAXILAR -->
<div class="card mb-4 border-start border-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-hospital me-2"></i>Recomendações para Cirurgia Maxilar</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="text-success"><i class="bi bi-check-circle me-1"></i> USAR (perfil genético favorável):</h6>
                <ul>
                    <li><strong>Anestesia geral:</strong> Remifentanila / Sufentanila / Fentanil / Alfentanila (OPRM1 AA, CYP3A4 normal, não dependem CYP2D6)</li>
                    <li><strong>Adjuvante:</strong> Cetamina (não depende CYPs problemáticos)</li>
                    <li><strong>Anestésico local:</strong> Ropivacaína / Mepivacaína / Bupivacaína / Lidocaína (G6PD normal)</li>
                    <li><strong>Analgesia pós-op:</strong> Paracetamol + Oxicodona ou Morfina (não dependem CYP2D6)</li>
                    <li><strong>AINE:</strong> Naproxeno, Meloxicam, Ibuprofeno, Diclofenaco (CYP2C9 normal)</li>
                    <li><strong>Antiemético:</strong> Ondansetrona (menos CYP2D6-dependente)</li>
                    <li><strong>Protetor gástrico:</strong> Rabeprazol (menos CYP2C19-dependente)</li>
                    <li><strong>Anticoagulação (se necessário):</strong> Rivaroxabana ou Apixabana (não VKORC1)</li>
                    <li><strong>Sedação pré-op:</strong> Lorazepam (não depende CYP - glucuronidação)</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-danger"><i class="bi bi-x-circle me-1"></i> EVITAR:</h6>
                <ul>
                    <li><strong>Tramadol / Codeína / Hidrocodona</strong> — CYP2D6 desconhecido (eficácia imprevisível)</li>
                    <li><strong>Omeprazol / Lansoprazol / Esomeprazol</strong> — CYP2C19 rápido (eficácia reduzida)</li>
                    <li><strong>Varfarina / Acenocumarol</strong> — VKORC1 TT (muito sensível)</li>
                    <li><strong>Metoclopramida</strong> — CYP2D6 desconhecido (usar ondansetrona)</li>
                    <li><strong>Meperidina</strong> — Metabólito neurotóxico (usar morfina/oxicodona)</li>
                    <li><strong>Midazolam em dose única</strong> — CYP3A5 CT = duração menor (considerar doses repetidas)</li>
                </ul>
                <h6 class="text-primary mt-3"><i class="bi bi-info-circle me-1"></i> ATENÇÃO ESPECIAL:</h6>
                <ul>
                    <li><strong>Opioides:</strong> COMT AG + rs2952768 TC → dose ~20% maior para analgesia</li>
                    <li><strong>Midazolam:</strong> CYP3A5 CT = duração menor (manutenção mais frequente)</li>
                    <li><strong>Se betabloqueador:</strong> Preferir atenolol (não CYP2D6) ao metoprolol</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- SEÇÃO 4: OPÇÕES TERAPÊUTICAS PARA AUTISMO/TDAH -->
<div class="card mb-4 border-start border-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-brain me-2"></i>Opções Terapêuticas para Autismo / TDAH</h5>
    </div>
    <div class="card-body">
        <h6>Antidepressivos (ranking por perfil farmacogenético):</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>#</th><th>Medicamento</th><th>Classe</th><th>Justificativa</th></tr>
                </thead>
                <tbody>
                    <tr class="table-success"><td>1º</td><td><strong>Desvenlafaxina</strong></td><td>IRSN</td><td>NÃO depende CYP2D6, FKBP5 favorável, menos afetado por HTR1A</td></tr>
                    <tr class="table-success"><td>2º</td><td><strong>Duloxetina</strong></td><td>IRSN</td><td>CYP2D6 parcial (CYP1A2 principal), IRSN menos HTR1A-dependente</td></tr>
                    <tr class="table-success"><td>3º</td><td><strong>Bupropiona</strong></td><td>IRND</td><td>NÃO depende CYP2D6/2C19. CI em epilepsia!</td></tr>
                    <tr><td>4º</td><td>Fluoxetina</td><td>ISRS</td><td>Menos CYP2C19-dependente que sertralina, mas HTR1A -30%</td></tr>
                    <tr><td>5º</td><td>Escitalopram</td><td>ISRS</td><td>GRIK4 TC favorável, mas CYP2C19 -30-40% + HTR1A -30%</td></tr>
                    <tr class="table-danger"><td>❌</td><td>Venlafaxina</td><td>IRSN</td><td>Depende do CYP2D6 (desconhecido!) — EVITAR</td></tr>
                </tbody>
            </table>
        </div>

        <h6 class="mt-3">TDAH:</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Medicamento</th><th>Depende CYP2D6?</th><th>Status</th><th>Observação</th></tr>
                </thead>
                <tbody>
                    <tr class="table-success"><td><strong>Guanfacina</strong></td><td>NÃO (CYP3A4)</td><td>✅</td><td>Não-estimulante. CYP3A4 normal. Boa para impulsividade</td></tr>
                    <tr><td>Metilfenidato/Lisdexanfetamina</td><td>NÃO (esterases/hidrólise)</td><td>⚠️</td><td>SLC6A2 GA + DRD1 TT = resposta variável. Monitorar</td></tr>
                    <tr><td>Modafinila</td><td>Parcial</td><td>✅</td><td>Impacto CYP2D6 limitado</td></tr>
                    <tr class="table-danger"><td>Atomoxetina</td><td><strong>SIM (5-10x!)</strong></td><td>❌</td><td>TESTE CYP2D6 OBRIGATÓRIO antes. Diferença de 5-10x</td></tr>
                </tbody>
            </table>
        </div>

        <h6 class="mt-3">Antipsicóticos (para irritabilidade/TEA):</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Medicamento</th><th>Status</th><th>Observação</th></tr>
                </thead>
                <tbody>
                    <tr class="table-success"><td><strong>Paliperidona</strong></td><td>✅</td><td>NÃO depende CYP2D6. MC4R TT + HTR2C CC favoráveis</td></tr>
                    <tr class="table-success"><td><strong>Lurasidona</strong></td><td>✅</td><td>CYP3A4 normal. Menor ganho de peso</td></tr>
                    <tr><td>Aripiprazol</td><td>⚠️</td><td>CYP2D6 N/D: dose baixa e titular. MC4R/HTR2C favoráveis</td></tr>
                    <tr><td>Quetiapina</td><td>✅</td><td>CYP3A4 normal. Útil para insônia. Ganho de peso moderado</td></tr>
                </tbody>
            </table>
        </div>

        <h6 class="mt-3">Ansiolíticos / Insônia:</h6>
        <ul>
            <li><strong>Lorazepam</strong> ✅ — NÃO depende CYP (glucuronidação). Preferencial</li>
            <li><strong>Buspirona</strong> ✅ — CYP3A4 normal</li>
            <li><strong>Canabidiol (CBD)</strong> ✅ — CYP3A4 normal, opção para ansiedade em TEA</li>
            <li><strong>Trazodona / Quetiapina baixa dose</strong> ✅ — Para insônia (melatonina tem efeito curto: CYP1A2 ultra-rápido)</li>
            <li>Diazepam ⚠️ — CYP2C19 rápido = duração menor</li>
            <li>Alprazolam ✅ — CYP3A4 normal</li>
        </ul>
    </div>
</div>

<!-- SEÇÃO 5: OBSERVAÇÕES E LIMITAÇÕES -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Observações e Limitações</h5>
    </div>
    <div class="card-body">
        <ul>
            <li><strong>CYP1A2 (*1F, rs762551 CA):</strong> Classificado como "ultra-rápido" mas tecnicamente é alelo de <em>alta indutibilidade</em>. O efeito é mais pronunciado em fumantes e com dieta rica em carnes grelhadas. Em não-fumantes adolescentes, o efeito pode ser menos marcado. Confiança: 2A.</li>
            <li><strong>HTR1A (rs6295 CG):</strong> Associação com resposta reduzida a ISRS em meta-análises. Não deve contraindicar ISRS, apenas justificar monitoramento e dose potencialmente maior ou troca para IRSN se sem resposta em 6-8 semanas. Confiança: 2B.</li>
            <li><strong>NOS1AP (rs12143842 CT):</strong> Risco moderado de prolongamento QTc. Relevante para amiodarona e combinações de medicamentos QT-prolongadores (antipsicóticos + macrolídeos, etc.). Confiança: 2A.</li>
            <li><strong>RARG (rs2229774 GA):</strong> Risco aumentado de cardiotoxicidade com antraciclinas. SLC28A3 GA pode oferecer proteção parcial. Se quimioterapia com antraciclinas for necessária, eco de base obrigatório. Confiança: 2A.</li>
            <li><strong>rs2952768 (morfina/opioides):</strong> Achado de GWAS com evidência nível 3. Associação estatística, não mecanismo comprovado. Usar apenas como informação complementar.</li>
            <li><strong>DRD1 rs4532 TT e SLC6A2 rs5569 GA (metilfenidato):</strong> Evidência nível 3. Não contraindicam metilfenidato. Monitorar resposta e considerar ajustes.</li>
            <li><strong>MTHFR/MTRR e metotrexato:</strong> MTHFR 677 NORMAL (GG). MTHFR 1298 heterozigoto (TG) + MTRR AG = comprometimento PARCIAL do metabolismo do folato. Suplementar com L-metilfolato se metotrexato for usado.</li>
            <li><strong>DPYD (rs3918290 CC, rs67376798 TT):</strong> Normais. CAUTELA: confirmar orientação de fita com laudo oficial antes de fluoropirimidinas (risco fatal).</li>
            <li><strong>TPMT:</strong> *3B e *2 normais. *3C (rs1142345) não testado mas prevalência &lt;0.2% em europeus. Probabilidade &gt;99% de TPMT funcional.</li>
            <li><strong>Genera vs CPIC para Opioides:</strong> A Genera classificou "menor resposta a opioides" usando COMT (rs4680 AG), não OPRM1. OPRM1 AA é normal. A classificação da Genera está correta pelo mecanismo da COMT.</li>
        </ul>
        
        <div class="alert alert-secondary mt-3 mb-0">
            <small><strong>Disclaimer:</strong> Este relatório é baseado em dados genéticos extraídos do chip GSA v3.0 (Genera) e interpretados segundo guidelines CPIC, DPWG e PharmGKB. Não substitui avaliação clínica. Decisões terapêuticas devem ser tomadas pelo médico considerando o quadro clínico completo. Análise de 232 medicamentos × 61 genes realizada em abril/2026.</small>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .sidebar-overlay, .page-header > div:last-child { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { break-inside: avoid; border: 1px solid #dee2e6 !important; }
    .content-wrapper { padding: 0 !important; }
    .badge { border: 1px solid #333 !important; }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>


