<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$rf    = $_SESSION['rf'];
$tipo  = $_SESSION['tipo'];
$nome  = $_SESSION['nome'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_equip          = $_POST['id_equipamento'];
    $descricao         = $_POST['descricao'];
    $rf_solic          = $_SESSION['id'];
    $nome_cpu          = $_POST['nome_cpu'];
    $ender_solicitante = $_POST['ender_solicitante'];

    $sql = "INSERT INTO tb_chamado (id_equipamento, descricao, rf_solicitante, nome_cpu, ender_solicitante, dt_solicitacao)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $id_equip, $descricao, $rf_solic, $nome_cpu, $ender_solicitante);

    if ($stmt->execute()) {
        $msg = "Chamado aberto com sucesso, Aguarde Confirmação da DTIC!";
    } else {
        $msg = "Erro ao abrir chamado: " . $conn->error;
    }
}

$equipamentos = $conn->query("SELECT * FROM tb_equipamento WHERE ativo = 1");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Chamado</title>
    <link rel="stylesheet" href="../frontend/abrir_chamado.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
    <body>
        <div class="center">
            <img src="../assets/logo.png" alt="logo">
            <form method="POST">
                    <h1>Gestão De Recursos e Chamado</h1>
                    <h2>Divisão de Tecnologia da Informação</h2>
                    <h2>Abrir Chamado</h2>
                    <h3>Olá, <?= $nome ?> (RF: <?= $rf ?>)</h3>
                    <!--Informações Adicioanais-->
                    <h1>IMPORTANTE ⚠️</h1>
                    <h4>A DTIC não é responsavel por Backups, Realiza o Backup com antecedência ao chamado!</h4>
                    <!--Unidade-->
                    <label>Unidade:</label>
                        <textarea name="ender_solicitante" required></textarea><br>
                    <!--Equipamento-->    
                    <label>Equipamento:</label>
                    <select name="id_equipamento" required>
                        <option value="">Selecione...</option>
                        <?php while ($eq = $equipamentos->fetch_assoc()): ?>
                            <option value="<?= $eq['id'] ?>"><?= $eq['tipo'] ?> - <?= $eq['grupo'] ?></option>
                        <?php endwhile; ?>
                    </select><br>
                    <!--Nome-->
                    <label>Nome do computador:</label>
                        <textarea name="nome_cpu" required></textarea><br>
                    <!--Relato-->
                    <label>Relate o problema:</label>
                        <textarea name="descricao" required></textarea><br>
                    <button type="submit">Registrar Chamado</button><br>
            </form>
            <?php if (isset($msg)) echo "<p>$msg</p>"; ?>
        <a href="dashboard.php">Voltar</a>
        <br><br>
        </div> 
        
    <script>
        document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', function () {
            this.style.height = 'auto'; // reseta altura
            this.style.height = this.scrollHeight + 'px'; // ajusta para conteúdo
        });
        });
    </script>

    </body>
</html>
