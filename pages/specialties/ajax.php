<?php
/**
 * AJAX endpoint para busca e criação de especialidades
 * GET  ?q=texto  → retorna JSON com especialidades que contêm o texto
 * POST name=texto → cria nova especialidade e retorna JSON
 */
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');
$pdo = getConnection();

// GET — busca
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = trim($_GET['q'] ?? '');
    if (strlen($q) === 0) {
        $rows = $pdo->query("SELECT id, name FROM specialties ORDER BY name")->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT id, name FROM specialties WHERE name LIKE ? ORDER BY name LIMIT 20");
        $stmt->execute(["%{$q}%"]);
        $rows = $stmt->fetchAll();
    }
    echo json_encode($rows);
    exit;
}

// POST — criar nova especialidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório.']);
        exit;
    }

    // Verificar se já existe (case-insensitive)
    $stmt = $pdo->prepare("SELECT id, name FROM specialties WHERE LOWER(name) = LOWER(?)");
    $stmt->execute([$name]);
    $existing = $stmt->fetch();

    if ($existing) {
        echo json_encode($existing);
        exit;
    }

    // Criar
    $pdo->prepare("INSERT INTO specialties (name) VALUES (?)")->execute([$name]);
    $newId = $pdo->lastInsertId();
    echo json_encode(['id' => (int)$newId, 'name' => $name]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método não permitido.']);