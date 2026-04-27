<?php
/**
 * MEDIC - Lógica de exportação para uso local
 * Este arquivo é incluído por pages/export_local.php
 * NÃO deve ser acessado diretamente
 */

// Prevenir acesso direto
if (!function_exists('getConnection')) {
    http_response_code(403);
    exit('Acesso direto não permitido');
}

$exportsDir = realpath(__DIR__ . '/../../uploads') . '/exports';
$maxChunkSize = 50 * 1024 * 1024; // 50MB por pacote

// Garantir pasta exports
if (!is_dir($exportsDir)) {
    @mkdir($exportsDir, 0777, true);
}

// Limpar exports antigos (>2 horas)
if (is_dir($exportsDir)) {
    foreach (glob($exportsDir . '/MEDIC_*.zip') as $oldFile) {
        if (filemtime($oldFile) < time() - 7200) {
            @unlink($oldFile);
        }
    }
}

$action = $_GET['op'] ?? ($_GET['action'] ?? '');

// =============================================
// DOWNLOAD
// =============================================
if ($action === 'download' || $action === 'dl') {
    if (!isset($_GET['file'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Arquivo não especificado']);
        exit;
    }
    $filename = basename($_GET['file']);
    $filepath = $exportsDir . '/' . $filename;
    
    if (!file_exists($filepath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
        setFlash('danger', 'Arquivo não encontrado ou expirado.');
        redirect(baseUrl('pages/settings.php'));
        exit;
    }
    
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $fp = fopen($filepath, 'rb');
    while (!feof($fp)) {
        echo fread($fp, 8192);
        flush();
    }
    fclose($fp);
    exit;
}

// =============================================
// LISTAR pacotes
// =============================================
if ($action === 'list' || $action === 'ls') {
    header('Content-Type: application/json');
    $packages = [];
    if (is_dir($exportsDir)) {
        foreach (glob($exportsDir . '/MEDIC_*.zip') as $f) {
            $name = basename($f);
            $packages[] = [
                'file' => $name,
                'size' => filesize($f),
                'created' => filemtime($f),
                'type' => (strpos($name, 'Sistema') !== false) ? 'system' : 'uploads'
            ];
        }
    }
    echo json_encode(['success' => true, 'packages' => $packages]);
    exit;
}

// =============================================
// LIMPAR exports
// =============================================
if ($action === 'cleanup') {
    header('Content-Type: application/json');
    $removed = 0;
    if (is_dir($exportsDir)) {
        foreach (glob($exportsDir . '/MEDIC_*.zip') as $f) {
            @unlink($f);
            $removed++;
        }
    }
    echo json_encode(['success' => true, 'removed' => $removed]);
    exit;
}

// =============================================
// GERAR PACOTE DO SISTEMA
// =============================================
if ($action === 'generate_system' || $action === 'pkg_system') {
    @set_time_limit(120);
    @ini_set('memory_limit', '256M');
    header('Content-Type: application/json');
    
    try {
        if (!is_dir($exportsDir)) mkdir($exportsDir, 0777, true);
        
        foreach (glob($exportsDir . '/MEDIC_Sistema_*.zip') as $old) @unlink($old);
        
        $pdo = getConnection();
        $syncToken = bin2hex(random_bytes(32));
        
        $downloadName = 'MEDIC_Sistema_' . date('Y-m-d_His') . '.zip';
        $zipPath = $exportsDir . '/' . $downloadName;
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            echo json_encode(['success' => false, 'error' => 'Não foi possível criar o ZIP']);
            exit;
        }
        
        $baseDir = realpath(__DIR__ . '/../../');
        
        // 1. Exportar dados das tabelas como JSON
        $tables = [
            'users', 'patients', 'medical_records', 'exams', 'exam_files',
            'record_files', 'user_patients', 'medications', 'specialties', 'access_logs'
        ];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT * FROM `{$table}`");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $zip->addFromString("data/{$table}.json", json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (Exception $e) {
                $zip->addFromString("data/{$table}.json", "[]");
            }
        }
        
        // 2. Adicionar arquivos do projeto (EXCLUINDO uploads, data, exports, testes)
        $excludeDirs = ['data', '.git', '.vscode', 'node_modules', 'exports', 'uploads'];
        $excludeFiles = ['medic.zip', 'test_upload_check.php', 'test_export_direct.php'];
        
        $addDirToZip = function($zip, $dirPath, $zipPath, $excludeDirs, $excludeFiles) use (&$addDirToZip) {
            $files = @scandir($dirPath);
            if (!$files) return;
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $fullPath = $dirPath . '/' . $file;
                $relPath = ltrim($zipPath . '/' . $file, '/');
                if (is_dir($fullPath)) {
                    if (in_array($file, $excludeDirs)) continue;
                    $addDirToZip($zip, $fullPath, $relPath, $excludeDirs, $excludeFiles);
                } else {
                    if (in_array($file, $excludeFiles)) continue;
                    $zip->addFile($fullPath, $relPath);
                }
            }
        };
        
        $addDirToZip($zip, $baseDir, '', $excludeDirs, $excludeFiles);
        
        $zip->addFromString('uploads/.htaccess', "Options -Indexes\nDeny from all\n");
        
        // 3. Config local
        $onlineUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $basePath = rtrim(str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')))), '/');
        $fullOnlineUrl = $onlineUrl . $basePath;
        
        $localConfigFile = __DIR__ . '/../../config/database_local.php';
        $localConfig = file_get_contents($localConfigFile);
        $localConfig = str_replace(
            "define('ONLINE_SERVER_URL', '');",
            "define('ONLINE_SERVER_URL', '{$fullOnlineUrl}');",
            $localConfig
        );
        $localConfig = str_replace(
            "define('ONLINE_SYNC_TOKEN', '');",
            "define('ONLINE_SYNC_TOKEN', '{$syncToken}');",
            $localConfig
        );
        $zip->addFromString('config/database.php', $localConfig);
        @$zip->deleteName('config/database_local.php');
        
        // 4. Scripts de inicialização
        $startBat = "@echo off\r\ntitle MEDIC - Sistema Medico Local\r\ncolor 0A\r\n"
            . "echo ============================================\r\n"
            . "echo   MEDIC - Sistema Medico Familiar (Local)\r\n"
            . "echo ============================================\r\necho.\r\n\r\n"
            . "where php >nul 2>nul\r\nif %errorlevel% equ 0 (\r\n"
            . "    echo [OK] PHP encontrado no PATH.\r\n    goto :start_server\r\n)\r\n\r\n"
            . "if exist \"%~dp0php\\php.exe\" (\r\n    set \"PATH=%~dp0php;%PATH%\"\r\n"
            . "    echo [OK] PHP portatil encontrado.\r\n    goto :start_server\r\n)\r\n\r\n"
            . "if exist \"C:\\php\\php.exe\" ( set \"PATH=C:\\php;%PATH%\" & goto :start_server )\r\n"
            . "if exist \"C:\\xampp\\php\\php.exe\" ( set \"PATH=C:\\xampp\\php;%PATH%\" & goto :start_server )\r\n\r\n"
            . "echo [!] PHP nao encontrado. Tentando instalar...\r\necho.\r\n"
            . "powershell -ExecutionPolicy Bypass -File \"%~dp0setup.ps1\"\r\n"
            . "if exist \"%~dp0php\\php.exe\" ( set \"PATH=%~dp0php;%PATH%\" & goto :start_server )\r\n\r\n"
            . "echo [ERRO] PHP nao encontrado nem instalado.\r\n"
            . "echo Instale de: https://windows.php.net/download/\r\npause\r\nexit /b 1\r\n\r\n"
            . ":start_server\r\necho.\r\n"
            . "echo Verificando extensoes...\r\n"
            . "php -m 2>nul | findstr /i pdo_sqlite >nul 2>&1\r\n"
            . "if errorlevel 1 (\r\n"
            . "    echo Configurando php.ini...\r\n"
            . "    for /f \"delims=\" %%P in ('php -r \"echo PHP_BINARY;\" 2^>nul') do set \"PHP_BIN=%%P\"\r\n"
            . "    for %%F in (\"%PHP_BIN%\") do set \"PHP_DIR=%%~dpF\"\r\n"
            . "    echo extension_dir=%PHP_DIR%ext> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=pdo_sqlite>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=sqlite3>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=mbstring>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=openssl>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=fileinfo>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=gd>> \"%PHP_DIR%php.ini\"\r\n"
            . "    echo extension=curl>> \"%PHP_DIR%php.ini\"\r\n"
            . ")\r\necho.\r\n"
            . "echo Iniciando servidor na porta 8080...\r\n"
            . "echo Acesse: http://localhost:8080\r\necho.\r\n"
            . "start http://localhost:8080/pages/login.php\r\n"
            . "php -S localhost:8080 -t \"%~dp0\"\r\npause\r\n";
        $zip->addFromString('start.bat', $startBat);
        
        // setup.ps1
        $setupPs1 = <<<'PS1'
$ErrorActionPreference = "Continue"
Write-Host "MEDIC - Instalacao do PHP Portatil" -ForegroundColor Cyan
$phpDir = Join-Path $PSScriptRoot "php"
if (Test-Path (Join-Path $phpDir "php.exe")) { Write-Host "PHP ja instalado"; exit 0 }
$phpInPath = Get-Command php -ErrorAction SilentlyContinue
if ($phpInPath) { Write-Host "PHP no PATH: $($phpInPath.Source)"; exit 0 }

$phpZip = Join-Path $PSScriptRoot "php-portable.zip"
$urls = @(
    "https://windows.php.net/downloads/releases/latest/php-8.3-nts-Win32-vs16-x64-latest.zip",
    "https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64-latest.zip"
)
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$ProgressPreference = "SilentlyContinue"
$ok = $false
foreach ($url in $urls) {
    try {
        Write-Host "Baixando: $url"
        Invoke-WebRequest -Uri $url -OutFile $phpZip -UseBasicParsing -TimeoutSec 120
        if ((Test-Path $phpZip) -and (Get-Item $phpZip).Length -gt 1MB) { $ok = $true; break }
        if (Test-Path $phpZip) { Remove-Item $phpZip -Force }
    } catch { Write-Host "Falhou: $_" }
}
if (-not $ok) {
    Write-Host "Instale PHP manualmente: https://windows.php.net/download/"
    Read-Host "Enter para sair"; exit 1
}
New-Item -ItemType Directory -Path $phpDir -Force | Out-Null
Expand-Archive -Path $phpZip -DestinationPath $phpDir -Force
Remove-Item $phpZip -Force -ErrorAction SilentlyContinue

$phpIni = Join-Path $phpDir "php.ini"
$dev = Join-Path $phpDir "php.ini-development"
if (Test-Path $dev) { Copy-Item $dev $phpIni -Force } else { New-Item $phpIni -Force | Out-Null }
$ini = Get-Content $phpIni -Raw
@("pdo_sqlite","sqlite3","mbstring","openssl","fileinfo","gd","curl") | ForEach-Object { $ini = $ini -replace ";extension=$_", "extension=$_" }
$ini += "`nextension_dir=ext`nupload_max_filesize=20M`npost_max_size=25M`nmax_execution_time=120`nmemory_limit=256M`n"
Set-Content $phpIni $ini
Write-Host "PHP instalado em: $phpDir" -ForegroundColor Green
exit 0
PS1;
        $zip->addFromString('setup.ps1', $setupPs1);
        
        // 5. Sync page
        $syncPage = @file_get_contents(__DIR__ . '/local_sync_local_template.php');
        if ($syncPage) {
            $zip->addFromString('pages/admin/local_sync.php', $syncPage);
        }
        
        // 6. Gerar baseline da exportação (para sync incremental)
        $baseline = [
            'exported_at' => date('Y-m-d H:i:s'),
            'record_ids' => [],
            'file_hashes' => []
        ];
        // IDs de cada tabela exportada
        foreach ($tables as $btable) {
            try {
                $bstmt = $pdo->query("SELECT id FROM `{$btable}`");
                $ids = $bstmt->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($ids)) {
                    $baseline['record_ids'][$btable] = $ids;
                }
            } catch (Exception $e) {}
        }
        // Hashes dos arquivos de upload
        $uploadsBase = realpath(__DIR__ . '/../../uploads');
        if ($uploadsBase && is_dir($uploadsBase)) {
            $biter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsBase));
            foreach ($biter as $bfile) {
                if ($bfile->isFile()) {
                    $brel = str_replace($uploadsBase . DIRECTORY_SEPARATOR, '', $bfile->getRealPath());
                    $brel = str_replace('\\', '/', $brel);
                    if (strpos($brel, 'exports/') === 0 || $brel === '.htaccess') continue;
                    $baseline['file_hashes'][$brel] = md5_file($bfile->getRealPath());
                }
            }
        }
        $zip->addFromString('config/.export_baseline.json', json_encode($baseline, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // 7. Extras
        $zip->addFromString('.htaccess', "Options -Indexes\n");
        $zip->addFromString('data/.gitkeep', '');
        
        $zip->close();
        
        // Salvar token
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `sync_tokens` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `token` VARCHAR(255) NOT NULL,
                `user_id` INT NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `used_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $stmt = $pdo->prepare("INSERT INTO sync_tokens (token, user_id) VALUES (?, ?)");
            $stmt->execute([$syncToken, $_SESSION['user_id']]);
        } catch (Exception $e) {}
        
        echo json_encode([
            'success' => true,
            'file' => $downloadName,
            'size' => filesize($zipPath)
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// =============================================
// GERAR PACOTES DE UPLOADS
// =============================================
if ($action === 'generate_uploads' || $action === 'pkg_uploads') {
    header('Content-Type: application/json');
    @set_time_limit(300);
    @ini_set('memory_limit', '256M');
    
    try {
        if (!is_dir($exportsDir)) mkdir($exportsDir, 0777, true);
        
        foreach (glob($exportsDir . '/MEDIC_Uploads_*.zip') as $old) @unlink($old);
        
        $uploadsDir = realpath(__DIR__ . '/../../uploads');
        if (!$uploadsDir || !is_dir($uploadsDir)) {
            echo json_encode(['success' => true, 'packages' => [], 'message' => 'Sem arquivos.']);
            exit;
        }
        
        $allFiles = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) continue;
            $fullPath = $fileInfo->getRealPath();
            $relPath = str_replace($uploadsDir . DIRECTORY_SEPARATOR, '', $fullPath);
            $relPath = str_replace('\\', '/', $relPath);
            if (strpos($relPath, 'exports/') === 0 || $relPath === '.htaccess') continue;
            
            $allFiles[] = [
                'full' => $fullPath,
                'rel' => 'uploads/' . $relPath,
                'size' => $fileInfo->getSize()
            ];
        }
        
        if (empty($allFiles)) {
            echo json_encode(['success' => true, 'packages' => [], 'message' => 'Sem arquivos.']);
            exit;
        }
        
        $chunks = [];
        $currentChunk = [];
        $currentSize = 0;
        foreach ($allFiles as $file) {
            if ($currentSize + $file['size'] > $maxChunkSize && !empty($currentChunk)) {
                $chunks[] = $currentChunk;
                $currentChunk = [];
                $currentSize = 0;
            }
            $currentChunk[] = $file;
            $currentSize += $file['size'];
        }
        if (!empty($currentChunk)) $chunks[] = $currentChunk;
        
        $packages = [];
        $timestamp = date('Y-m-d_His');
        foreach ($chunks as $i => $chunk) {
            $partNum = $i + 1;
            $totalParts = count($chunks);
            $zipName = "MEDIC_Uploads_Parte{$partNum}de{$totalParts}_{$timestamp}.zip";
            $zipPath = $exportsDir . '/' . $zipName;
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) continue;
            foreach ($chunk as $file) $zip->addFile($file['full'], $file['rel']);
            $zip->close();
            
            $packages[] = [
                'file' => $zipName,
                'size' => filesize($zipPath),
                'files_count' => count($chunk),
                'part' => $partNum,
                'total_parts' => $totalParts
            ];
        }
        
        echo json_encode([
            'success' => true,
            'packages' => $packages,
            'total_files' => count($allFiles)
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Página padrão
header('Content-Type: application/json');
echo json_encode(['success' => false, 'error' => 'Ação não especificada. Use ?op=pkg_system ou ?op=pkg_uploads']);