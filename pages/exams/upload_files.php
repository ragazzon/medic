<?php
/**
 * AJAX endpoint para upload de arquivos de exames em lotes
 * Recebe arquivos em batches para evitar limites de PHP (max_file_uploads, post_max_size)
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

header('Content-Type: application/json');

$examId = intval($_POST['exam_id'] ?? 0);
if (!$examId) {
    echo json_encode(['success' => false, 'error' => 'ID do exame não informado']);
    exit;
}

$pdo = getConnection();

// Verificar se o exame existe
$stmt = $pdo->prepare("SELECT id FROM exams WHERE id = ?");
$stmt->execute([$examId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Exame não encontrado']);
    exit;
}

$uploadDir = __DIR__ . '/../../uploads/exams/' . $examId . '/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$uploaded = 0;
$errors = 0;

if (!empty($_FILES['files']['name'][0])) {
    foreach ($_FILES['files']['name'] as $i => $name) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $safeName = uniqid() . '_' . $i . '.' . $ext;
            $destPath = $uploadDir . $safeName;
            $relativePath = 'uploads/exams/' . $examId . '/' . $safeName;
            
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destPath)) {
                $mimeType = mime_content_type($destPath);
                $fileSize = filesize($destPath);
                $isImage = str_starts_with($mimeType, 'image/') ? 1 : 0;
                $pdo->prepare("INSERT INTO exam_files (exam_id, file_name, original_name, file_path, file_type, file_size, is_image) VALUES (?, ?, ?, ?, ?, ?, ?)")
                    ->execute([$examId, $safeName, $name, $relativePath, $mimeType, $fileSize, $isImage]);
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