<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_chamados';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
