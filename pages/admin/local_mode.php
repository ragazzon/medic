<?php
$pageTitle = 'Uso Local';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$isLocal = defined('LOCAL_MODE') && LOCAL_MODE;

// Estatísticas do banco
$tables = ['users','patients','medical_records','exams','exam_files','record_files','medications','specialties'];
$counts = [];
$totalRecords = 0;
foreach ($tables as $t) {
    try {
        $c = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        $counts[$t] = intval($c);
        $totalRecords += $counts[$t];
    } catch (Exception $e) {
        $counts[$t] = 0;
    }
}

// Tamanho dos uploads
$uploadsSize = 0;
$uploadsCount = 0;
$uploadsDir = realpath(__DIR__ . '/../../uploads');
if ($uploadsDir && is_dir($uploadsDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $rel = str_replace($uploadsDir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $rel = str_replace('\\', '/', $rel);
            if (strpos($rel, 'exports/') === 0 || $rel === '.htaccess') continue;
            $uploadsSize += $file->getSize();
            $uploadsCount++;
        }
    }
}

function formatBytes($bytes, $precision = 1) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
}

// Tokens de sync existentes (apenas modo online)
$syncTokens = [];
if (!$isLocal) {
    try {
        $syncTokens = $pdo->query("SELECT * FROM sync_tokens ORDER BY created_at DESC LIMIT 10")->fetchAll();
    } catch (Exception $e) {}
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>
        <?php if ($isLocal): ?>
            <i class="bi bi-pc-display me-2"></i>Modo Local
        <?php else: ?>
            <i class="bi bi-download me-2"></i>Uso Local
        <?php endif; ?>
    </h1>
    <?php if ($isLocal): ?>
        <span class="badge bg-warning text-dark fs-6"><i class="bi bi-pc-display me-1"></i>Executando Localmente</span>
    <?php endif; ?>
</div>

<?php if ($isLocal): ?>
<!-- MODO LOCAL -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-pc-display text-warning" style="font-size:40px;"></i>
                    <div class="ms-3">
                        <h4 class="mb-0">Você está no Modo Local</h4>
                        <p class="text-muted mb-0">O sistema está rodando na sua máquina com banco de dados SQLite.</p>
                    </div>
                </div>
                <hr>
                <p>Todas as alterações feitas aqui são salvas localmente. Para enviar as alterações para o servidor online, use a sincronização.</p>
                <a href="<?= baseUrl('pages/admin/local_sync.php') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-cloud-arrow-up me-2"></i>Sincronizar com Servidor Online
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5><i class="bi bi-database me-2"></i>Dados Locais</h5>
                <div class="mt-3">
                    <?php foreach ($counts as $table => $count): ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= ucfirst(str_replace('_', ' ', $table)) ?></span>
                        <span class="badge bg-secondary"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between small fw-bold">
                        <span>Total</span>
                        <span><?= $totalRecords ?> registros</span>
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span>Arquivos</span>
                        <span><?= $uploadsCount ?> (<?= formatBytes($uploadsSize) ?>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- MODO ONLINE - Downloads divididos -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <!-- Pacote do Sistema -->
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Passo 1: Pacote do Sistema (Obrigatório)</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Contém todo o código do sistema, dados do banco de dados e scripts de inicialização. 
                    <strong>Este pacote é obrigatório</strong> para rodar o sistema localmente.
                </p>
                <div class="bg-light rounded p-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Registros no banco:</span>
                        <strong><?= $totalRecords ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small">Inclui:</span>
                        <strong>Código + Dados + Scripts</strong>
                    </div>
                </div>
                
                <div id="system-status" class="d-none alert mb-3"></div>
                
                <button id="btn-generate-system" class="btn btn-primary btn-lg" onclick="generateSystem()">
                    <i class="bi bi-gear me-2"></i>Gerar Pacote do Sistema
                </button>
                <a id="btn-download-system" class="btn btn-success btn-lg d-none" href="#">
                    <i class="bi bi-download me-2"></i>Baixar Pacote do Sistema
                </a>
            </div>
        </div>
        
        <!-- Pacotes de Uploads -->
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-down me-2"></i>Passo 2: Arquivos de Upload (Opcional)</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Arquivos enviados pelos usuários (exames, prontuários, etc.). São divididos em pacotes de até ~50MB cada.
                    <strong>O sistema funciona sem estes arquivos</strong> — você só não verá os anexos.
                </p>
                
                <div class="bg-light rounded p-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Total de arquivos:</span>
                        <strong><?= $uploadsCount ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small">Tamanho total:</span>
                        <strong><?= formatBytes($uploadsSize) ?></strong>
                    </div>
                    <?php if ($uploadsSize > 0): ?>
                    <div class="d-flex justify-content-between">
                        <span class="small">Pacotes estimados:</span>
                        <strong>~<?= max(1, ceil($uploadsSize / (50 * 1024 * 1024))) ?> arquivo(s)</strong>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($uploadsCount > 0): ?>
                <div id="uploads-status" class="d-none alert mb-3"></div>
                
                <button id="btn-generate-uploads" class="btn btn-secondary btn-lg" onclick="generateUploads()">
                    <i class="bi bi-gear me-2"></i>Gerar Pacotes de Uploads
                </button>
                
                <div id="uploads-list" class="d-none mt-3">
                    <h6>Pacotes disponíveis para download:</h6>
                    <div id="uploads-packages"></div>
                </div>
                <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>Não há arquivos de upload para exportar.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Instruções -->
        <div class="card mb-3">
            <div class="card-body">
                <h5><i class="bi bi-list-ol me-2"></i>Como Usar</h5>
                <ol class="mt-3 ps-3">
                    <li class="mb-2">Gere e baixe o <strong>Pacote do Sistema</strong></li>
                    <li class="mb-2">Extraia o ZIP em uma pasta</li>
                    <li class="mb-2"><em>(Opcional)</em> Gere e baixe os <strong>Pacotes de Uploads</strong></li>
                    <li class="mb-2"><em>(Opcional)</em> Extraia os ZIPs de uploads <strong>na mesma pasta</strong></li>
                    <li class="mb-2">Dê duplo clique em <strong>start.bat</strong></li>
                    <li class="mb-2">O navegador abrirá automaticamente</li>
                </ol>
                
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Dica:</strong> Extraia todos os ZIPs na mesma pasta para que os uploads fiquem no lugar correto.
                </div>
            </div>
        </div>
        
        <!-- Requisitos -->
        <div class="card mb-3">
            <div class="card-body">
                <h5><i class="bi bi-pc-display me-2"></i>Requisitos</h5>
                <ul class="list-unstyled mt-3 mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Windows 10/11</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Conexão internet (apenas 1ª vez)</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>~100MB de espaço em disco</li>
                    <li class="text-muted small"><i class="bi bi-info-circle me-2"></i>PHP será instalado automaticamente</li>
                </ul>
            </div>
        </div>

        <!-- Dados -->
        <div class="card">
            <div class="card-body">
                <h5><i class="bi bi-database me-2"></i>Dados Incluídos</h5>
                <div class="mt-3">
                    <?php foreach ($counts as $table => $count): if ($count === 0) continue; ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= ucfirst(str_replace('_', ' ', $table)) ?></span>
                        <span class="badge bg-secondary"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($syncTokens)): ?>
<div class="card">
    <div class="card-body">
        <h5><i class="bi bi-key me-2"></i>Tokens de Sincronização</h5>
        <div class="table-responsive mt-3">
            <table class="table table-sm">
                <thead>
                    <tr><th>Criado em</th><th>Usado em</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($syncTokens as $tk): ?>
                <tr>
                    <td><small><?= date('d/m/Y H:i', strtotime($tk['created_at'])) ?></small></td>
                    <td><small><?= $tk['used_at'] ? date('d/m/Y H:i', strtotime($tk['used_at'])) : '-' ?></small></td>
                    <td>
                        <?php if ($tk['used_at']): ?>
                            <span class="badge bg-success">Usado</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pendente</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const exportUrl = '<?= baseUrl("pages/export_local.php") ?>';

function formatSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024, units = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + units[i];
}

