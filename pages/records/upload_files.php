<?php
/**
 * AJAX endpoint para upload de arquivos de prontuários em lotes
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

header('Content-Type: application/json');

$recordId = intval($_POST['record_id'] ?? 0);
if (!$recordId) {
    echo json_encode(['success' => false, 'error' => 'ID do prontuário não informado']);
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("SELECT id FROM medical_records WHERE id = ?");
$stmt->execute([$recordId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Prontuário não encontrado']);
    exit;
}

$uploadDir = __DIR__ . '/../../uploads/records/' . $recordId . '/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$uploaded = 0;
$errors = 0;

if (!empty($_FILES['files']['name'][0])) {
    foreach ($_FILES['files']['name'] as $i => $name) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $safeName = uniqid() . '_' . $i . '.' . $ext;
            $destPath = $uploadDir . $safeName;
            $relativePath = 'uploads/records/' . $recordId . '/' . $safeName;
            
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destPath)) {
                $mimeType = mime_content_type($destPath);
                $fileSize = filesize($destPath);
                $isImage = str_starts_with($mimeType, 'image/') ? 1 : 0;
                $pdo->prepare("INSERT INTO record_files (record_id, file_name, original_name, file_path, file_type, file_size, is_image) VALUES (?, ?, ?, ?, ?, ?, ?)")
                    ->execute([$recordId, $safeName, $name, $relativePath, $mimeType, $fileSize, $isImage]);
                $uploaded++;
            } else {
                $errors++;
            }
        } else {
            $errors++;
        }
    }
}

echo json_encode([
    'success' => true,
    'uploaded' => $uploaded,
    'errors' => $errors
]);