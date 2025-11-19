<?php
require_once __DIR__ . '/../includes/auth.php';

if (!empty($_SESSION['usuario_id'])) {
    redirecionar('/admin/dashboard.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (autenticar($_POST['email'] ?? '', $_POST['senha'] ?? '')) {
        redirecionar('/admin/dashboard.php');
    } else {
        $erro = 'Usuário ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Theo & Luísa - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-3">Theo & Luísa</h3>
                    <p class="text-center text-muted">Painel administrativo</p>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= $erro; ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" class="form-control" name="senha" required>
                        </div>
                        <button class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
