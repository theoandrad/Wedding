-- Script de criação do banco de dados para o casamento Theo & Luísa
CREATE TABLE convites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) UNIQUE NOT NULL,
    nome_impresso VARCHAR(255) NOT NULL,
    quantidade_pessoas INT NOT NULL DEFAULT 1,
    mesa_numero INT NULL,
    status_rsvp ENUM('pendente','vai','nao_vai') DEFAULT 'pendente',
    status_checkin ENUM('nao_chegou','presente') DEFAULT 'nao_chegou',
    observacoes TEXT NULL
);

CREATE TABLE mensagens_convidados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    convite_id INT NOT NULL,
    tipo ENUM('pre_evento','pos_evento') DEFAULT 'pre_evento',
    emoji VARCHAR(10) NULL,
    mensagem TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    lida TINYINT(1) DEFAULT 0,
    favorita TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_mensagem_convite FOREIGN KEY (convite_id) REFERENCES convites(id) ON DELETE CASCADE
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin'
);

-- Usuário administrador padrão (senha: mudar123)
INSERT INTO usuarios (nome, email, senha_hash) VALUES (
    'Theo & Luísa',
    'admin@seusistema.com',
    '$2y$10$qQSKpCCpt5TR1f0NenE2RuexoQj3T/gUY8ailqXFz3vGN9VOGBev2'
);
