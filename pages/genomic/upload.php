<?php
$pageTitle = 'Upload Genético';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();
$patientId = intval($_GET['patient_id'] ?? 0);
if (!$patientId || !canAccessPatient($patientId)) {
    redirect(baseUrl('pages/patients/list.php'));
}
$pdo = getConnection();
$patient = $pdo->prepare("SELECT * FROM patients WHERE id=?");
$patient->execute([$patientId]);
$patient = $patient->fetch();
$imports = $pdo->prepare("SELECT * FROM genomic_imports WHERE patient_id=? ORDER BY imported_at DESC LIMIT 5");
$imports->execute([$patientId]);
$imports = $imports->fetchAll();
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="bi bi-dna me-2"></i>Upload Genético</h1>
    <a href="<?= baseUrl("pages/genomic/index.php") ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-upload me-2"></i><?= sanitize($patient['name']) ?></div>
            <div class="card-body">
                <div class="alert alert-info"><b>Formatos aceitos:</b> Genera, 23andMe, AncestryDNA. Importação em chunks (sem timeout).</div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Arquivo CSV:</label>
                    <input type="file" class="form-control form-control-lg" accept=".csv,.txt" id="fileInput">
                </div>
                <div id="fileInfo" class="mb-3 d-none">
                    <div class="alert alert-light"><i class="bi bi-file-text me-1"></i><span id="fName"></span> (<span id="fSize"></span>)</div>
                </div>
                <button class="btn btn-primary btn-lg w-100" id="startBtn" disabled><i class="bi bi-cloud-upload me-2"></i>Importar</button>
                <div id="progress" class="mt-3 d-none">
                    <div class="progress mb-2" style="height:28px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="pbar" style="width:0%"><span id="ptxt">0%</span></div>
                    </div>
                    <div id="pstatus" class="small text-muted">Preparando...</div>
                    <div id="plog" class="mt-2 small" style="max-height:150px;overflow-y:auto"></div>
                </div>
                <div id="done" class="mt-3 d-none">
                    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><span id="doneMsg"></span></div>
                    <a href="<?= baseUrl("pages/genomic/dashboard.php?patient_id=" . $patientId) ?>" class="btn btn-success btn-lg w-100 mt-2"><i class="bi bi-bar-chart me-2"></i>Ver Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Histórico</div>
            <div class="card-body p-0">
                <?php if (empty($imports)): ?>
                <p class="text-center py-3 text-muted">Nenhum</p>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($imports as $i): ?>
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <small class="text-truncate"><?= sanitize($i['file_name']) ?></small>
                            <span class="badge <?= $i['status'] === 'completed' ? 'bg-success' : 'bg-warning' ?>"><?= $i['status'] ?></span>
                        </div>
                        <small class="text-muted"><?= number_format($i['imported_snps'] ?? 0, 0, ',', '.') ?> SNPs</small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (hasGenomicData($patientId)): ?>
        <div class="card mt-3">
            <div class="card-body text-center">
                <a href="<?= baseUrl("pages/genomic/dashboard.php?patient_id=" . $patientId) ?>" class="btn btn-outline-success w-100"><i class="bi bi-bar-chart me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
var CHUNK = 50000, pid = <?= $patientId ?>, chunkUrl = '<?= baseUrl("pages/genomic/import_chunk.php") ?>';
var fileInput = document.getElementById('fileInput'), startBtn = document.getElementById('startBtn');

fileInput.addEventListener('change', function() {
    var f = this.files[0];
    if (f) {
        document.getElementById('fName').textContent = f.name;
        var s = f.size > 1048576 ? (f.size / 1048576).toFixed(1) + ' MB' : (f.size / 1024).toFixed(0) + ' KB';
        document.getElementById('fSize').textContent = s;
        document.getElementById('fileInfo').classList.remove('d-none');
        startBtn.disabled = false;
    }
});

startBtn.addEventListener('click', async function() {
    startBtn.disabled = true;
    document.getElementById('progress').classList.remove('d-none');
    var file = fileInput.files[0];
    log('Enviando arquivo...');

    // Step 1: Upload file
    var fd = new FormData();
    fd.append('file', file);
    fd.append('patient_id', pid);
    fd.append('action', 'upload');
    try {
        var r = await fetch(chunkUrl, {method: 'POST', body: fd});
        var j = await r.json();
        if (j.error) { log('ERRO: ' + j.error, 'danger'); return; }
        log('Arquivo recebido: ' + j.total_lines + ' linhas');
        var total = j.total_lines, fn = j.saved_as, offset = 0, totalImported = 0;

        // Step 2: Process chunks
        while (offset < total) {
            var fd2 = new FormData();
            fd2.append('patient_id', pid);
            fd2.append('action', 'chunk');
            fd2.append('offset', offset);
            fd2.append('limit', CHUNK);
            fd2.append('file_name', fn);
            var r2 = await fetch(chunkUrl, {method: 'POST', body: fd2});
            var j2 = await r2.json();
            if (j2.error) { log('ERRO chunk: ' + j2.error, 'danger'); return; }
            totalImported += j2.imported;
            offset += CHUNK;
            var pct = Math.min(95, Math.round(offset / total * 100));
            document.getElementById('pbar').style.width = pct + '%';
            document.getElementById('ptxt').textContent = pct + '%';
            document.getElementById('pstatus').textContent = 'Importando: ' + totalImported.toLocaleString() + ' SNPs...';
            log('Chunk ' + offset + '/' + total + ': +' + j2.imported + ' SNPs');
        }

        // Step 3: Analyze
        log('Executando análise genética...');
        document.getElementById('pstatus').textContent = 'Analisando...';
        var fd3 = new FormData();
        fd3.append('patient_id', pid);
        fd3.append('action', 'analyze');
        fd3.append('file_name', fn);
        var r3 = await fetch(chunkUrl, {method: 'POST', body: fd3});
        var j3 = await r3.json();
        document.getElementById('pbar').style.width = '100%';
        document.getElementById('pbar').className = 'progress-bar bg-success';
        document.getElementById('ptxt').textContent = '100%';
        document.getElementById('doneMsg').textContent = j3.total_snps.toLocaleString() + ' SNPs importados e analisados!';
        document.getElementById('done').classList.remove('d-none');
        log('CONCLUÍDO: ' + j3.total_snps + ' SNPs', 'success');
    } catch(e) {
        log('ERRO: ' + e.message, 'danger');
    }
});

function log(msg, type) {
    var d = document.getElementById('plog');
    d.innerHTML += '<div class="text-' + (type || 'muted') + '">' + new Date().toLocaleTimeString() + ' - ' + msg + '</div>';
    d.scrollTop = d.scrollHeight;
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>