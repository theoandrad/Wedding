<?php
require_once __DIR__ . '/includes/funcoes.php';

$token = $_GET['token'] ?? '';
$convite = $token ? buscarConvitePorToken($token) : null;
$mensagemFlash = '';

if ($convite && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getPDO();

    if (isset($_POST['acao']) && $_POST['acao'] === 'rsvp') {
        $status = $_POST['status_rsvp'] ?? 'pendente';
        $pessoas = max(1, min((int)$_POST['pessoas'], (int)$convite['quantidade_pessoas']));
        $observacoes = trim($_POST['observacoes'] ?? '');

        $stmt = $pdo->prepare('UPDATE convites SET status_rsvp = :status, observacoes = :obs WHERE id = :id');
        $stmt->execute([
            ':status' => $status,
            ':obs' => $observacoes . " | Pessoas confirmadas: " . $pessoas,
            ':id' => $convite['id'],
        ]);

        $convite = buscarConvitePorToken($token);
        $mensagemFlash = 'Obrigado! Sua resposta foi registrada.';
    }

    if (isset($_POST['acao']) && $_POST['acao'] === 'recado') {
        $stmt = $pdo->prepare('INSERT INTO mensagens_convidados (convite_id, tipo, emoji, mensagem, created_at) VALUES (:convite, :tipo, :emoji, :mensagem, NOW())');
        $stmt->execute([
            ':convite' => $convite['id'],
            ':tipo' => $_POST['tipo'] ?? 'pre_evento',
            ':emoji' => $_POST['emoji'] ?? null,
            ':mensagem' => trim($_POST['mensagem'] ?? ''),
        ]);
        $mensagemFlash = 'Seu recado chegou aos noivos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Convite • Theo & Luísa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <?php if (!$convite): ?>
        <div class="alert alert-danger">
            Token inválido ou convite não encontrado. Entre em contato com o cerimonial.
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Theo & Luísa · 24/10/2026 · 17h</p>
                        <h2>Olá, <?= htmlspecialchars($convite['nome_impresso']); ?>!</h2>
                        <p>Estamos ansiosos para celebrar com você na Sede Campestre do Clube Comercial.</p>

                        <?php if ($mensagemFlash): ?>
                            <div class="alert alert-success"><?= $mensagemFlash; ?></div>
                        <?php endif; ?>

                        <hr>
                        <h5>Confirmação de presença</h5>
                        <p>Status atual: <strong><?= strtoupper($convite['status_rsvp']); ?></strong></p>

                        <form method="post" class="row g-3">
                            <input type="hidden" name="acao" value="rsvp">
                            <div class="col-md-6">
                                <label class="form-label">Você vai ao casamento?</label>
                                <select class="form-select" name="status_rsvp">
                                    <option value="vai" <?= $convite['status_rsvp'] === 'vai' ? 'selected' : ''; ?>>Vou celebrar com vocês</option>
                                    <option value="nao_vai" <?= $convite['status_rsvp'] === 'nao_vai' ? 'selected' : ''; ?>>Infelizmente não poderei ir</option>
                                    <option value="pendente" <?= $convite['status_rsvp'] === 'pendente' ? 'selected' : ''; ?>>Ainda estou confirmando</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantas pessoas?</label>
                                <input type="number" class="form-control" name="pessoas" min="1" max="<?= (int)$convite['quantidade_pessoas']; ?>" value="<?= (int)$convite['quantidade_pessoas']; ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observacoes" rows="2" placeholder="Restrições alimentares, recados..."></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary">Salvar resposta</button>
                            </div>
                        </form>

                        <?php if ($convite['mesa_numero']): ?>
                            <div class="alert alert-info mt-4">
                                Sua mesa será a <strong><?= $convite['mesa_numero']; ?></strong>.
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">
                        <h5>Deixe um recado</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="acao" value="recado">
                            <div class="col-md-4">
                                <label class="form-label">Quando ler?</label>
                                <select class="form-select" name="tipo">
                                    <option value="pre_evento">Antes do casamento</option>
                                    <option value="pos_evento">Depois da festa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emoji</label>
                                <select class="form-select" name="emoji">
                                    <option value="">Sem emoji</option>
                                    <option>💜</option>
                                    <option>🥂</option>
                                    <option>🌿</option>
                                    <option>🎶</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mensagem</label>
                                <textarea class="form-control" name="mensagem" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success">Enviar recado</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
