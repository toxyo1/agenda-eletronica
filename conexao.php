<?php
$host = "localhost";
$banco = "agenda_eletronica";
$usuario = "marian";
$senha = "1234";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erro) {
    echo "Erro ao conectar com o banco de dados: " . $erro->getMessage();
}
?>