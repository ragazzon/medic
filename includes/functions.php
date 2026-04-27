<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitiza input do usuário
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redireciona para uma URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Define mensagem flash na sessão
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Recupera e limpa mensagem flash
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Retorna o ID do usuário logado
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Retorna os dados do usuário logado
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

/**
 * Verifica se é admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Detecta se estamos usando SQLite
 */
function isSQLite() {
    return defined('LOCAL_MODE') && LOCAL_MODE;
}

/**
 * SQL cross-DB: NOW()
 */
function sqlNow() {
    return isSQLite() ? "datetime('now')" : "NOW()";
}

/**
 * SQL cross-DB: CURDATE()
 */
function sqlCurdate() {
    return isSQLite() ? "date('now')" : "CURDATE()";
}

/**
 * SQL cross-DB: DATE_SUB(NOW(), INTERVAL x MONTH/YEAR)
 */
function sqlDateSub($interval) {
    if (!$interval) return null;
    // Parse "6 MONTH", "3 YEAR", etc.
    if (preg_match('/(\d+)\s*(MONTH|YEAR|DAY)/i', $interval, $m)) {
        $num = $m[1];
        $unit = strtolower($m[2]) . 's'; // months, years, days
        if (isSQLite()) {
            return "datetime('now', '-{$num} {$unit}')";
        }
        return "DATE_SUB(NOW(), INTERVAL {$num} " . strtoupper($m[2]) . ")";
    }
    return isSQLite() ? "datetime('now')" : "NOW()";
}

/**
 * SQL cross-DB: DATE_FORMAT(col, '%Y-%m')
 */
function sqlDateFormat($col, $format) {
    if (isSQLite()) {
        // Convert MySQL format to SQLite strftime format
        return "strftime('{$format}', {$col})";
    }
    return "DATE_FORMAT({$col}, '{$format}')";
}

/**
 * SQL cross-DB: YEAR(col)
 */
function sqlYear($col) {
    if (isSQLite()) {
        return "CAST(strftime('%Y', {$col}) AS INTEGER)";
    }
    return "YEAR({$col})";
}

/**
 * Formata data para exibição (dd/mm/yyyy)
 */
function formatDate($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

/**
 * Formata data e hora para exibição
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Retorna nome do mês em português abreviado
 */
function monthNamePtBr($monthNum) {
    $months = [
        1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
        5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
        9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
    ];
    return $months[(int)$monthNum] ?? '';
}

/**
 * Formata mês/ano em PT-BR (ex: "2026-01" → "Jan/2026")
 */
function formatMonthYear($yearMonth) {
    $parts = explode('-', $yearMonth);
    if (count($parts) < 2) return $yearMonth;
    return monthNamePtBr((int)$parts[1]) . '/' . $parts[0];
}

/**
 * Converte data DD/MM/YYYY para YYYY-MM-DD (MySQL)
 */
function dateToDb($dateBr) {
    if (empty($dateBr)) return '';
    // Se já está no formato YYYY-MM-DD, retorna direto
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateBr)) return $dateBr;
    $parts = explode('/', $dateBr);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $dateBr;
}

/**
 * Converte data YYYY-MM-DD para DD/MM/YYYY (exibição em forms)
 */
function dateToForm($dateDb) {
    if (empty($dateDb)) return '';
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateDb)) return $dateDb;
    return date('d/m/Y', strtotime($dateDb));
}

/**
 * Calcula idade a partir da data de nascimento
 */
function calculateAge($birthDate) {
    if (empty($birthDate)) return '';
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    $age = $today->diff($birth);
    return $age->y;
}

/**
 * Gera nome único para arquivo
 */
function generateFileName($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid('file_', true) . '.' . strtolower($ext);
}

/**
 * Verifica se o tipo de arquivo é imagem
 */
function isImageFile($mimeType) {
    return in_array($mimeType, ALLOWED_IMAGE_TYPES);
}

/**
 * Upload de arquivo
 */
function uploadFile($file, $destination) {
    $uploadDir = __DIR__ . '/../uploads/' . $destination . '/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erro no upload do arquivo'];
    }
    
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'error' => 'Arquivo muito grande (máx. 10MB)'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não permitido'];
    }
    
    $newName = generateFileName($file['name']);
    $filePath = $uploadDir . $newName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return [
            'success' => true,
            'file_name' => $newName,
            'original_name' => $file['name'],
            'file_path' => 'uploads/' . $destination . '/' . $newName,
            'file_type' => $mimeType,
            'file_size' => $file['size'],
            'is_image' => isImageFile($mimeType) ? 1 : 0
        ];
    }
    
    return ['success' => false, 'error' => 'Falha ao salvar o arquivo'];
}

/**
 * Remove arquivo do servidor
 */
function deleteFile($filePath) {
    $fullPath = __DIR__ . '/../' . $filePath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Formata tamanho de arquivo
 */
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2, ',', '.') . ' KB';
    }
    return $bytes . ' bytes';
}

