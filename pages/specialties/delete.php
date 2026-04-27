<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    setFlash('danger', 'Especialidade inválida.');
    redirect(baseUrl('pages/specialties/list.php'));
}

$stmt = $pdo->prepare("SELECT * FROM specialties WHERE id = ?");
$stmt->execute([$id]);
$specialty = $stmt->fetch();

if (!$specialty) {
    setFlash('danger', 'Especialidade não encontrada.');
    redirect(baseUrl('pages/specialties/list.php'));
}

// Deletar especialidade (os registros que a usam mantêm o texto)
$pdo->prepare("DELETE FROM specialties WHERE id = ?")->execute([$id]);

setFlash('success', 'Especialidade "' . $specialty['name'] . '" excluída.');
redirect(baseUrl('pages/specialties/list.php'));