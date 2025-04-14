<?php
// Realiza o logout do usuário
// Limpa as variáveis de sessão e destrói a sessão
require 'config.php';

session_unset();
session_destroy();
header('Location: login.php');
exit;
?>