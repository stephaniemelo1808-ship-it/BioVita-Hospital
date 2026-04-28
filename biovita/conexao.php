<?php
$host = '127.0.0.1'; 
$usuario = 'root';
$senha = '';
$banco = 'trabalho';

$mysqli = new mysqli($host, $usuario, $senha, $banco);

if ($mysqli->error) {
    die("Falha na conexão: " . $mysqli->error);
}
?>