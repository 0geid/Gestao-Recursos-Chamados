<?php
session_start();
include_once('conexao.php');

$rf = $_POST['rf'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM tb_usuario WHERE rf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rf);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    if ($usuario['senha'] === $senha) {
        $_SESSION['rf'] = $usuario['rf'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?erro=senha");
        exit();
    }
} else {
    header("Location: index.html?erro=usuario");
    exit();
}
?>

