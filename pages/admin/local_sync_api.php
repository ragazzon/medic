<?php
/**
 * MEDIC - API de Sincronização (Servidor Online)
 * Recebe dados do modo local e sincroniza com o banco MySQL
 */
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Aumentar limites para sincronização
@set_time_limit(600);
@ini_set('max_execution_time', '600');
@ini_set('memory_limit', '256M');
@ini_set('post_max_size', '50M');
@ini_set('upload_max_filesize', '50M');

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Ler payload
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

if (!$payload || !isset($payload['token']) || !isset($payload['data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Payload inválido']);
    exit;
}

$pdo = getConnection();

// Validar token
try {
    $stmt = $pdo->prepare("SELECT * FROM sync_tokens WHERE token = ? AND used_at IS NULL");
    $stmt->execute([$payload['token']]);
    $tokenRecord = $stmt->fetch();
    
    if (!$tokenRecord) {
        // Verificar se já foi usado (token reutilizável)
        $stmt = $pdo->prepare("SELECT * FROM sync_tokens WHERE token = ?");
        $stmt->execute([$payload['token']]);
        $tokenRecord = $stmt->fetch();
        
        if (!$tokenRecord) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Token de sincronização inválido']);
            exit;
        }
    }
    
    // Marcar token como usado
    $stmt = $pdo->prepare("UPDATE sync_tokens SET used_at = NOW() WHERE id = ?");
    $stmt->execute([$tokenRecord['id']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao validar token: ' . $e->getMessage()]);
    exit;
}

// Definir tabelas e suas colunas (para UPSERT seguro)
$tableColumns = [
    'users' => ['id','name','email','password','role','created_at','updated_at'],
    'patients' => ['id','name','birth_date','gender','cpf','blood_type','relationship','phone','email','address','allergies','chronic_conditions','medications','health_insurance','insurance_number','notes','photo','created_by','created_at','updated_at'],
    'medical_records' => ['id','patient_id','title','description','diagnosis','symptoms','prescription','doctor_name','specialty','clinic_hospital','record_date','category','notes','created_by','created_at','updated_at'],
    'exams' => ['id','patient_id','title','exam_type','specialty','exam_date','lab_clinic','doctor_name','results','notes','status','created_by','created_at','updated_at'],
    'exam_files' => ['id','exam_id','file_name','original_name','file_path','file_type','file_size','is_image','uploaded_at'],
    'record_files' => ['id','record_id','file_name','original_name','file_path','file_type','file_size','is_image','uploaded_at'],
    'user_patients' => ['id','user_id','patient_id','created_at'],
    'medications' => ['id','patient_id','name','active_ingredient','dosage','frequency','route','start_date','end_date','prescriber','specialty','reason','instructions','side_effects','is_continuous','is_active','notes','created_by','created_at','updated_at'],
    'specialties' => ['id','name','description','icon','color','is_active','created_at'],
];

// Ordem de importação (respeitando foreign keys)
$importOrder = ['users','patients','user_patients','medical_records','exams','exam_files','record_files','medications','specialties'];

$stats = [];
$errors = [];

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->beginTransaction();
    
    foreach ($importOrder as $table) {
        if (!isset($payload['data'][$table]) || !isset($tableColumns[$table])) continue;
        
        $rows = $payload['data'][$table];
        $cols = $tableColumns[$table];
        $inserted = 0;
        $updated = 0;
        
        foreach ($rows as $row) {
            // Filtrar apenas colunas conhecidas
            $filteredRow = [];
            foreach ($cols as $col) {
                $filteredRow[$col] = $row[$col] ?? null;
            }
            
            $id = $filteredRow['id'] ?? null;
            if ($id === null) continue;
            
            // Verificar se já existe
            $checkStmt = $pdo->prepare("SELECT id FROM `{$table}` WHERE id = ?");
            $checkStmt->execute([$id]);
            $exists = $checkStmt->fetch();
            
            if ($exists) {
                // UPDATE
                $updateCols = [];
                $updateParams = [];
                foreach ($filteredRow as $col => $val) {
                    if ($col === 'id') continue;
                    // Não sobrescrever senhas de usuários existentes com hash diferente
                    if ($table === 'users' && $col === 'password') continue;
                    $updateCols[] = "`{$col}` = ?";
                    $updateParams[] = $val;
                }
                $updateParams[] = $id;
                $sql = "UPDATE `{$table}` SET " . implode(', ', $updateCols) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($updateParams);
                $updated++;
            } else {
                // INSERT
                $colNames = implode(',', array_map(fn($c) => "`{$c}`", array_keys($filteredRow)));
                $placeholders = implode(',', array_fill(0, count($filteredRow), '?'));
                $sql = "INSERT INTO `{$table}` ({$colNames}) VALUES ({$placeholders})";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($filteredRow));
                $inserted++;
            }
        }
        
        $stats[$table] = ['inserted' => $inserted, 'updated' => $updated];
    }
    
    $pdo->commit();
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
} catch (Exception $e) {
    $pdo->rollBack();
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    $errors[] = $e->getMessage();
}

// Sincronizar arquivos de upload
$filesSynced = 0;
if (isset($payload['files']) && is_array($payload['files'])) {
    $uploadsDir = realpath(__DIR__ . '/../../uploads');
    if (!$uploadsDir) {
        $uploadsDir = __DIR__ . '/../../uploads';
        @mkdir($uploadsDir, 0777, true);
    }
    
    foreach ($payload['files'] as $relPath => $base64Content) {
        $targetPath = $uploadsDir . '/' . $relPath;
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        $content = base64_decode($base64Content);
        if ($content !== false) {
            file_put_contents($targetPath, $content);
            $filesSynced++;
        }
    }
}

// Resposta
$totalInserted = array_sum(array_column($stats, 'inserted'));
$totalUpdated = array_sum(array_column($stats, 'updated'));

$result = [
    'success' => empty($errors),
    'message' => "Sincronizado: {$totalInserted} novos registros, {$totalUpdated} atualizados, {$filesSynced} arquivos.",
    'stats' => $stats,
    'files_synced' => $filesSynced,
    'synced_at' => date('Y-m-d H:i:s')
];

if (!empty($errors)) {
    $result['errors'] = $errors;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);