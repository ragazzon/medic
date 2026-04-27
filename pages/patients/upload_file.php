<?php
/**
 * AJAX endpoint: Upload generic file for a patient
 * POST: patient_id, comment, file
 * Returns JSON
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

header('Content-Type: application/json');

$pdo = getConnection();
$patientId = intval($_POST['patient_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if (!$patientId) {
    echo json_encode(['success' => false, 'error' => 'Paciente não informado.']);
    exit;
}

// Verificar se paciente existe
$stmt = $pdo->prepare("SELECT id FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Paciente não encontrado.']);
    exit;
}

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo enviado ou erro no upload.']);
    exit;
}

$file = $_FILES['file'];
$uploadDir = __DIR__ . '/../../uploads/patients/' . $patientId . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . $safeName;
$relativePath = 'uploads/patients/' . $patientId . '/' . $safeName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'error' => 'Erro ao mover arquivo.']);
    exit;
}

$mimeType = mime_content_type($destPath);
$fileSize = filesize($destPath);
$isImage = str_starts_with($mimeType, 'image/') ? 1 : 0;

$stmt = $pdo->prepare("INSERT INTO patient_files (patient_id, file_name, original_name, file_path, file_type, file_size, is_image, comment, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $patientId,
    $safeName,
    $file['name'],
    $relativePath,
    $mimeType,
    $fileSize,
    $isImage,
    $comment ?: null,
    getCurrentUserId()
]);

$newId = $pdo->lastInsertId();

// Retornar dados do arquivo criado para renderização no front
$newFile = $pdo->prepare("SELECT * FROM patient_files WHERE id = ?");
$newFile->execute([$newId]);
$f = $newFile->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'file' => [
        'id' => $f['id'],
        'original_name' => $f['original_name'],
        'file_path' => $f['file_path'],
        'file_type' => $f['file_type'],
        'file_size' => $f['file_size'],
        'is_image' => $f['is_image'],
        'comment' => $f['comment'],
        'created_at' => $f['created_at']
    ]
]);