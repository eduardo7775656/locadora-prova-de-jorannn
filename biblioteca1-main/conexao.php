<?php
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "locadora_db";
$porta    = 3307;

$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);

if ($conn->connect_error) {
    die("Falha na ligação à base de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