/**
 * Retorna as categorias de prontuário
 */
function getRecordCategories() {
    return [
        'Consulta',
        'Internação',
        'Cirurgia',
        'Acompanhamento',
        'Emergência',
        'Retorno',
        'Check-up',
        'Vacinação',
        'Outro'
    ];
}

/**
 * Retorna os tipos de exame
 */
function getExamTypes() {
    return [
        'Sangue',
        'Urina',
        'Fezes',
        'Imagem',
        'Tomografia',
        'Ressonância Magnética',
        'Raio-X',
        'Ultrassom',
        'Eletrocardiograma',
        'Endoscopia',
        'Colonoscopia',
        'Biópsia',
        'Audiometria',
        'Oftalmológico',
        'Outro'
    ];
}

/**
 * Retorna classe CSS baseada no status
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Normal': return 'bg-success';
        case 'Alterado': return 'bg-danger';
        case 'Aguardando': return 'bg-warning text-dark';
        case 'Indefinido': return 'bg-secondary';
        default: return 'bg-secondary';
    }
}

/**
 * Retorna a URL base do site
 */
function baseUrl($path = '') {
    return SITE_URL . ltrim($path, '/');
}

/**
 * Conta registros em uma tabela com condição opcional
 */
function countRecords($table, $where = '', $params = []) {
    $pdo = getConnection();
    $sql = "SELECT COUNT(*) as total FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch()['total'];
}

/**
 * Retorna IDs dos pacientes que o usuário pode ver
 * Admin vê todos; user vê apenas os associados
 */
