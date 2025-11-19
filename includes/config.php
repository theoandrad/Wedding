<?php
// Configurações gerais do sistema Theo & Luísa

define('SITE_NAME', 'Theo & Luísa');
define('SITE_URL', 'https://seusistema.com'); // Ajuste conforme o domínio final

define('DB_HOST', 'localhost');
define('DB_NAME', 'casamento');
define('DB_USER', 'root');
define('DB_PASS', '');

// Inicia a sessão para toda a aplicação quando necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Retorna uma instância PDO reutilizável.
 */
function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
