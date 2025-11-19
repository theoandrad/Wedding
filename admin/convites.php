<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLogin();

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'adicionar') {
        $stmt = $pdo->prepare('INSERT INTO convites (token, nome_impresso, quantidade_pessoas, mesa_numero) VALUES (:token, :nome, :quantidade, :mesa)');
        $stmt->execute([
            ':token' => gerarToken(),
            ':nome' => trim($_POST['nome_impresso']),
            ':quantidade' => (int)$_POST['quantidade_pessoas'],
            ':mesa' => $_POST['mesa_numero'] !== '' ? (int)$_POST['mesa_numero'] : null,
        ]);
    }
    if ($acao === 'editar') {
        $stmt = $pdo->prepare('UPDATE convites SET nome_impresso = :nome, quantidade_pessoas = :quantidade, mesa_numero = :mesa, status_rsvp = :status, status_checkin = :checkin WHERE id = :id');
        $stmt->execute([
            ':nome' => trim($_POST['nome_impresso']),
            ':quantidade' => (int)$_POST['quantidade_pessoas'],
            ':mesa' => $_POST['mesa_numero'] !== '' ? (int)$_POST['mesa_numero'] : null,
            ':status' => $_POST['status_rsvp'],
            ':checkin' => $_POST['status_checkin'],
            ':id' => (int)$_POST['id'],
        ]);
    }
    if ($acao === 'excluir') {
        $stmt = $pdo->prepare('DELETE FROM convites WHERE id = :id');
        $stmt->execute([':id' => (int)$_POST['id']]);
    }
}

$convites = $pdo->query('SELECT * FROM convites ORDER BY nome_impresso')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Convites • Theo & Luísa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Convites</h1>
        <a href="/admin/dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Adicionar convite</h5>
                    <form method="post" class="row g-3">
                        <input type="hidden" name="acao" value="adicionar">
                        <div class="col-12">
                            <label class="form-label">Nome impresso</label>
                            <input type="text" class="form-control" name="nome_impresso" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantidade de pessoas</label>
                            <input type="number" class="form-control" name="quantidade_pessoas" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mesa (opcional)</label>
                            <input type="number" class="form-control" name="mesa_numero">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Criar convite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Qtd</th>
                            <th>Mesa</th>
                            <th>RSVP</th>
                            <th>Check-in</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($convites as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nome_impresso']); ?></td>
                            <td><?= $item['quantidade_pessoas']; ?></td>
                            <td><?= $item['mesa_numero'] ?? '-'; ?></td>
                            <td><?= $item['status_rsvp']; ?></td>
                            <td><?= $item['status_checkin']; ?></td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editar<?= $item['id']; ?>">Editar</button>
                                <form method="post" onsubmit="return confirm('Excluir este convite?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                                <a class="btn btn-sm btn-outline-success" href="/admin/gerar_qr.php?id=<?= $item['id']; ?>" target="_blank">Gerar arquivos</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editar<?= $item['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post">
                                        <input type="hidden" name="acao" value="editar">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar convite</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nome</label>
                                                <input type="text" class="form-control" name="nome_impresso" value="<?= htmlspecialchars($item['nome_impresso']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Quantidade</label>
                                                <input type="number" class="form-control" name="quantidade_pessoas" min="1" value="<?= $item['quantidade_pessoas']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mesa</label>
                                                <input type="number" class="form-control" name="mesa_numero" value="<?= $item['mesa_numero']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status RSVP</label>
                                                <select class="form-select" name="status_rsvp">
                                                    <?php foreach (['pendente' => 'Pendente', 'vai' => 'Vai', 'nao_vai' => 'Não vai'] as $valor => $rotulo): ?>
                                                        <option value="<?= $valor; ?>" <?= $item['status_rsvp'] === $valor ? 'selected' : ''; ?>><?= $rotulo; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Check-in</label>
                                                <select class="form-select" name="status_checkin">
                                                    <?php foreach (['nao_chegou' => 'Não chegou', 'presente' => 'Presente'] as $valor => $rotulo): ?>
                                                        <option value="<?= $valor; ?>" <?= $item['status_checkin'] === $valor ? 'selected' : ''; ?>><?= $rotulo; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
