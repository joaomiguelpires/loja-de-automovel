-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS loja_carros;
USE loja_carros;

-- Tabela marcas
CREATE TABLE IF NOT EXISTS marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    pais_origem VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela carros
CREATE TABLE IF NOT EXISTS carros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(255) NOT NULL,
    marca_id INT NOT NULL,
    categoria_id INT NOT NULL,
    ano INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0,
    cor VARCHAR(100) NOT NULL,
    quilometragem INT NOT NULL DEFAULT 0,
    descricao TEXT,
    disponivel BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela vendas
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carro_id INT NOT NULL,
    cliente_id INT NOT NULL,
    data_venda DATE NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (carro_id) REFERENCES carros(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir dados iniciais - Marcas
INSERT INTO marcas (nome, pais_origem) VALUES
('Toyota', 'Japão'),
('Honda', 'Japão'),
('Ford', 'Estados Unidos'),
('Chevrolet', 'Estados Unidos'),
('Volkswagen', 'Alemanha'),
('BMW', 'Alemanha'),
('Mercedes-Benz', 'Alemanha'),
('Fiat', 'Itália'),
('Renault', 'França'),
('Hyundai', 'Coreia do Sul');

-- Inserir dados iniciais - Categorias
INSERT INTO categorias (nome) VALUES
('Sedan'),
('Hatchback'),
('SUV'),
('Pickup'),
('Coupé'),
('Convertible'),
('Wagon'),
('Minivan');

-- Inserir dados de exemplo - Clientes
INSERT INTO clientes (nome, email, telefone, endereco) VALUES
('João Silva', 'joao@email.com', '(11) 99999-9999', 'Rua das Flores, 123 - São Paulo/SP'),
('Maria Santos', 'maria@email.com', '(11) 88888-8888', 'Av. Paulista, 456 - São Paulo/SP'),
('Pedro Oliveira', 'pedro@email.com', '(11) 77777-7777', 'Rua Augusta, 789 - São Paulo/SP');

-- Inserir dados de exemplo - Carros
INSERT INTO carros (modelo, marca_id, categoria_id, ano, preco, cor, quilometragem, descricao, disponivel) VALUES
('Civic', 2, 1, 2020, 85000.00, 'Prata', 45000, 'Carro em excelente estado, único dono', 1),
('Corolla', 1, 1, 2019, 78000.00, 'Branco', 52000, 'Bem conservado, revisões em dia', 1),
('Golf', 5, 2, 2021, 95000.00, 'Preto', 30000, 'Semi-novo, com garantia', 1),
('Focus', 3, 2, 2018, 65000.00, 'Azul', 68000, 'Carro confiável, histórico completo', 1),
('Cruze', 4, 1, 2020, 72000.00, 'Vermelho', 41000, 'Bem equipado, ar condicionado', 1);

-- Tabela de usuários para autenticação
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'vendedor', 'gerente', 'cliente') DEFAULT 'vendedor',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário admin padrão (senha: admin123)
-- Hash gerado com password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (nome, email, password, role) VALUES
('Administrador', 'admin@loja.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Vendedor Teste', 'vendedor@loja.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor');

-- Tabela de movimentações
CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carro_id INT NOT NULL,
    tipo ENUM('entrada', 'saida', 'transferencia') NOT NULL,
    descricao TEXT,
    data_movimentacao DATE NOT NULL,
    data_prevista DATE,
    origem VARCHAR(255),
    destino VARCHAR(255),
    responsavel VARCHAR(255),
    status ENUM('pendente', 'realizada', 'cancelada') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (carro_id) REFERENCES carros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir dados de exemplo - Movimentações
INSERT INTO movimentacoes (carro_id, tipo, descricao, data_movimentacao, data_prevista, origem, destino, responsavel, status) VALUES
(1, 'entrada', 'Carro recebido do fornecedor', '2024-01-10', NULL, 'Fornecedor ABC', 'Loja Central', 'João Silva', 'realizada'),
(2, 'saida', 'Carro enviado para filial', '2024-01-15', '2024-01-20', 'Loja Central', 'Filial SP', 'Maria Santos', 'pendente'),
(3, 'transferencia', 'Transferência entre lojas', '2024-01-20', '2024-01-25', 'Loja Central', 'Loja RJ', 'Pedro Oliveira', 'pendente');









