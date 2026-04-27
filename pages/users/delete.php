<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

if ($id == getCurrentUserId()) {
    setFlash('danger', 'Você não pode excluir sua própria conta.');
    redirect(baseUrl('pages/users/list.php'));
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('danger', 'Usuário não encontrado.');
    redirect(baseUrl('pages/users/list.php'));
}

// Remover associações e o usuário
$pdo->prepare("DELETE FROM user_patients WHERE user_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);

setFlash('success', 'Usuário "' . $user['name'] . '" excluído com sucesso.');
redirect(baseUrl('pages/users/list.php'));