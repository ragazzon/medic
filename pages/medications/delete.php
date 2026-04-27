<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlash('danger', 'Medicamento não encontrado.');
    redirect(baseUrl('pages/medications/list.php'));
}

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM medications WHERE id = ?");
$stmt->execute([$id]);
$medication = $stmt->fetch();

if (!$medication) {
    setFlash('danger', 'Medicamento não encontrado.');
    redirect(baseUrl('pages/medications/list.php'));
}

$pdo->prepare("DELETE FROM medications WHERE id = ?")->execute([$id]);
setFlash('success', 'Medicamento excluído com sucesso!');
redirect(baseUrl('pages/medications/list.php'));