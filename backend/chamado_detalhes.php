<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$id_chamado = $_GET['id'] ?? null;

if (!$id_chamado) {
    echo "Chamado não especificado.";
    exit();
}

//consulta SQL
$sql = "SELECT c.*, e.tipo AS equipamento, u.nome AS nome_solicitante, u.rf AS rf_solicitante, s.descricao AS solucao
        FROM tb_chamado c
        JOIN tb_equipamento e ON c.id_equipamento = e.id
        JOIN tb_usuario u ON c.rf_solicitante = u.id
        LEFT JOIN tb_solucao s ON c.id_solucao = s.id
        WHERE c.id = ?
        ORDER BY c.dt_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_chamado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Chamado não encontrado.";
    exit();
}

$chamado = $result->fetch_assoc();

// Determinar status
if (!$chamado['confirmado_admin']) {
    $status = '🟡 Aguardando confirmação da DTIC';
} elseif (!empty($chamado['recebido_em']) && empty($chamado['dt_solucao'])) {
    $status = '🛠️ Em manutenção';
} elseif (!empty($chamado['dt_solucao']) && empty($chamado['dt_retirada'])) {
    $status = '📦 Disponível para retirada';
} elseif (!empty($chamado['dt_retirada'])) {
    $status = '✅ Retirado';
} else {
    $status = '⏳ Em aberto, encaminhar a máquina para o DTIC';
}
?>
<!-- Corpo HTML-->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Chamado</title>
    <link rel="stylesheet" href="../frontend/chamado_detalhes.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
    <header>
        <img src="../assets/logo.png" alt="logo">
        <h1>Detalhes do Chamado</h1>
    </header>

    <div class="center">
    <div class="secao">
        <h2>📞 Dados Do Chamado</h2>
        <p><strong>OS:</strong> <?= $chamado['id'] ?></p>
        <p><strong>Solicitante:</strong> <?= $chamado['nome_solicitante'] ?></p>
        <p><strong>Equipamento:</strong> <?= $chamado['equipamento'] ?></p>
        <p><strong>Máquina:</strong> <?= $chamado['nome_cpu'] ?></p>
        <p><strong>Endereço:</strong> <?= $chamado['ender_solicitante'] ?></p>
        <p><strong>Descrição:</strong> <?= $chamado['descricao'] ?></p>
        <p><strong>Data da Solicitação:</strong> <?= date('d/m/Y', strtotime($chamado['dt_solicitacao'])) ?></p>
        <p><strong>Status:</strong> <?= $status ?></p>
        <?php if (!empty($chamado['dt_solucao'])): ?>
            <p><strong>Data da Solução:</strong> <?= date('d/m/Y', strtotime($chamado['dt_solucao'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="secao">
        <h2>ℹ️ Dados Da Confirmação</h2>
        <?php if (!empty($chamado['observacao_confirmacao'])): ?>
            <p><strong>Observação da Confirmação:</strong> <?= $chamado['observacao_confirmacao'] ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($chamado['recebido_em'])): ?>
    <div class="secao">
        <h2>📥 Dados do Recebimento</h2>
        <p><strong>Responsável:</strong> <?= $chamado['responsavel_recebimento'] ?></p>
        <p><strong>RF:</strong> <?= $chamado['rf_recebimento'] ?></p>
        <p><strong>Graduação:</strong> <?= $chamado['graduacao_recebimento'] ?></p>
        <p><strong>Data do Recebimento:</strong> <?= date('d/m/Y', strtotime($chamado['recebido_em'])) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($chamado['dt_retirada'])): ?>
    <div class="secao">
        <h2>📦 Dados da Retirada</h2>
        <p><strong>Responsável:</strong> <?= $chamado['responsavel_retirada'] ?></p>
        <p><strong>RF:</strong> <?= $chamado['rf_responsavel'] ?></p>
        <p><strong>Graduação:</strong> <?= $chamado['graduacao_resposavel'] ?></p>
        <p><strong>Data da Retirada:</strong> <?= date('d/m/Y H:i', strtotime($chamado['dt_retirada'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="secao">
        <h2>🛠️ Dados Da manutenção</h2>
        <p><strong>Solução Aplicada:</strong> <?= !empty($chamado['solucao']) ? $chamado['solucao'] : 'Não registrada.' ?></p>
    </div>
</div>

    
    <a href="meus_chamados.php">⬅ Voltar</a>
</body>
</html>
