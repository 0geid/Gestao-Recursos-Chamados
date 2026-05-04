<?php
session_start();
$retirada_msg = isset($_GET['retirada']) && $_GET['retirada'] == 1;
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$nome = $_SESSION['nome'];

$sql = "SELECT c.*, e.tipo AS equipamento, u.nome AS nome_solicitante, u.rf AS rf_solicitante, s.descricao AS solucao
        FROM tb_chamado c
        JOIN tb_equipamento e ON c.id_equipamento = e.id
        JOIN tb_usuario u ON c.rf_solicitante = u.id
        LEFT JOIN tb_solucao s ON c.id_solucao = s.id
        WHERE c.rf_solicitante = ?
        ORDER BY c.dt_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados</title>
    <link rel="stylesheet" href="../frontend/meus_chamados.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
    <header>
        <img src="../assets/logo.png" alt="logo">
        <h1>Gestão De Recursos e Chamado</h1>
        <h2>Divisão De Tecnologia Informação E Comunicação</h2>
        <p>Bem-vindo, <?= $nome ?>!</p>
        <h3>Meus Chamados</h3>
        <?php if ($retirada_msg): ?>
            <p style="color:green;"><strong>✅ Retirada registrada com sucesso!</strong></p>
        <?php endif; ?>
    </header>

    <br><br>

    <div class="center">
        <div class="chamados-container">
            <?php while ($c = $result->fetch_assoc()): ?>
                <?php
                    // Determinar status do chamado após recebimento
                    if (!empty($c['recebido_em']) && empty($c['dt_solucao'])) {
                        $status = '🛠️ Em manutenção';
                    } elseif (!empty($c['dt_solucao']) && empty($c['dt_retirada'])) {
                        $status = '📦 Disponível para retirada';
                    } elseif (!empty($c['dt_retirada'])) {
                        $status = '✅ Retirado';
                    } else {
                        $status = '⏳ Aguardando recebimento pela DTIC';
                    }

                    // Definir link de visualização
                    $link = (!empty($c['dt_solucao'])) ? 'chamado_vizualizar.php' : 'chamado_detalhes.php';
                ?>

                <a href="<?= $link ?>?id=<?= $c['id'] ?>" class="chamado-link">
                    <div class="chamado-bolha">
    <p><strong>OS:</strong> <?= $c['id'] ?></p>
    <p><strong>Solicitante:</strong> <?= $c['nome_solicitante'] ?></p>
    <p><strong>RF:</strong> <?= $c['rf_solicitante'] ?></p>
    <p><strong>Máquina:</strong> <?= $c['nome_cpu'] ?></p>
    <p><strong>Status:</strong> <?= $status ?></p>

    <?php if (!empty($c['observacao_confirmacao']) && empty($c['recebido_em'])): ?>
        <p><strong>Observação da Confirmação:</strong> <?= $c['observacao_confirmacao'] ?></p>
    <?php endif; ?>

    <?php if (!empty($c['solucao'])): ?>
        <p><strong>Observação da Manutenção:</strong> <?= nl2br(htmlspecialchars($c['solucao'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($c['dt_retirada'])): ?>
        <p style="color:green;"><strong>✅ Equipamento retirado</strong></p>
    <?php endif; ?>
</div>

                </a>
            <?php endwhile; ?>
        </div>
        <a href="dashboard.php">⬅ Voltar</a>
    </div>
</body>
</html>
