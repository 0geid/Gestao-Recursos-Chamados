<?php
session_start();
if (!isset($_SESSION['rf'])) {
    header("Location: index.html?erro=sem_sessao");
    exit();
}

$rf = $_SESSION['rf'];
$nome = $_SESSION['nome'];
$tipo = $_SESSION['tipo'];
?>
<!--Corpo HTML-->
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel - Chamados</title>
<link rel="stylesheet" href="../frontend/dashboard.css">
<link rel="icon" type="image/png" href="../assets/logo.png">

</head>
<body>
    <div class="center">
        <img src="../assets/logo.png" alt="logo">
        <h1>Gestão De Recursos e Chamado</h1>
        <h2>DTIC</h2>
            <h2>Bem-vindo, <?php echo $nome; ?>!</h2>
                <h3>RF: <?php echo $rf; ?>.</h3>
<!--Tipos de usuário-->
<?php if ($tipo == 1): // master ?>
    <a href="chamados_listar.php">Gerenciar Chamados</a><br>
    <a href="consultar_chamado.php">Consulta De Chamados</a><br>
<?php else: //Usuario ?> 
    <a href="abrir_chamado.php">Abrir Novo Chamado</a><br>
    <a href="chamados_listar.php">Meus Chamados</a><br>
<?php endif; ?>
<a href="/gestao-recursos-chamados/index.php"> Sair </a>
</div>
</body>
</html>
