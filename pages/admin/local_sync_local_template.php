<?php
$pageTitle = "Sincronizar com Servidor";
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

if (!defined('LOCAL_MODE') || !LOCAL_MODE) {
    setFlash('danger', 'Esta função só está disponível no modo local.');
    redirect(baseUrl('pages/dashboard.php'));
}

$syncUrl = defined('ONLINE_SERVER_URL') ? ONLINE_SERVER_URL : '';
$syncToken = defined('ONLINE_SYNC_TOKEN') ? ONLINE_SYNC_TOKEN : '';
$message = '';
$messageType = '';

$syncStateFile = __DIR__ . '/../../config/.last_sync_state.json';
$baselineFile = __DIR__ . '/../../config/.export_baseline.json';

function loadJsonFile($file) {
    if (file_exists($file)) {
        $d = json_decode(file_get_contents($file), true);
        if ($d) return $d;
    }
    return [];
}

function saveJsonFile($file, $data) {
    @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function solveInfinityFreeChallenge($html) {
    // InfinityFree anti-bot: parse AES challenge from HTML
    // Looks for: var a=toNumbers("..."),b=toNumbers("..."),c=toNumbers("...");
    if (preg_match('/var\s+a\s*=\s*toNumbers\("([0-9a-f]+)"\)/', $html, $ma) &&
        preg_match('/b\s*=\s*toNumbers\("([0-9a-f]+)"\)/', $html, $mb) &&
        preg_match('/c\s*=\s*toNumbers\("([0-9a-f]+)"\)/', $html, $mc)) {
        
        $key = hex2bin($ma[1]);   // AES key (a)
        $iv  = hex2bin($mb[1]);   // IV (b)
        $enc = hex2bin($mc[1]);   // encrypted data (c)
        
        // Decrypt using AES-128-CBC (mode 2 = CBC in slowAES)
        $decrypted = @openssl_decrypt($enc, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        if ($decrypted !== false) {
            // Remove PKCS7 padding
            $pad = ord($decrypted[strlen($decrypted) - 1]);
            if ($pad > 0 && $pad <= 16) {
                $decrypted = substr($decrypted, 0, -$pad);
            }
            return bin2hex($decrypted);
        }
    }
    return null;
}

function sendToServer($url, $payload) {
    static $antiBotCookie = null;
    $cookieFile = sys_get_temp_dir() . '/medic_sync_cookies.txt';
    
    // If we have a cached anti-bot cookie, use it
    if ($antiBotCookie === null && file_exists($cookieFile) && filemtime($cookieFile) > time() - 3600) {
        $antiBotCookie = trim(file_get_contents($cookieFile));
    }
    
    $makeRequest = function($url, $payload, $cookie = null) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $headers = ['Content-Type: application/json'];
        if ($cookie) {
            $headers[] = 'Cookie: __test=' . $cookie;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) MEDIC-Sync/1.0');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        return ['response' => $response, 'httpCode' => $httpCode, 'error' => $curlError];
    };
    
    // First attempt with existing cookie
    $result = $makeRequest($url, $payload, $antiBotCookie);
    
    // Check if we got the anti-bot challenge page
    if ($result['response'] && strpos($result['response'], 'aes.js') !== false) {
        // Solve the challenge
        $cookieValue = solveInfinityFreeChallenge($result['response']);
        if ($cookieValue) {
            $antiBotCookie = $cookieValue;
            @file_put_contents($cookieFile, $cookieValue);
            // Retry with the solved cookie
            $result = $makeRequest($url, $payload, $cookieValue);
        } else {
            return ['response' => '', 'httpCode' => 0, 'error' => 'Não foi possível resolver o desafio anti-bot do servidor. Tente acessar ' . $url . ' no navegador primeiro.'];
        }
    }
    
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sync') {
    @set_time_limit(600);
    @ini_set('max_execution_time', '600');
    @ini_set('memory_limit', '256M');
    
    $pdo = getConnection();
    $syncState = loadJsonFile($syncStateFile);
    $baseline = loadJsonFile($baselineFile);
    $forceFull = isset($_POST['force_full']) && $_POST['force_full'] === '1';
    $apiUrl = $syncUrl . '/pages/admin/local_sync_api.php';
    
    // ========================================
    // ETAPA 1: Detecção de mudanças via SNAPSHOT (hash de cada registro)
    // Compara estado atual do banco com snapshot salvo após última sync
    // ========================================
    $tables = ['users','patients','medical_records','exams','exam_files',
               'record_files','user_patients','medications','specialties'];
    
    // Carregar snapshot anterior (hashes de registros já sincronizados)
    $prevSnapshot = [];
    $baselineFileHashes = [];
    
    if ($forceFull) {
        // Forçar completo - ignorar snapshot
    } elseif (!empty($syncState['record_hashes'])) {
        // Já sincronizou - usar snapshot de hashes
        $prevSnapshot = $syncState['record_hashes'];
        $baselineFileHashes = $syncState['file_hashes'] ?? [];
    } elseif (!empty($baseline['record_hashes'])) {
        // Primeira sync - usar snapshot do baseline de exportação
        $prevSnapshot = $baseline['record_hashes'];
        $baselineFileHashes = $baseline['file_hashes'] ?? [];
    } elseif (!empty($baseline['record_ids'])) {
        // Baseline antigo (sem hashes) - converter IDs para snapshot vazio (forçar sync de tudo)
        $baselineFileHashes = $baseline['file_hashes'] ?? [];
    }
    
    $allData = [];
    $totalRecords = 0;
    $changedExamIds = [];
    $changedRecordIds = [];
    $currentSnapshot = []; // Snapshot atual para salvar após sync
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $allRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $changedRows = [];
            $currentSnapshot[$table] = [];
            
            foreach ($allRows as $row) {
                $id = $row['id'] ?? null;
                if ($id === null) continue;
                
                // Calcular hash do registro atual
                $rowHash = md5(json_encode($row));
                $currentSnapshot[$table][$id] = $rowHash;
                
                // Comparar com snapshot anterior
                $prevHash = $prevSnapshot[$table][$id] ?? null;
                
                if ($prevHash === null || $prevHash !== $rowHash) {
                    // Registro novo ou modificado
                    $changedRows[] = $row;
                }
            }
            
            if (!empty($changedRows)) {
                $allData[$table] = $changedRows;
                $totalRecords += count($changedRows);
                
                if ($table === 'exams') {
                    foreach ($changedRows as $r) $changedExamIds[] = $r['id'];
                } elseif ($table === 'medical_records') {
                    foreach ($changedRows as $r) $changedRecordIds[] = $r['id'];
                }
            }
        } catch (Exception $e) {
            // Tabela pode não existir no SQLite
        }
    }
    
    // ========================================
    // ETAPA 2: Identificar APENAS arquivos relacionados a dados alterados
    // ========================================
    $filesToSync = [];
    $newFileHashes = $baselineFileHashes; // Manter hashes existentes
    $uploadsDir = __DIR__ . '/../../uploads';
    $filesSkipped = 0;
    
    // Coletar file_paths dos exam_files e record_files modificados
    $relatedFilePaths = [];
    if (!empty($allData['exam_files'])) {
        foreach ($allData['exam_files'] as $ef) {
            if (!empty($ef['file_path'])) $relatedFilePaths[] = $ef['file_path'];
        }
    }
    if (!empty($allData['record_files'])) {
        foreach ($allData['record_files'] as $rf) {
            if (!empty($rf['file_path'])) $relatedFilePaths[] = $rf['file_path'];
        }
    }
    // Também buscar arquivos de exames/registros que foram modificados
    if (!empty($changedExamIds)) {
        try {
            $ph = implode(',', array_fill(0, count($changedExamIds), '?'));
            $stmt = $pdo->prepare("SELECT file_path FROM exam_files WHERE exam_id IN ({$ph})");
            $stmt->execute($changedExamIds);
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $fp) {
                if ($fp) $relatedFilePaths[] = $fp;
            }
        } catch (Exception $e) {}
    }
    if (!empty($changedRecordIds)) {
        try {
            $ph = implode(',', array_fill(0, count($changedRecordIds), '?'));
            $stmt = $pdo->prepare("SELECT file_path FROM record_files WHERE record_id IN ({$ph})");
            $stmt->execute($changedRecordIds);
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $fp) {
                if ($fp) $relatedFilePaths[] = $fp;
            }
        } catch (Exception $e) {}
    }
    // Fotos de pacientes modificados
    if (!empty($allData['patients'])) {
        foreach ($allData['patients'] as $p) {
            if (!empty($p['photo'])) $relatedFilePaths[] = $p['photo'];
        }
    }
    
    $relatedFilePaths = array_unique($relatedFilePaths);
    
    // Verificar quais desses arquivos existem e são novos/modificados
    foreach ($relatedFilePaths as $fp) {
        // file_path pode ser "uploads/exams/xxx.pdf" ou "exams/xxx.pdf"
        $relPath = preg_replace('#^uploads/#', '', $fp);
        $fullPath = $uploadsDir . '/' . $relPath;
        
        if (!file_exists($fullPath)) continue;
        
        $fileHash = md5_file($fullPath);
        $newFileHashes[$relPath] = $fileHash;
        
        if (isset($baselineFileHashes[$relPath]) && $baselineFileHashes[$relPath] === $fileHash) {
            $filesSkipped++;
            continue;
        }
        
        $filesToSync[] = ['relPath' => $relPath, 'fullPath' => $fullPath];
    }
    
    // Nada para sincronizar?
    if ($totalRecords === 0 && count($filesToSync) === 0) {
        $message = 'Nenhuma alteração detectada desde a última sincronização. Tudo já está atualizado!';
        $messageType = 'info';
    } else {
        $syncErrors = [];
        
        // ========================================
        // ETAPA 3: Enviar dados em lotes de 25 registros por tabela
        // ========================================
        $BATCH_SIZE = 25;
        $dataBatchesSent = 0;
        
        if ($totalRecords > 0) {
            foreach ($allData as $tblName => $tblRows) {
                $chunks = array_chunk($tblRows, $BATCH_SIZE);
                $chunkNum = 0;
                $totalChunks = count($chunks);
                
                foreach ($chunks as $chunk) {
                    $chunkNum++;
                    @set_time_limit(120); // Reset timer: 120s per batch
                    
                    $dataPayload = json_encode([
                        'token' => $syncToken,
                        'user_id' => $_SESSION['user_id'],
                        'data' => [$tblName => $chunk],
                        'files' => [],
                        'synced_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $result = sendToServer($apiUrl, $dataPayload);
                    unset($dataPayload);
                    
                    if ($result['error']) {
                        $syncErrors[] = "Erro conexão ({$tblName} lote {$chunkNum}/{$totalChunks}): " . $result['error'];
                        break 2;
                    } elseif ($result['httpCode'] !== 200) {
                        $raw = substr($result['response'] ?? '', 0, 300);
                        $syncErrors[] = "Erro HTTP {$result['httpCode']} ({$tblName} lote {$chunkNum}/{$totalChunks}): {$raw}";
                        break 2;
                    } else {
                        $resp = json_decode($result['response'], true);
                        if (!isset($resp['success']) || !$resp['success']) {
                            $raw = substr($result['response'] ?? '', 0, 300);
                            $syncErrors[] = "Erro ({$tblName} lote {$chunkNum}): " . ($resp['error'] ?? "Resposta inválida: {$raw}");
                            break 2;
                        }
                    }
                    $dataBatchesSent++;
                }
            }
            unset($allData);
        }
        
        // ========================================
        // ETAPA 4: Enviar arquivos um a um (com reset de timer)
        // ========================================
        $filesSent = 0;
        
        if (empty($syncErrors) && count($filesToSync) > 0) {
            foreach ($filesToSync as $fileInfo) {
                @set_time_limit(120); // Reset timer: 120s per file
                
                $fileContent = @file_get_contents($fileInfo['fullPath']);
                if ($fileContent === false) continue;
                
                $batch = [$fileInfo['relPath'] => base64_encode($fileContent)];
                unset($fileContent);
                
                $filePayload = json_encode([
                    'token' => $syncToken,
                    'user_id' => $_SESSION['user_id'],
                    'data' => [],
                    'files' => $batch,
                    'synced_at' => date('Y-m-d H:i:s')
                ]);
                
                $result = sendToServer($apiUrl, $filePayload);
                unset($filePayload, $batch);
                
                if ($result['error']) {
                    $syncErrors[] = 'Erro arquivo ' . $fileInfo['relPath'] . ': ' . $result['error'];
                    break;
                } elseif ($result['httpCode'] !== 200) {
                    $raw = substr($result['response'] ?? '', 0, 300);
                    $syncErrors[] = "Erro HTTP {$result['httpCode']} arquivo: {$raw}";
                    break;
                } else {
                    $resp = json_decode($result['response'], true);
                    if (isset($resp['success']) && $resp['success']) {
                        $filesSent++;
                    }
                }
            }
        }
        
        // ========================================
        // RESULTADO
        // ========================================
        if (empty($syncErrors)) {
            saveJsonFile($syncStateFile, [
                'last_sync' => date('Y-m-d H:i:s'),
                'record_hashes' => $currentSnapshot,
                'file_hashes' => $newFileHashes
            ]);
            
            $details = [];
            $details[] = "Registros: {$totalRecords}";
            $details[] = "Arquivos: {$filesSent}";
            if ($filesSkipped > 0) $details[] = "Ignorados: {$filesSkipped}";
            $message = 'Sincronização concluída! ' . implode(' | ', $details);
            $messageType = 'success';
        } else {
            $message = 'Erros: ' . implode('; ', $syncErrors);
            $messageType = 'danger';
        }
    }
}

$syncState = loadJsonFile($syncStateFile);
$baseline = loadJsonFile($baselineFile);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-cloud-upload me-2"></i>Sincronizar com Servidor</h1>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cloud-upload" style="font-size:60px;color:#0d6efd;"></i>
                <h4 class="mt-3">Enviar Alterações para o Servidor</h4>
                <p class="text-muted">Envia apenas dados e arquivos novos ou modificados.</p>
                
                <?php if (!empty($syncState['last_sync'])): ?>
                <p class="small text-success">
                    <i class="bi bi-check-circle me-1"></i>Última sync: <strong><?= htmlspecialchars($syncState['last_sync']) ?></strong>
                </p>
                <?php elseif (!empty($baseline['exported_at'])): ?>
                <p class="small text-info">
                    <i class="bi bi-info-circle me-1"></i>Baseline: exportado em <strong><?= htmlspecialchars($baseline['exported_at']) ?></strong>
                </p>
                <?php else: ?>
                <p class="small text-warning">
                    <i class="bi bi-exclamation-circle me-1"></i>Sem baseline — todos os dados serão enviados.
                </p>
                <?php endif; ?>
                
                <?php if ($syncUrl): ?>
                <p class="small"><strong>Servidor:</strong> <?= htmlspecialchars($syncUrl) ?></p>
                <form method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Sincronizando...'; return true;">
                    <input type="hidden" name="action" value="sync">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Sincronizar
                    </button>
                </form>
                
                <?php if (!empty($syncState['last_sync']) || !empty($baseline['exported_at'])): ?>
                <div class="mt-3">
                    <form method="POST" class="d-inline" onsubmit="return confirm('Enviar TODOS os dados e arquivos?');">
                        <input type="hidden" name="action" value="sync">
                        <input type="hidden" name="force_full" value="1">
                        <button type="submit" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i>Forçar Completa
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="alert alert-warning mt-3">URL do servidor não configurada.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5><i class="bi bi-info-circle me-2"></i>Como funciona</h5>
                <ul class="list-unstyled mt-3">
                    <li class="mb-2"><i class="bi bi-lightning text-primary me-2"></i>Compara dados locais com o baseline da exportação</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Envia apenas registros novos ou modificados</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Envia apenas arquivos de registros alterados</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Dados enviados tabela por tabela (baixa memória)</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Arquivos enviados um a um</li>
                </ul>
                
                <h6 class="mt-4">Dados Locais:</h6>
                <?php
                $pdo = getConnection();
                $localTables = ['patients','medical_records','exams','medications','specialties'];
                foreach ($localTables as $t):
                    try { $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn(); } catch (Exception $e) { $count = 0; }
                ?>
                <div class="d-flex justify-content-between small mb-1">
                    <span><?= ucfirst(str_replace('_', ' ', $t)) ?></span>
                    <span class="badge bg-secondary"><?= $count ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= baseUrl('pages/admin/local_mode.php') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>