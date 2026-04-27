<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$id]);
$exam = $stmt->fetch();

if (!$exam) {
    setFlash('danger', 'Exame não encontrado.');
    redirect(baseUrl('pages/exams/list.php'));
}

$files = $pdo->prepare("SELECT * FROM exam_files WHERE exam_id = ?");
$files->execute([$id]);
foreach ($files->fetchAll() as $f) {
    $path = __DIR__ . '/../../' . $f['file_path'];
    if (file_exists($path)) unlink($path);
}

$pdo->prepare("DELETE FROM exam_files WHERE exam_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([$id]);

setFlash('success', 'Exame excluído com sucesso.');
redirect(baseUrl('pages/exams/list.php'));