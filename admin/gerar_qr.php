<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/phpqrcode/qrlib.php';
exigirLogin();

$id = (int)($_GET['id'] ?? 0);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT * FROM convites WHERE id = :id');
$stmt->execute([':id' => $id]);
$convite = $stmt->fetch();

if (!$convite) {
    exit('Convite não encontrado');
}

$url = SITE_URL . '/convidado.php?token=' . $convite['token'];
$baseNome = preg_replace('/[^a-zA-Z0-9 \-]/', '', $convite['nome_impresso']);
$baseNome .= $convite['mesa_numero'] ? ' - Mesa ' . $convite['mesa_numero'] : '';
$pngPath = sys_get_temp_dir() . '/' . uniqid('qr_') . '.png';
$txtPath = sys_get_temp_dir() . '/' . uniqid('qr_') . '.txt';

QRcode::png($url, $pngPath, QR_ECLEVEL_L, 6);

$conteudoTxt = "Casamento Theo & Luísa\n" .
    "Data: 24/10/2026 às 17h\n" .
    "Local: Sede Campestre do Clube Comercial – São Borja/RS\n\n" .
    "Convite: {$convite['nome_impresso']}\n" .
    "Mesa: " . ($convite['mesa_numero'] ?? 'Definiremos em breve') . "\n" .
    "Quantidade de pessoas: {$convite['quantidade_pessoas']}\n" .
    "Token: {$convite['token']}\n" .
    "URL: {$url}\n" .
    "Observações: " . ($convite['observacoes'] ?? '---');
file_put_contents($txtPath, $conteudoTxt);

$zipPath = sys_get_temp_dir() . '/' . uniqid('qr_') . '.zip';
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE);
$zip->addFile($pngPath, $baseNome . '.png');
$zip->addFile($txtPath, $baseNome . '.txt');
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $baseNome . '.zip"');
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);

unlink($pngPath);
unlink($txtPath);
unlink($zipPath);
exit;
