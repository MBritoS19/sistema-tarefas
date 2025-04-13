# Como Executar o Repositório PHP Existente com XAMPP

Este guia mostra como configurar e executar o projeto PHP já existente disponível em:

```
https://github.com/MBritoS19/sistema-tarefas
```

---

## 1. Pré-requisitos

- **XAMPP** instalado com **PHP 8.3** e **MySQL**.
- **Git** instalado para clonar o repositório.
- **Navegador** (Chrome, Firefox, Edge etc.).
- Permissão para escrever na pasta `htdocs` do XAMPP.

---

## 2. Clonar o Repositório Diretamente em htdocs

1. Abra o **Prompt de Comando** ou **Git Bash** como administrador.
2. Navegue até a pasta `htdocs` do XAMPP:
   ```bash
   cd C:/xampp/htdocs
   ```
3. Clone o repositório diretamente nessa pasta:
   ```bash
   git clone https://github.com/MBritoS19/sistema-tarefas.git
   ```
4. Verifique que a pasta `sistema-tarefas` foi criada em `C:/xampp/htdocs`.

---

## 3. Configurar e Iniciar o Servidor XAMPP

1. Abra o **Painel de Controle do XAMPP**.
2. Confirme que o Apache está configurado para usar PHP 8.3:
   - Clique em **Config** ao lado de **Apache** → **PHP (php.ini)** e verifique a versão no topo.
3. Inicie **Apache** e **MySQL** clicando em **Start** para ambos.

---

## 4. Configurar Banco de Dados

1. No repositório clonado, abra o arquivo SQL (por exemplo `database.sql`) em um editor de texto.
2. Acesse o **phpMyAdmin** em `http://localhost/phpmyadmin` ou abra seu cliente MySQL preferido.
3. Copie todo o conteúdo do arquivo SQL e cole na aba **SQL** do phpMyAdmin (ou execute no cliente MySQL):
   ```sql
   
   -- Criar o banco de dados
   -- CREATE DATABASE IF NOT EXISTS sistema_tarefas;

   -- Usar o banco de dados criado
   -- USE sistema_tarefas;

   -- Criar a tabela de usuarios
   -- CREATE TABLE IF NOT EXISTS usuarios (
   -- id INT AUTO_INCREMENT PRIMARY KEY,
   -- nome VARCHAR(255) NOT NULL,
   -- cargo VARCHAR(255) NOT NULL,
   -- setor VARCHAR(255) NOT NULL,
   -- email VARCHAR(255) NOT NULL,
   -- senha VARCHAR(255) NOT NULL,
   -- ativo BOOLEAN DEFAULT FALSE,
   -- data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   -- );
   
   -- Criar a tabela de tarefas
   -- CREATE TABLE IF NOT EXISTS tarefas (
   -- id INT AUTO_INCREMENT PRIMARY KEY,
   -- titulo VARCHAR(255) NOT NULL,
   -- idUsuario int NOT NULL,
   -- dataConclusao date NOT NULL,
   -- descricao TEXT,
   -- concluida BOOLEAN DEFAULT FALSE,
   -- data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   -- FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
   -- );

   ```
4. Execute para criar o banco de dados, tabelas e inserir dados iniciais.

---

## 5. Acessar o Projeto no Navegador. Acessar o Projeto no Navegador

1. No navegador, acesse:
   ```
   http://localhost/sistema-tarefas
   ```
2. A interface do sistema de tarefas deve aparecer.

---

## 6. Solução de Problemas Comuns

- **Erro 404 (Página não encontrada)**:
  - Confirme que `sistema-tarefas` está em `C:/xampp/htdocs`.
  - Verifique a URL e o nome da pasta.
- **Erro de Conexão com o Banco**:
  - Assegure-se de que o MySQL está em execução.
  - Cheque as credenciais em `config.php`.
- **Versão PHP Incorreta**:
  - No painel do XAMPP, verifique a versão do PHP no `phpinfo()` ou `php -v`.
- **Permissões de Arquivos**:
  - No Windows, normalmente não há ajustes, mas verifique as propriedades de segurança.

---

## 7. Conclusão

Você clonou o repositório diretamente em `htdocs`, configurou o XAMPP com PHP 8.3, ajustou o banco de dados e acessou o projeto em `http://localhost/sistema-tarefas`. Adapte conforme seu ambiente e aproveite o sistema de tarefas!

