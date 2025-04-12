<?php
session_start();
$host = 'localhost';
$db   = 'sistema_tarefas';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar autenticação em páginas restritas
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
