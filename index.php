<?php
session_start();
include('backend/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rf = $_POST['rf'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM tb_usuario WHERE rf = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $rf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['tipo'] = $user['tipo'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['rf'] = $user['rf'];

        header("Location: backend/dashboard.php");
        exit();
    } else {
        $erro = "RF ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Solicitação De Chamado</title>
<link rel="stylesheet" href="frontend/stylesheet.css">
<link rel="icon" type="image/png" href="../assets/logo.png">
<script src="Script.js"></script>
</head>
<body>
<div class="center">
    <img src="assets/logo.png" alt="logo">
    <h1>Gestão De Recursos e Chamado</h1>
    <h2>Divisão De Tecnologia Informação E Comunicação</h2>
    <h2>Login</h2>
    <form method="POST">
        <label>RF:</label>
        <input type="text" name="rf" required>

        <label>Senha:</label>
        <input type="password" name="senha" required>

        <button type="submit">Entrar</button>
        <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    </form>
</div>
</body>
</html>