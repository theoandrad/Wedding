<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLogin();

header('Content-Type: application/json');
$pdo = getPDO();
$acao = $_POST['acao'] ?? '';

if ($acao === 'buscar_token') {
    $token = $_POST['token'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM convites WHERE token = :token');
    $stmt->execute([':token' => $token]);
    $convite = $stmt->fetch();
    echo json_encode(['convite' => $convite]);
    exit;
}

if ($acao === 'buscar_id') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM convites WHERE id = :id');
    $stmt->execute([':id' => $id]);
    echo json_encode(['convite' => $stmt->fetch()]);
    exit;
}

if ($acao === 'buscar_nome') {
    $nome = '%' . ($_POST['nome'] ?? '') . '%';
    $stmt = $pdo->prepare('SELECT id, nome_impresso, quantidade_pessoas, status_rsvp, status_checkin, mesa_numero FROM convites WHERE nome_impresso LIKE :nome ORDER BY nome_impresso LIMIT 10');
    $stmt->execute([':nome' => $nome]);
    echo json_encode(['resultados' => $stmt->fetchAll()]);
    exit;
}

if ($acao === 'checkin') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("UPDATE convites SET status_checkin = 'presente' WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['sucesso' => true]);
    exit;
}

echo json_encode(['erro' => 'Ação inválida']);
