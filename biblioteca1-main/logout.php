<?php
session_start();
session_unset();   // Limpa as variáveis salvas na sessão
session_destroy(); // Destrói a sessão ativa do usuário

// Manda de volta para a tela de login
header("Location: login.php");
exit;
?>