function setStatus(id, type, msg) {
    const el = document.getElementById(id);
    el.className = 'alert alert-' + type + ' mb-3';
    el.innerHTML = msg;
}

function generateSystem() {
    const btn = document.getElementById('btn-generate-system');
    const statusEl = document.getElementById('system-status');
    const dlBtn = document.getElementById('btn-download-system');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando...';
    statusEl.className = 'alert alert-info mb-3';
    statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando pacote do sistema. Aguarde...';
    dlBtn.classList.add('d-none');
    
    fetch(exportUrl + '?op=pkg_system&t=' + Date.now())
    .then(r => {
        if (!r.ok) {
            return r.text().then(body => {
                throw new Error('HTTP ' + r.status + ': ' + (body ? body.substring(0, 500) : r.statusText));
            });
        }
        return r.text();
    })
    .then(text => {
        if (!text || !text.trim()) throw new Error('Resposta vazia do servidor. Possível timeout ou erro fatal.');
        try { return JSON.parse(text); } catch(e) { 
            throw new Error('Resposta inválida: ' + text.substring(0, 200)); 
        }
    })
    .then(data => {
        if (data.success && data.file) {
            setStatus('system-status', 'success', 
                '<i class="bi bi-check-circle me-2"></i>Pacote gerado! (' + formatSize(data.size) + ')');
            dlBtn.href = exportUrl + '?action=download&file=' + encodeURIComponent(data.file);
            dlBtn.classList.remove('d-none');
            btn.classList.add('d-none');
        } else {
            setStatus('system-status', 'danger', 
                '<i class="bi bi-exclamation-triangle me-2"></i>Erro: ' + (data.error || 'Falha desconhecida'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-gear me-2"></i>Tentar Novamente';
        }
    })
    .catch(err => {
        setStatus('system-status', 'danger', 
            '<i class="bi bi-exclamation-triangle me-2"></i>Erro de conexão: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-gear me-2"></i>Tentar Novamente';
    });
}

function generateUploads() {
    const btn = document.getElementById('btn-generate-uploads');
    const statusEl = document.getElementById('uploads-status');
    const listEl = document.getElementById('uploads-list');
    const pkgEl = document.getElementById('uploads-packages');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando...';
    statusEl.className = 'alert alert-info mb-3';
    statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando pacotes de uploads. Isso pode levar alguns minutos...';
    listEl.classList.add('d-none');
    
    fetch(exportUrl + '?op=pkg_uploads&t=' + Date.now())
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (!data.packages || data.packages.length === 0) {
                setStatus('uploads-status', 'info', 
                    '<i class="bi bi-info-circle me-2"></i>Nenhum arquivo de upload para exportar.');
                btn.classList.add('d-none');
                return;
            }
            
            setStatus('uploads-status', 'success', 
                '<i class="bi bi-check-circle me-2"></i>' + data.packages.length + ' pacote(s) gerado(s) com ' + data.total_files + ' arquivo(s)!');
            
            let html = '';
            data.packages.forEach((pkg, i) => {
                html += '<div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">' +
                    '<div>' +
                        '<i class="bi bi-file-earmark-zip text-warning me-2"></i>' +
                        '<strong>Parte ' + pkg.part + ' de ' + pkg.total_parts + '</strong>' +
                        '<span class="text-muted ms-2 small">(' + formatSize(pkg.size) + ' — ' + pkg.files_count + ' arquivos)</span>' +
                    '</div>' +
                    '<a href="' + exportUrl + '?action=download&file=' + encodeURIComponent(pkg.file) + '" class="btn btn-sm btn-outline-success">' +
                        '<i class="bi bi-download me-1"></i>Baixar' +
                    '</a>' +
                '</div>';
            });
            
            pkgEl.innerHTML = html;
            listEl.classList.remove('d-none');
            btn.classList.add('d-none');
        } else {
            setStatus('uploads-status', 'danger', 
                '<i class="bi bi-exclamation-triangle me-2"></i>Erro: ' + (data.error || 'Falha desconhecida'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-gear me-2"></i>Tentar Novamente';
        }
    })
    .catch(err => {
        setStatus('uploads-status', 'danger', 
            '<i class="bi bi-exclamation-triangle me-2"></i>Erro de conexão: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-gear me-2"></i>Tentar Novamente';
    });
}
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>