function getAllowedPatientIds($userId = null) {
    if (isAdmin()) return null; // null = sem restrição
    $userId = $userId ?: getCurrentUserId();
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT patient_id FROM user_patients WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Verifica se o usuário pode acessar determinado paciente
 */
function canAccessPatient($patientId, $userId = null) {
    if (isAdmin()) return true;
    $userId = $userId ?: getCurrentUserId();
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_patients WHERE user_id = ? AND patient_id = ?");
    $stmt->execute([$userId, $patientId]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Gera cláusula SQL para filtrar pacientes por acesso do usuário
 * Retorna [whereClause, params]
 */
function getPatientAccessFilter($alias = 'p') {
    if (isAdmin()) return ['', []];
    $ids = getAllowedPatientIds();
    if (empty($ids)) return ["{$alias}.id IN (0)", []]; // nenhum acesso
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    return ["{$alias}.id IN ({$placeholders})", $ids];
}

/**
 * Detecta a categoria de um arquivo pelo MIME type
 * Retorna: 'image', 'pdf', 'word', 'audio', 'video', 'other'
 */
function getFileCategory($mimeType, $fileName = '', $filePath = '') {
    // 1) Check MIME type first
    if (str_starts_with($mimeType, 'image/')) return 'image';
    if ($mimeType === 'application/pdf') return 'pdf';
    if ($mimeType === 'application/msword' 
        || str_starts_with($mimeType, 'application/vnd.openxmlformats-officedocument.word')) return 'word';
    if (str_starts_with($mimeType, 'audio/')) return 'audio';
    if (str_starts_with($mimeType, 'video/')) return 'video';

    // 2) Always fallback to file extension regardless of MIME type
    // Try fileName first, then filePath
    $names = array_filter([$fileName, $filePath]);
    foreach ($names as $n) {
        $ext = strtolower(pathinfo($n, PATHINFO_EXTENSION));
        if (in_array($ext, ['doc', 'docx'])) return 'word';
        if ($ext === 'pdf') return 'pdf';
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'])) return 'audio';
        if (in_array($ext, ['mp4', 'webm', 'ogv', 'mov', 'avi', 'mkv'])) return 'video';
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) return 'image';
    }

    return 'other';
}

/**
 * Retorna o ícone Bootstrap Icons para cada tipo de arquivo
 */
function getFileIcon($mimeType, $fileName = '', $filePath = '') {
    $cat = getFileCategory($mimeType, $fileName, $filePath);
    switch ($cat) {
        case 'image': return 'bi-image';
        case 'pdf': return 'bi-file-earmark-pdf';
        case 'word': return 'bi-file-earmark-word';
        case 'audio': return 'bi-file-earmark-music';
        case 'video': return 'bi-file-earmark-play';
        default: return 'bi-file-earmark';
    }
}

/**
 * Renderiza preview inline de um arquivo
 * Suporta: imagens, PDF (iframe), Word (Google Viewer), áudio, vídeo
 * $file = array com file_path, file_name, file_type
 * $size = 'sm' (sidebar), 'lg' (main area)
 */
function renderFilePreview($file, $size = 'lg') {
    $url = baseUrl($file['file_path']);
    $name = sanitize($file['file_name']);
    $mime = $file['file_type'];
    $fname = $file['file_name'] ?? $file['original_name'] ?? '';
    $fpath = $file['file_path'] ?? '';
    $cat = getFileCategory($mime, $fname, $fpath);
    $icon = getFileIcon($mime, $fname, $fpath);
    $height = ($size === 'sm') ? '200px' : '500px';
    $uniqueId = 'preview-' . md5($file['file_path']);

    $html = '<div class="file-preview-container mb-3" id="' . $uniqueId . '">';

    switch ($cat) {
        case 'image':
            $html .= '<div class="file-preview-image text-center">';
            $html .= '<img src="' . $url . '" class="img-fluid rounded" alt="' . $name . '" style="max-height:' . $height . ';cursor:pointer;" onclick="window.open(\'' . $url . '\', \'_blank\')">';
            $html .= '</div>';
            break;

        case 'pdf':
            $html .= '<div class="file-preview-pdf">';
            $html .= '<iframe src="' . $url . '#toolbar=1&navpanes=0" style="width:100%;height:' . $height . ';border:1px solid #dee2e6;border-radius:0.375rem;" allowfullscreen></iframe>';
            $html .= '</div>';
            break;

        case 'word':
            // Use Mammoth.js for client-side DOCX rendering
            $containerId = 'docx-' . md5($file['file_path']);
            $html .= '<div class="file-preview-word">';
            $html .= '<div class="alert alert-info mb-2"><i class="bi bi-file-earmark-word me-2"></i><strong>' . $name . '</strong></div>';
            $html .= '<div id="' . $containerId . '" class="docx-preview-content" style="max-height:' . $height . ';overflow-y:auto;border:1px solid #dee2e6;border-radius:0.375rem;padding:20px;background:#fff;">';
            $html .= '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Carregando documento...</p></div>';
            $html .= '</div>';
            $html .= '<script>';
            $html .= '(function(){';
            $html .= 'fetch("' . $url . '")';
            $html .= '.then(function(r){return r.arrayBuffer()})';
            $html .= '.then(function(buf){return mammoth.convertToHtml({arrayBuffer:buf})})';
            $html .= '.then(function(result){';
            $html .= 'var el=document.getElementById("' . $containerId . '");';
            $html .= 'el.innerHTML=result.value;';
            $html .= 'el.style.fontSize="14px";';
            $html .= 'el.style.lineHeight="1.6";';
            $html .= '})';
            $html .= '.catch(function(err){';
            $html .= 'document.getElementById("' . $containerId . '").innerHTML=';
            $html .= '"<div class=\'text-center text-muted py-3\'><i class=\'bi bi-exclamation-triangle fs-3 d-block mb-2\'></i>Não foi possível carregar o documento.<br><small>Faça o download para visualizar.</small></div>";';
            $html .= '});';
            $html .= '})();';
            $html .= '</script>';
            $html .= '</div>';
            break;

        case 'audio':
            $html .= '<div class="file-preview-audio">';
            $html .= '<div class="d-flex align-items-center gap-3 p-3 bg-light rounded border">';
            $html .= '<i class="bi bi-file-earmark-music fs-1 text-primary"></i>';
            $html .= '<div class="flex-grow-1">';
            $html .= '<div class="fw-semibold mb-2">' . $name . '</div>';
            $html .= '<audio controls preload="metadata" style="width:100%;">';
            $html .= '<source src="' . $url . '" type="' . sanitize($mime) . '">';
            $html .= 'Seu navegador não suporta o player de áudio.';
            $html .= '</audio>';
            $html .= '</div></div></div>';
            break;

        case 'video':
            $html .= '<div class="file-preview-video">';
            $html .= '<video controls preload="metadata" style="width:100%;max-height:' . $height . ';border-radius:0.375rem;background:#000;">';
            $html .= '<source src="' . $url . '" type="' . sanitize($mime) . '">';
            $html .= 'Seu navegador não suporta o player de vídeo.';
            $html .= '</video>';
            $html .= '</div>';
            break;

        default:
            $html .= '<div class="file-preview-generic">';
            $html .= '<div class="d-flex align-items-center gap-2 p-3 bg-light rounded border">';
            $html .= '<i class="bi ' . $icon . ' fs-3"></i>';
            $html .= '<span>' . $name . '</span>';
            $html .= '</div></div>';
            break;
    }

    // Download button always present
    $html .= '<div class="file-preview-actions mt-2 d-flex align-items-center justify-content-between">';
    $html .= '<small class="text-muted text-truncate"><i class="bi ' . $icon . ' me-1"></i>' . $name . '</small>';
    $html .= '<a href="' . $url . '" download="' . $name . '" class="btn btn-sm btn-outline-primary" title="Baixar arquivo"><i class="bi bi-download me-1"></i>Baixar</a>';
    $html .= '</div>';

    $html .= '</div>';
    return $html;
}
