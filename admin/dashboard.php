<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLogin();

$estatisticas = obterEstatisticasConvites();
$mensagens = buscarMensagensRecentes();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard • Theo & Luísa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin/dashboard.php">Theo & Luísa</a>
        <div class="d-flex gap-2">
            <a href="/admin/convites.php" class="btn btn-outline-light btn-sm">Convites</a>
            <a href="/admin/recepcao.php" class="btn btn-outline-light btn-sm">Recepção</a>
            <a href="/admin/mensagens.php" class="btn btn-outline-light btn-sm">Mensagens</a>
            <a href="/admin/logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h1 class="mb-4">Visão geral</h1>
    <div class="row g-3">
        <?php foreach (['total' => 'Convites', 'vai' => 'Confirmados', 'naoVai' => 'Não irão', 'pendente' => 'Pendentes', 'presentes' => 'Check-in'] as $chave => $titulo): ?>
            <div class="col-md-4 col-lg-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted"><?= $titulo; ?></h6>
                        <p class="display-6"><?= $estatisticas[$chave]; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-5">
        <h4>Últimos recados</h4>
        <div class="list-group">
            <?php foreach ($mensagens as $mensagem): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($mensagem['nome_impresso']); ?> <?= $mensagem['emoji']; ?></strong>
                        <small><?= date('d/m H:i', strtotime($mensagem['created_at'])); ?></small>
                    </div>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($mensagem['mensagem'])); ?></p>
                    <span class="badge bg-secondary"><?= $mensagem['tipo'] === 'pre_evento' ? 'Pré-evento' : 'Pós-evento'; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="/admin/mensagens.php" class="btn btn-link mt-2">Ver todas</a>
    </div>
</div>
</body>
</html>
