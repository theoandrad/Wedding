<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLogin();

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE mensagens_convidados SET lida = :lida, favorita = :favorita WHERE id = :id");
    $stmt->execute([
        ':lida' => isset($_POST['lida']) ? 1 : 0,
        ':favorita' => isset($_POST['favorita']) ? 1 : 0,
        ':id' => (int)$_POST['id'],
    ]);
}

$filtro = $_GET['filtro'] ?? 'todas';
$sql = 'SELECT m.*, c.nome_impresso FROM mensagens_convidados m JOIN convites c ON c.id = m.convite_id';
if ($filtro === 'nao_lidas') {
    $sql .= " WHERE m.lida = 0";
}
$sql .= ' ORDER BY m.created_at DESC';
$mensagens = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens • Theo & Luísa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Mensagens</h1>
        <a href="/admin/dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="mb-3">
        <a href="?filtro=todas" class="btn btn-sm <?= $filtro === 'todas' ? 'btn-primary' : 'btn-outline-primary'; ?>">Todas</a>
        <a href="?filtro=nao_lidas" class="btn btn-sm <?= $filtro === 'nao_lidas' ? 'btn-primary' : 'btn-outline-primary'; ?>">Não lidas</a>
    </div>

    <div class="list-group">
        <?php foreach ($mensagens as $mensagem): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong><?= htmlspecialchars($mensagem['nome_impresso']); ?></strong> <?= $mensagem['emoji']; ?>
                        <span class="badge bg-secondary"><?= $mensagem['tipo'] === 'pre_evento' ? 'Pré-evento' : 'Pós-evento'; ?></span>
                        <?php if ($mensagem['favorita']): ?><span class="badge bg-warning text-dark">Favorita</span><?php endif; ?>
                    </div>
                    <small><?= date('d/m/Y H:i', strtotime($mensagem['created_at'])); ?></small>
                </div>
                <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($mensagem['mensagem'])); ?></p>
                <form method="post" class="d-flex align-items-center gap-3">
                    <input type="hidden" name="id" value="<?= $mensagem['id']; ?>">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="lida" <?= $mensagem['lida'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Marcar como lida</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="favorita" <?= $mensagem['favorita'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Favorita</span>
                    </label>
                    <button class="btn btn-sm btn-outline-primary">Salvar</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
