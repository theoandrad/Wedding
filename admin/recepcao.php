<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recepção • Theo & Luísa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Recepção</h1>
        <a href="/admin/dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5>Leitor de QR</h5>
                    <div id="leitor" style="width:100%;"></div>
                    <button class="btn btn-outline-primary mt-3" id="iniciar">Ativar câmera</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body" id="detalhes">
                    <p class="text-muted">Passe um QR ou busque pelo nome.</p>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <label class="form-label">Buscar convidado</label>
                    <input type="text" class="form-control" id="busca" placeholder="Digite parte do nome">
                    <ul class="list-group mt-2" id="resultados"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let scanner;
function mostrarConvite(convite) {
    if (!convite) {
        $('#detalhes').html('<div class="alert alert-warning">Convite não encontrado.</div>');
        return;
    }
    let html = `
        <h4>${convite.nome_impresso}</h4>
        <p>Quantidade: ${convite.quantidade_pessoas} · RSVP: ${convite.status_rsvp} · Mesa: ${convite.mesa_numero ?? '-'} </p>
        <p>Check-in: <strong>${convite.status_checkin}</strong></p>
        <button class="btn btn-success" id="checkin" data-id="${convite.id}">Marcar como presente</button>
    `;
    $('#detalhes').html(html);
}

function buscarPorToken(token) {
    $.post('recepcao_api.php', { acao: 'buscar_token', token }, function(resp) {
        mostrarConvite(resp.convite);
    }, 'json');
}

$(function() {
    $('#iniciar').on('click', function() {
        if (scanner) return;
        scanner = new Html5Qrcode('leitor');
        scanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, token => {
            buscarPorToken(token.split('token=').pop());
        });
    });

    $('#busca').on('input', function() {
        const nome = $(this).val();
        if (nome.length < 2) { $('#resultados').empty(); return; }
        $.post('recepcao_api.php', { acao: 'buscar_nome', nome }, function(resp) {
            const itens = resp.resultados.map(item => `<li class="list-group-item" data-id="${item.id}">${item.nome_impresso}</li>`);
            $('#resultados').html(itens.join(''));
        }, 'json');
    });

    $('#resultados').on('click', 'li', function() {
        const id = $(this).data('id');
        $.post('recepcao_api.php', { acao: 'buscar_nome', nome: $(this).text() }, function(resp) {
            mostrarConvite(resp.resultados.find(r => r.id == id));
        }, 'json');
    });

    $('#detalhes').on('click', '#checkin', function() {
        const id = $(this).data('id');
        $.post('recepcao_api.php', { acao: 'checkin', id }, function() {
            $.post('recepcao_api.php', { acao: 'buscar_id', id }, function(resp) {
                mostrarConvite(resp.convite);
            }, 'json');
        });
    });
});
</script>
</body>
</html>
