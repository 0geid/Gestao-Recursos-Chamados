<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
  exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
  echo "Chamado não encontrado.";
  exit();
}

// Consulta do chamado com solução
$sql = "SELECT c.*, u.nome AS solicitante, u.rf AS rf_solicitante, e.tipo AS equipamento, s.descricao AS solucao 
        FROM tb_chamado c 
        JOIN tb_usuario u ON c.rf_solicitante = u.id 
        JOIN tb_equipamento e ON c.id_equipamento = e.id 
        LEFT JOIN tb_solucao s ON c.id_solucao = s.id 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Chamado não encontrado.";
  exit();
}

$chamado = $result->fetch_assoc();

// Consulta da retirada (se houver)
$sql_retirada = "SELECT * FROM tb_retirada WHERE id_chamado = ?";
$stmt_retirada = $conn->prepare($sql_retirada);
$stmt_retirada->bind_param("i", $id);
$stmt_retirada->execute();
$result_retirada = $stmt_retirada->get_result();
$retirada = $result_retirada->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizar Chamado</title>
  <link rel="stylesheet" href="../frontend/chamado_vizualizar.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
  <header>
    <img src="../assets/logo.png" alt="logo">
    <h1>Gestão De Recursos e Chamado</h1>
    <h2>Divisão De Tecnologia Informação e Comunicação</h2>
    <h3>Chamado Finalizado</h3>
  </header>

  <div class="colunas">
    <!-- Dados do Chamado -->
    <div class="coluna">
      <h2>📞 Dados do Chamado</h2>
      <p><strong>OS:</strong> <?= $chamado['id'] ?></p>
      <p><strong>Base Solicitante:</strong> <?= $chamado['ender_solicitante'] ?></p>
      <p><strong>Solicitante:</strong> <?= $chamado['solicitante'] ?></p>
      <p><strong>RF do Solicitante:</strong> <?= $chamado['rf_solicitante'] ?></p>
      <p><strong>Equipamento:</strong> <?= $chamado['equipamento'] ?></p>
      <p><strong>Máquina:</strong> <?= $chamado['nome_cpu'] ?></p>
      <p><strong>Descrição:</strong> <?= $chamado['descricao'] ?></p>
      <p><strong>Data da Solicitação:</strong> <?= date('d/m/Y', strtotime($chamado['dt_solicitacao'])) ?></p>
    </div>

    <!-- Dados do Recebimento -->
    <?php if (!empty($chamado['recebido_em'])): ?>
    <div class="coluna">
      <h2>📥 Dados do Recebimento</h2>
      <p><strong>Responsável:</strong> <?= $chamado['responsavel_recebimento'] ?></p>
      <p><strong>RF:</strong> <?= $chamado['rf_recebimento'] ?></p>
      <p><strong>Graduação:</strong> <?= $chamado['graduacao_recebimento'] ?></p>
      <p><strong>Data do Recebimento:</strong> <?= date('d/m/Y', strtotime($chamado['recebido_em'])) ?></p>
    </div>
    <?php endif; ?>

      <div class="coluna">
        <h2>🛠️ Dados Da manutenção</h2>
        <p><strong>Data da Solução:</strong> <?= date('d/m/Y', strtotime($chamado['dt_solucao'])) ?></p>
        <p><strong>Solução Aplicada:</strong> <?= !empty($chamado['solucao']) ? $chamado['solucao'] : 'Não registrada.' ?></p>
        <p class="status-finalizado">✅ Manutenção Finalizada, Aguardando Retirada</p>
    </div>

    <!-- Dados da Retirada -->
    <?php if (!empty($chamado['dt_retirada'])): ?>
    <div class="coluna">
      <h2>📦 Dados da Retirada</h2>
      <p><strong>Responsável:</strong> <?= $chamado['responsavel_retirada'] ?></p>
      <p><strong>RF:</strong> <?= $chamado['rf_responsavel'] ?></p>
      <p><strong>Graduação:</strong> <?= $chamado['graduacao_responsavel'] ?></p>
      <p><strong>Data da Retirada:</strong> <?= date('d/m/Y', strtotime($chamado['dt_retirada'])) ?></p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Botão de Retirada -->
  <?php if (!empty($chamado['dt_solucao']) && $_SESSION['tipo'] == 1 && empty($chamado['dt_retirada'])): ?>
    <div class="retirada-botao">
      <a href="retirada_form.php?id=<?= $chamado['id'] ?>" class="btn-retirada">Realizar Retirada</a>
    </div>
  <?php endif; ?>

  <a href="../backend/dashboard.php">⬅ Voltar</a>
</body>
</html>