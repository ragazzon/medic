<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    setFlash('danger', 'Prontuário não encontrado.');
    redirect(baseUrl('pages/records/list.php'));
}

// Excluir arquivos físicos
$files = $pdo->prepare("SELECT * FROM record_files WHERE record_id = ?");
$files->execute([$id]);
foreach ($files->fetchAll() as $f) {
    $path = __DIR__ . '/../../' . $f['file_path'];
    if (file_exists($path)) unlink($path);
}

$pdo->prepare("DELETE FROM record_files WHERE record_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM medical_records WHERE id = ?")->execute([$id]);

setFlash('success', 'Prontuário excluído com sucesso.');
redirect(baseUrl('pages/records/list.php'));