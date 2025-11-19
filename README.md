# Sistema Theo & Luísa

Projeto em PHP + MySQL que entrega um site completo para o casamento de Theo & Luísa (24/10/2026) com:

- Landing page pública com informações do evento e ritual das areias.
- Área exclusiva para convidados acessada via token/QR Code.
- Painel administrativo para o casal/cerimonialista com controle de convites, check-in e mensagens.

## Tecnologias
- PHP 8+
- MySQL 8+
- Bootstrap 5, HTML5, CSS3
- JavaScript com jQuery

## Estrutura
```
├── index.php             # Landing page
├── convidado.php         # Área do convidado (acesso via token)
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── convites.php
│   ├── recepcao.php
│   ├── recepcao_api.php
│   ├── mensagens.php
│   ├── gerar_qr.php
│   └── logout.php
├── includes/
│   ├── config.php        # Configuração e conexão PDO
│   ├── funcoes.php       # Funções utilitárias
│   ├── auth.php          # Autenticação de admins
│   └── phpqrcode/        # Biblioteca para gerar QR Code
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── mock_static/          # Versões estáticas (HTML puro) das telas principais
│   ├── index.html
│   ├── convidado.html
│   └── admin/
│       ├── login.html
│       ├── dashboard.html
│       ├── convites.html
│       ├── recepcao.html
│       ├── mensagens.html
│       └── gerar_qr.html
└── database.sql          # Script de criação das tabelas
```

## Banco de dados
Importe `database.sql` em um banco MySQL vazio. Ele cria as tabelas `convites`, `mensagens_convidados` e `usuarios`, além de inserir um usuário admin padrão (`admin@seusistema.com` / senha `mudar123`).

## Configuração
1. Ajuste as constantes de `includes/config.php` com os dados do seu banco e domínio.
2. Configure o host para apontar para a pasta do projeto.
3. Faça login em `/admin/login.php` com o usuário padrão e comece a cadastrar convites.

### Pasta mock_static
Se precisar demonstrar o layout sem rodar PHP/MySQL, use os arquivos em `mock_static/`. Eles reproduzem os fluxos principais
com HTML estático (landing, área do convidado e principais telas do painel) para apresentações ou validações rápidas.

## Recursos principais
- Geração automática de tokens e QR Codes para cada convite.
- Formulário de RSVP e recados diretamente na página do convidado.
- Painel com estatísticas, lista de mensagens e tela mobile-friendly para check-in (scanner QR + busca por nome).

Sinta-se à vontade para personalizar textos, imagens e estilos para combinar com o layout final do casamento!
