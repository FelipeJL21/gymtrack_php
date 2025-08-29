<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$basededados = "bd";

$conn = new mysqli($servidor, $usuario, $senha, $basededados);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}
?>