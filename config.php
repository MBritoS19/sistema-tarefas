<?php
// Configuração do banco de dados
// Inicia a sessão
session_start();
$host = 'localhost';
$db   = 'sistema_tarefas';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Cria a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

//função para verificar se o usuario está logado
// Se não estiver logado, redireciona para a página de login
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
