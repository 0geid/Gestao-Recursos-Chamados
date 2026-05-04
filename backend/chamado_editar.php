<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] != 1) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Chamado não especificado.");
}

$confirmado_msg = isset($_GET['confirmado']) && $_GET['confirmado'] == 1;
$recebido_msg = isset($_GET['recebido']) && $_GET['recebido'] == 1;

// Consulta do chamado
$sql = "SELECT c.*, e.tipo AS equipamento, u.nome AS solicitante
        FROM tb_chamado c
        JOIN tb_equipamento e ON c.id_equipamento = e.id
        JOIN tb_usuario u ON c.rf_solicitante = u.id
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$chamado = $stmt->get_result()->fetch_assoc();

if (!$chamado) {
    die("Chamado não encontrado.");
}

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rf_operador = $_SESSION['rf'];

    // Confirmar chamado
    if (isset($_POST['confirmar'])) {
        $observacao = $_POST['observacao_confirmacao'];

        $sql_confirm = "UPDATE tb_chamado 
                        SET confirmado_admin = 1, observacao_confirmacao = ?
                        WHERE id = ?";
        $stmt_confirm = $conn->prepare($sql_confirm);
        $stmt_confirm->bind_param("si", $observacao, $id);
        $stmt_confirm->execute();

        header("Location: chamado_editar.php?id=$id&confirmado=1");
        exit();
    }

    // Finalizar chamado
    if (isset($_POST['finalizar'])) {
        $descricao_solucao = $_POST['descricao_solucao'];

        $sql_insert = "INSERT INTO tb_solucao (descricao, ativo) VALUES (?, 1)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $descricao_solucao);
        $stmt_insert->execute();
        $id_solucao = $conn->insert_id;

        $sql_update = "UPDATE tb_chamado 
                       SET dt_solucao = NOW(), rf_operador = ?, id_solucao = ?
                       WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $rf_operador, $id_solucao, $id);
        $stmt_update->execute();

        header("Location: chamados_listar.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Chamado</title>
    <link rel="stylesheet" href="../frontend/chamados_editar.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
    <header>
        <img src="../assets/logo.png" alt="logo">
        <h1>Edição de chamado</h1>
    </header>
    <div class="center">
        <h2>Editar / Finalizar Chamado: <br> #<?= $chamado['id'] ?></h2>

        <?php if ($recebido_msg): ?>
            <p style="color:green;"><strong>✅ Chamado atualizado: equipamento recebido para manutenção.</strong></p>
        <?php endif; ?>

        <?php if ($confirmado_msg): ?>
            <p style="color:green;"><strong>✅ Chamado confirmado com sucesso!</strong></p>
        <?php endif; ?>

        <p><strong>Solicitante:</strong> <?= $chamado['solicitante'] ?></p>
        <p><strong>Equipamento:</strong> <?= $chamado['equipamento'] ?></p>
        <p><strong>Descrição do Problema:</strong> <?= $chamado['descricao'] ?></p>
        <p><strong>Data da Solicitação:</strong> <?= date('d/m/Y H:i', strtotime($chamado['dt_solicitacao'])) ?></p>

        <p><strong>Status:</strong> 
            <?php if ($chamado['confirmado_admin'] && empty($chamado['dt_solucao'])): ?>
                <span style="color:orange;">🛠️ Em manutenção</span>
            <?php elseif ($chamado['confirmado_admin']): ?>
                <span style="color:green;">Chamado confirmado pela DTIC ✅</span>
            <?php else: ?>
                <span style="color:orange;">Aguardando confirmação da DTIC ⏳</span>
            <?php endif; ?>
        </p>

        <form method="POST">
            <?php if (!$chamado['confirmado_admin']): ?>
                <p><strong>Antes de confirmar, insira uma observação:</strong></p>
                <label>Observação da Confirmação:</label>
                <textarea name="observacao_confirmacao" required></textarea>
                <br><br>
                <button type="submit" name="confirmar">Confirmar Chamado</button>

            <?php elseif (empty($chamado['recebido_em'])): ?>
                <p><strong>O chamado foi confirmado. Aguardando recebimento pelo responsável.</strong></p>
                <a href="recebimento_form.php?id=<?= $chamado['id'] ?>" class="botao">📥 Preencher formulário de recebimento</a>

            <?php else: ?>
                <p><strong>Status:</strong> Em manutenção 🛠️</p>
                <label>Descreva a Solução:</label>
                <textarea name="descricao_solucao" required></textarea>
                <br><br>
                <button type="submit" name="finalizar">Finalizar Chamado</button>
            <?php endif; ?>

            <br>
            <a href="chamados_listar.php">⬅ Voltar</a>
        </form>
    </div>

    <script>
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 300) + 'px';
            });
        });
    </script>
</body>
</html>
