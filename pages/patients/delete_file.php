<?php
/**
 * AJAX endpoint: Delete a generic patient file
 * POST: file_id
 * Returns JSON
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

header('Content-Type: application/json');

$pdo = getConnection();
$fileId = intval($_POST['file_id'] ?? 0);

if (!$fileId) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não informado.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM patient_files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch();

if (!$file) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não encontrado.']);
    exit;
}

// Remover arquivo físico
$filePath = __DIR__ . '/../../' . $file['file_path'];
if (file_exists($filePath)) {
    unlink($filePath);
}

// Remover do banco
$pdo->prepare("DELETE FROM patient_files WHERE id = ?")->execute([$fileId]);

echo json_encode(['success' => true]);