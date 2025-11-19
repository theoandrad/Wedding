<?php
require_once __DIR__ . '/config.php';

/**
 * Verifica se o usuário está logado e, se não estiver, redireciona para o login.
 */
function exigirLogin(): void
{
    if (empty($_SESSION['usuario_id'])) {
        redirecionar('/admin/login.php');
    }
}

/**
 * Processa o login a partir de email e senha.
 */
function autenticar(string $email, string $senha): bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        return true;
    }
    return false;
}

function encerrarSessao(): void
{
    session_destroy();
}
