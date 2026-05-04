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

$sql = "SELECT c.*, 
               u.nome AS solicitante, 
               u.rf AS rf_solicitante, 
               e.tipo AS equipamento, 
               s.descricao AS solucao 
        FROM tb_chamado c 
        JOIN tb_usuario u ON c.rf_solicitante = u.id 
        JOIN tb_equipamento e ON c.id_equipamento = e.id 
        LEFT JOIN tb_solucao s ON c.id_solucao = s.id 
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_chamado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Chamado não encontrado.";
  exit();
}

$chamado = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmar Recebimento</title>
  <link rel="stylesheet" href="../frontend/retirada_form.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
  <div class="center">
    <h1>Confirmar Recebimento do Chamado: <br> #<?= $chamado['id'] ?></h1>

    <div class="chamado-detalhes">
      <p><strong>Solicitante:</strong> <?= $chamado['solicitante'] ?></p>
      <p><strong>RF do Solicitante:</strong> <?= $chamado['rf_solicitante'] ?></p>
      <p><strong>Equipamento:</strong> <?= $chamado['equipamento'] ?></p>
      <p><strong>Máquina:</strong> <?= $chamado['nome_cpu'] ?></p>
      <p><strong>Descrição:</strong> <?= $chamado['descricao'] ?></p>
      <p><strong>Base Solicitante:</strong> <?= $chamado['ender_solicitante'] ?></p>
      <p><strong>Data da Solicitação:</strong> <?= date('d/m/Y H:i', strtotime($chamado['dt_solicitacao'])) ?></p>
    </div>

    <h1>Dados de quem está recebendo:</h1>

    <form method="POST" action="recebimento_salvar.php">
      <input type="hidden" name="id_chamado" value="<?= $chamado['id'] ?>">

      <label>Responsável pelo Recebimento:</label>
      <input type="text" name="responsavel_recebimento" required>

      <label>RF do Responsável:</label>
      <input type="text" name="rf_recebimento" required>

      <label>Graduação do Responsável:</label>
      <input type="text" name="graduacao_recebimento" required>

      <label>Data do Recebimento:</label>
      <input type="date" name="data_recebimento" id="data_recebimento" readonly onkeydown="return false" onmousedown="return false">

      <button type="submit">Confirmar</button>
    </form>

    <a href="chamados_listar.php">⬅ Voltar</a>
  </div>

  <script>
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('data_recebimento').value = today;
  </script>

</body>
</html>
