<?php
// Capturar erros fatais
ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_start();

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(200); // Forçar 200 para ver a mensagem
        echo json_encode([
            'success' => false,
            'error' => 'PHP Fatal: ' . $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line']
        ]);
    }
});

/**
 * MEDIC - Exportação para Uso Local (Pacotes Divididos)
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$exportsDir = __DIR__ . '/../../uploads/exports';
$maxChunkSize = 50 * 1024 * 1024; // 50MB por pacote de uploads

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
// DOWNLOAD de arquivo já gerado
// =============================================
if (($action === 'download' || $action === 'dl') && isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = $exportsDir . '/' . $filename;
    
    if (!file_exists($filepath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
        setFlash('danger', 'Arquivo não encontrado ou expirado.');
        redirect(baseUrl('pages/admin/local_mode.php'));
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
// LISTAR pacotes disponíveis (JSON)
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
// LIMPAR todos os exports
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
// GERAR PACOTE DO SISTEMA (código + dados, SEM uploads)
// =============================================
if ($action === 'generate_system' || $action === 'pkg_system') {
    @set_time_limit(120);
    @ini_set('memory_limit', '256M');
    header('Content-Type: application/json');
    
    try {
        if (!is_dir($exportsDir)) mkdir($exportsDir, 0777, true);
        
        // Remover sistema anterior
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
        
        // 2. Adicionar arquivos do projeto (EXCLUINDO uploads, data, exports)
        $excludeDirs = ['data', '.git', '.vscode', 'node_modules', 'exports', 'uploads'];
        $excludeFiles = ['medic.zip'];
        
        function addDirToZipSystem($zip, $dirPath, $zipPath, $excludeDirs, $excludeFiles) {
            $files = @scandir($dirPath);
            if (!$files) return;
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $fullPath = $dirPath . '/' . $file;
                $relPath = ltrim($zipPath . '/' . $file, '/');
                if (is_dir($fullPath)) {
                    if (in_array($file, $excludeDirs)) continue;
                    addDirToZipSystem($zip, $fullPath, $relPath, $excludeDirs, $excludeFiles);
                } else {
                    if (in_array($file, $excludeFiles)) continue;
                    $zip->addFile($fullPath, $relPath);
                }
            }
        }
        
        addDirToZipSystem($zip, $baseDir, '', $excludeDirs, $excludeFiles);
        
        // Adicionar uploads/.htaccess vazio (para manter a pasta)
        $zip->addFromString('uploads/.htaccess', "Options -Indexes\nDeny from all\n");
        
        // 3. Config local
        $onlineUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $basePath = rtrim(str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')))), '/');
        $fullOnlineUrl = $onlineUrl . $basePath;
        
        $localConfig = file_get_contents(__DIR__ . '/../../config/database_local.php');
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
        $zip->deleteName('config/database_local.php');
        
        // 4. Scripts de inicialização
        $startBat = "@echo off\r\n"
            . "title MEDIC - Sistema Medico Local\r\n"
            . "color 0A\r\n"
            . "echo ============================================\r\n"
            . "echo   MEDIC - Sistema Medico Familiar (Local)\r\n"
            . "echo ============================================\r\n"
            . "echo.\r\n"
            . "\r\n"
            . ":: Verificar se PHP esta no PATH\r\n"
            . "where php >nul 2>nul\r\n"
            . "if %errorlevel% equ 0 (\r\n"
            . "    echo [OK] PHP encontrado no PATH do sistema.\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "\r\n"
            . ":: Verificar PHP portatil na pasta local\r\n"
            . "if exist \"%~dp0php\\php.exe\" (\r\n"
            . "    set \"PATH=%~dp0php;%PATH%\"\r\n"
            . "    echo [OK] PHP portatil encontrado na pasta local.\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "\r\n"
            . ":: Verificar caminhos comuns de instalacao do PHP\r\n"
            . "if exist \"C:\\php\\php.exe\" (\r\n"
            . "    set \"PATH=C:\\php;%PATH%\"\r\n"
            . "    echo [OK] PHP encontrado em C:\\php\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "if exist \"C:\\xampp\\php\\php.exe\" (\r\n"
            . "    set \"PATH=C:\\xampp\\php;%PATH%\"\r\n"
            . "    echo [OK] PHP encontrado em C:\\xampp\\php\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "if exist \"C:\\wamp64\\bin\\php\\php8*\\php.exe\" (\r\n"
            . "    for /d %%D in (C:\\wamp64\\bin\\php\\php8*) do set \"PATH=%%D;%PATH%\"\r\n"
            . "    echo [OK] PHP encontrado em WAMP.\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "if exist \"C:\\wamp\\bin\\php\\php8*\\php.exe\" (\r\n"
            . "    for /d %%D in (C:\\wamp\\bin\\php\\php8*) do set \"PATH=%%D;%PATH%\"\r\n"
            . "    echo [OK] PHP encontrado em WAMP.\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "if exist \"C:\\laragon\\bin\\php\\php-8*\\php.exe\" (\r\n"
            . "    for /d %%D in (C:\\laragon\\bin\\php\\php-8*) do set \"PATH=%%D;%PATH%\"\r\n"
            . "    echo [OK] PHP encontrado em Laragon.\r\n"
            . "    goto :start_server\r\n"
            . ")\r\n"
            . "\r\n"
            . ":: PHP nao encontrado - tentar instalar\r\n"
            . "echo [!] PHP nao encontrado no sistema.\r\n"
            . "echo.\r\n"
            . "echo Tentando instalacao automatica do PHP portatil...\r\n"
            . "echo.\r\n"
            . "powershell -ExecutionPolicy Bypass -File \"%~dp0setup.ps1\"\r\n"
            . "if %errorlevel% equ 0 (\r\n"
            . "    if exist \"%~dp0php\\php.exe\" (\r\n"
            . "        set \"PATH=%~dp0php;%PATH%\"\r\n"
            . "        goto :start_server\r\n"
            . "    )\r\n"
            . ")\r\n"
            . "\r\n"
            . "echo.\r\n"
            . "echo ============================================\r\n"
            . "echo [ERRO] PHP nao foi encontrado nem instalado.\r\n"
            . "echo.\r\n"
            . "echo Opcoes:\r\n"
            . "echo   1. Instale PHP de https://windows.php.net/download/\r\n"
            . "echo   2. Extraia o PHP em uma pasta \"php\" aqui dentro\r\n"
            . "echo   3. Ou adicione o PHP ao PATH do sistema\r\n"
            . "echo ============================================\r\n"
            . "pause\r\n"
            . "exit /b 1\r\n"
            . "\r\n"
            . ":start_server\r\n"
            . "echo.\r\n"
            . "echo Verificando extensoes PHP...\r\n"
            . "php -m 2>nul | findstr /i pdo_sqlite >nul 2>&1\r\n"
            . "if errorlevel 1 (\r\n"
            . "    echo [AVISO] pdo_sqlite nao encontrado. Configurando php.ini...\r\n"
            . "    for /f \"delims=\" %%P in ('php -r \"echo PHP_BINARY;\" 2^>nul') do set \"PHP_BIN=%%P\"\r\n"
            . "    for %%F in (\"%PHP_BIN%\") do set \"PHP_DIR=%%~dpF\"\r\n"
            . "    set \"INI_FILE=%PHP_DIR%php.ini\"\r\n"
            . "    echo extension_dir=%PHP_DIR%ext> \"%INI_FILE%\"\r\n"
            . "    echo extension=pdo_sqlite>> \"%INI_FILE%\"\r\n"
            . "    echo extension=sqlite3>> \"%INI_FILE%\"\r\n"
            . "    echo extension=mbstring>> \"%INI_FILE%\"\r\n"
            . "    echo extension=openssl>> \"%INI_FILE%\"\r\n"
            . "    echo extension=fileinfo>> \"%INI_FILE%\"\r\n"
            . "    echo extension=gd>> \"%INI_FILE%\"\r\n"
            . "    echo extension=curl>> \"%INI_FILE%\"\r\n"
            . "    echo php.ini configurado em: %INI_FILE%\r\n"
            . "    php -m 2>nul | findstr /i pdo_sqlite >nul 2>&1\r\n"
            . "    if errorlevel 1 (\r\n"
            . "        echo [ERRO] Nao foi possivel habilitar pdo_sqlite.\r\n"
            . "        echo Verifique se a pasta ext existe no diretorio do PHP.\r\n"
            . "        pause\r\n"
            . "        exit /b 1\r\n"
            . "    )\r\n"
            . ")\r\n"
            . "echo Extensoes OK.\r\n"
            . "echo.\r\n"
            . "echo Iniciando servidor local na porta 8080...\r\n"
            . "echo Acesse: http://localhost:8080\r\n"
            . "echo.\r\n"
            . "echo Para parar o servidor, feche esta janela.\r\n"
            . "echo ============================================\r\n"
            . "echo.\r\n"
            . "start http://localhost:8080/pages/login.php\r\n"
            . "php -S localhost:8080 -t \"%~dp0\"\r\n"
            . "pause\r\n";
        $zip->addFromString('start.bat', $startBat);
        
        $setupPs1 = '# MEDIC - Script de Instalacao do PHP Portatil
$ErrorActionPreference = "Continue"
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  MEDIC - Instalacao do PHP Portatil" -ForegroundColor Cyan  
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$phpDir = Join-Path $PSScriptRoot "php"

# Verificar se ja tem PHP portatil local
if (Test-Path (Join-Path $phpDir "php.exe")) {
    Write-Host "[OK] PHP portatil ja esta instalado em: $phpDir" -ForegroundColor Green
    exit 0
}

# Verificar se PHP esta no PATH
$phpInPath = Get-Command php -ErrorAction SilentlyContinue
if ($phpInPath) {
    Write-Host "[OK] PHP encontrado no PATH: $($phpInPath.Source)" -ForegroundColor Green
    exit 0
}

# Verificar caminhos comuns
$commonPaths = @("C:\php", "C:\xampp\php", "C:\laragon\bin\php")
foreach ($p in $commonPaths) {
    if (Test-Path (Join-Path $p "php.exe")) {
        Write-Host "[OK] PHP encontrado em: $p" -ForegroundColor Green
        Write-Host "     Adicione este caminho ao PATH ou copie a pasta para ca." -ForegroundColor Yellow
        exit 0
    }
}
# WAMP com versoes
$wampPaths = @("C:\wamp64\bin\php", "C:\wamp\bin\php")
foreach ($wp in $wampPaths) {
    if (Test-Path $wp) {
        $sub = Get-ChildItem $wp -Directory -Filter "php8*" | Select-Object -First 1
        if ($sub -and (Test-Path (Join-Path $sub.FullName "php.exe"))) {
            Write-Host "[OK] PHP encontrado em: $($sub.FullName)" -ForegroundColor Green
            exit 0
        }
    }
}

# Tentar baixar PHP portatil
Write-Host "[*] PHP nao encontrado. Tentando baixar PHP portatil..." -ForegroundColor Yellow
Write-Host ""

$phpZip = Join-Path $PSScriptRoot "php-portable.zip"

# Tentar multiplas URLs (versoes diferentes)
$urls = @(
    "https://windows.php.net/downloads/releases/php-8.4.7-nts-Win32-vs17-x64.zip",
    "https://windows.php.net/downloads/releases/php-8.3.20-nts-Win32-vs16-x64.zip",
    "https://windows.php.net/downloads/releases/php-8.2.28-nts-Win32-vs16-x64.zip",
    "https://windows.php.net/downloads/releases/latest/php-8.3-nts-Win32-vs16-x64-latest.zip",
    "https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64-latest.zip"
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$ProgressPreference = "SilentlyContinue"
$downloaded = $false

foreach ($url in $urls) {
    try {
        Write-Host "  Tentando: $url" -ForegroundColor Gray
        Invoke-WebRequest -Uri $url -OutFile $phpZip -UseBasicParsing -TimeoutSec 60
        if ((Test-Path $phpZip) -and (Get-Item $phpZip).Length -gt 1000000) {
            $downloaded = $true
            Write-Host "  [OK] Download concluido!" -ForegroundColor Green
            break
        } else {
            if (Test-Path $phpZip) { Remove-Item $phpZip -Force }
        }
    } catch {
        Write-Host "  [!] Falhou: $($_.Exception.Message)" -ForegroundColor DarkGray
        if (Test-Path $phpZip) { Remove-Item $phpZip -Force -ErrorAction SilentlyContinue }
    }
}

if (-not $downloaded) {
    Write-Host "" 
    Write-Host "[ERRO] Nao foi possivel baixar o PHP automaticamente." -ForegroundColor Red
    Write-Host ""
    Write-Host "Instale manualmente:" -ForegroundColor Yellow
    Write-Host "  1. Acesse https://windows.php.net/download/" -ForegroundColor White
    Write-Host "  2. Baixe a versao ''VS16 x64 Non Thread Safe'' (ZIP)" -ForegroundColor White
    Write-Host "  3. Extraia o conteudo em uma pasta ''php'' aqui dentro:" -ForegroundColor White
    Write-Host "     $phpDir" -ForegroundColor Cyan
    Write-Host "  4. Execute start.bat novamente" -ForegroundColor White
    Write-Host ""
    Read-Host "Pressione Enter para sair"
    exit 1
}

# Extrair
Write-Host "[*] Extraindo PHP..." -ForegroundColor Yellow
try {
    if (Test-Path $phpDir) { Remove-Item $phpDir -Recurse -Force }
    New-Item -ItemType Directory -Path $phpDir -Force | Out-Null
    Expand-Archive -Path $phpZip -DestinationPath $phpDir -Force
    Remove-Item $phpZip -Force -ErrorAction SilentlyContinue
} catch {
    Write-Host "[ERRO] Falha ao extrair: $($_.Exception.Message)" -ForegroundColor Red
    Read-Host "Pressione Enter para sair"
    exit 1
}

# Configurar php.ini
Write-Host "[*] Configurando php.ini..." -ForegroundColor Yellow
$phpIni = Join-Path $phpDir "php.ini"
$phpIniDev = Join-Path $phpDir "php.ini-development"
if (Test-Path $phpIniDev) { 
    Copy-Item $phpIniDev $phpIni -Force
} else { 
    New-Item -Path $phpIni -ItemType File -Force | Out-Null 
}

try {
    $ini = Get-Content $phpIni -Raw
    @("pdo_sqlite","sqlite3","mbstring","openssl","fileinfo","gd","curl") | ForEach-Object {
        $ini = $ini -replace ";extension=$_", "extension=$_"
    }
    $ini += "`n; MEDIC Local Mode`nupload_max_filesize=20M`npost_max_size=25M`nmax_execution_time=120`nmemory_limit=256M`nextension_dir=ext`n"
    Set-Content $phpIni $ini
} catch {
    Write-Host "[AVISO] Nao foi possivel configurar php.ini: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "[OK] PHP instalado com sucesso em: $phpDir" -ForegroundColor Green
Write-Host "     Execute start.bat para iniciar o MEDIC." -ForegroundColor Cyan
Write-Host ""
exit 0
';
        $zip->addFromString('setup.ps1', $setupPs1);
        
        // 5. Sync page
        $syncPageContent = @file_get_contents(__DIR__ . '/local_sync_local_template.php');
        if ($syncPageContent) {
            $zip->addFromString('pages/admin/local_sync.php', $syncPageContent);
        }
        
        // 6. Extras
        $zip->addFromString('.htaccess', "Options -Indexes\n");
        $zip->addFromString('data/.gitkeep', '');
        
        $zip->close();
        
        // Salvar token no banco
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
// GERAR PACOTES DE UPLOADS (~50MB cada)
// =============================================
if ($action === 'generate_uploads' || $action === 'pkg_uploads') {
    header('Content-Type: application/json');
    @set_time_limit(300);
    @ini_set('memory_limit', '256M');
    
    try {
        if (!is_dir($exportsDir)) mkdir($exportsDir, 0777, true);
        
        // Remover uploads anteriores
        foreach (glob($exportsDir . '/MEDIC_Uploads_*.zip') as $old) @unlink($old);
        
        $uploadsDir = realpath(__DIR__ . '/../../uploads');
        if (!$uploadsDir || !is_dir($uploadsDir)) {
            echo json_encode(['success' => true, 'packages' => [], 'message' => 'Nenhum arquivo de upload encontrado.']);
            exit;
        }
        
        // Coletar todos os arquivos de upload com tamanhos
        $allFiles = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) continue;
            $fullPath = $fileInfo->getRealPath();
            $relPath = str_replace($uploadsDir . DIRECTORY_SEPARATOR, '', $fullPath);
            $relPath = str_replace('\\', '/', $relPath);
            
            // Pular a pasta exports e .htaccess
            if (strpos($relPath, 'exports/') === 0) continue;
            if ($relPath === '.htaccess') continue;
            
            $allFiles[] = [
                'full' => $fullPath,
                'rel' => 'uploads/' . $relPath,
                'size' => $fileInfo->getSize()
            ];
        }
        
        if (empty($allFiles)) {
            echo json_encode(['success' => true, 'packages' => [], 'message' => 'Nenhum arquivo de upload encontrado.']);
            exit;
        }
        
        // Dividir em chunks de ~50MB
        $chunks = [];
        $currentChunk = [];
        $currentSize = 0;
        
        foreach ($allFiles as $file) {
            // Se um único arquivo é maior que o limite, ele vai sozinho no chunk
            if ($currentSize + $file['size'] > $maxChunkSize && !empty($currentChunk)) {
                $chunks[] = $currentChunk;
                $currentChunk = [];
                $currentSize = 0;
            }
            $currentChunk[] = $file;
            $currentSize += $file['size'];
        }
        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }
        
        $packages = [];
        $timestamp = date('Y-m-d_His');
        
        foreach ($chunks as $i => $chunk) {
            $partNum = $i + 1;
            $totalParts = count($chunks);
            $zipName = "MEDIC_Uploads_Parte{$partNum}de{$totalParts}_{$timestamp}.zip";
            $zipPath = $exportsDir . '/' . $zipName;
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                continue;
            }
            
            foreach ($chunk as $file) {
                $zip->addFile($file['full'], $file['rel']);
            }
            
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

// =============================================
// PÁGINA PADRÃO - Redirecionar para local_mode
// =============================================
redirect(baseUrl('pages/admin/local_mode.php'));