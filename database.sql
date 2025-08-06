CREATE DATABASE dr_derm;
USE dr_derm;

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    cpf VARCHAR(14),
    conselho_classe VARCHAR(10) NOT NULL,
    numero_conselho VARCHAR(20) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    submodelo VARCHAR(100),
    lote VARCHAR(50),
    vencimento DATE,
    preco DECIMAL(10,2) NOT NULL,
    preco_original DECIMAL(10,2),
    promocao BOOLEAN DEFAULT FALSE,
    estoque INT DEFAULT 0,
    categoria_id INT,
    imagem VARCHAR(255),
    indicacao TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'confirmado', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    forma_pagamento ENUM('pix', 'transferencia', 'cartao') NOT NULL,
    endereco_entrega TEXT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE itens_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Inserir categorias
INSERT INTO categorias (nome, descricao) VALUES
('Toxina Botulínica', 'Produtos para aplicação de botox'),
('Preenchedores', 'Ácido hialurônico e outros preenchedores'),
('Agulhas e Seringas', 'Material para aplicação'),
('Skincare', 'Produtos para cuidados com a pele'),
('Equipamentos', 'Equipamentos para estética');

-- Inserir produtos de exemplo
INSERT INTO produtos (nome, descricao, marca, modelo, preco, preco_original, promocao, estoque, categoria_id, indicacao, vencimento) VALUES
('Botox 100UI', 'Toxina botulínica tipo A', 'Allergan', 'Botox', 850.00, 950.00, TRUE, 50, 1, 'Rugas de expressão, hiperidrose', '2025-06-15'),
('Dysport 300UI', 'Toxina botulínica tipo A', 'Galderma', 'Dysport', 780.00, NULL, FALSE, 30, 1, 'Rugas faciais', '2025-08-20'),
('Juvederm Ultra', 'Ácido hialurônico', 'Allergan', 'Ultra', 650.00, NULL, FALSE, 25, 2, 'Preenchimento labial', '2024-12-30'),
('Agulha 30G', 'Agulha para aplicação', 'BD', '30G', 2.50, NULL, FALSE, 1000, 3, 'Aplicação de toxina', '2026-01-15'),
('Serum Vitamina C', 'Antioxidante facial', 'SkinCeuticals', 'CE Ferulic', 320.00, 380.00, TRUE, 15, 4, 'Antienvelhecimento', '2025-03-10');