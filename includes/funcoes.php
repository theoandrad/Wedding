<?php
require_once __DIR__ . '/config.php';

/**
 * Gera um token aleatório para convites.
 */
function gerarToken(int $tamanho = 32): string
{
    return bin2hex(random_bytes($tamanho / 2));
}

/**
 * Redireciona com header Location e encerra.
 */
function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Retorna estatísticas básicas dos convites.
 */
function obterEstatisticasConvites(): array
{
    $pdo = getPDO();
    $total = $pdo->query('SELECT COUNT(*) FROM convites')->fetchColumn();
    $vai = $pdo->query("SELECT COUNT(*) FROM convites WHERE status_rsvp = 'vai'")->fetchColumn();
    $naoVai = $pdo->query("SELECT COUNT(*) FROM convites WHERE status_rsvp = 'nao_vai'")->fetchColumn();
    $pendente = $pdo->query("SELECT COUNT(*) FROM convites WHERE status_rsvp = 'pendente'")->fetchColumn();
    $presentes = $pdo->query("SELECT COUNT(*) FROM convites WHERE status_checkin = 'presente'")->fetchColumn();

    return compact('total', 'vai', 'naoVai', 'pendente', 'presentes');
}

/**
 * Busca mensagens recentes para o painel.
 */
function buscarMensagensRecentes(int $limite = 5): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT m.*, c.nome_impresso FROM mensagens_convidados m JOIN convites c ON c.id = m.convite_id ORDER BY m.created_at DESC LIMIT :limite');
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Retorna os dados do convite a partir do token.
 */
function buscarConvitePorToken(string $token): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM convites WHERE token = :token');
    $stmt->execute([':token' => $token]);
    $dados = $stmt->fetch();
    return $dados ?: null;
}
