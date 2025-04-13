-- Criar o banco de dados

CREATE DATABASE IF NOT EXISTS sistema_tarefas;

-- Usar o banco de dados criado

USE sistema_tarefas;

-- Criar a tabela de usuarios

CREATE TABLE IF NOT EXISTS usuarios (

id INT AUTO_INCREMENT PRIMARY KEY,

nome VARCHAR(255) NOT NULL,

cargo VARCHAR(255) NOT NULL,

setor VARCHAR(255) NOT NULL,

email VARCHAR(255) NOT NULL,

senha VARCHAR(255) NOT NULL,

ativo BOOLEAN DEFAULT FALSE,

data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

-- Criar a tabela de tarefas

CREATE TABLE IF NOT EXISTS tarefas (

id INT AUTO_INCREMENT PRIMARY KEY,

titulo VARCHAR(255) NOT NULL,

idUsuario int NOT NULL,

dataConclusao date NOT NULL,

descricao TEXT,

concluida BOOLEAN DEFAULT FALSE,

data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (idUsuario) REFERENCES usuarios(id)

);