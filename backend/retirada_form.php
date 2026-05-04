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
  <title>Confirmar Retirada</title>
  <link rel="stylesheet" href="../frontend/retirada_form.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
  <div class="center">
    <h1>Confirmar Retirada do Chamado: <br> #<?= $chamado['id'] ?></h1>

    <div class="chamado-detalhes">
      <p><strong>Solicitante:</strong> <?= $chamado['solicitante'] ?></p>
      <p><strong>RF do Solicitante:</strong> <?= $chamado['rf_solicitante'] ?></p>
      <p><strong>Equipamento:</strong> <?= $chamado['equipamento'] ?></p>
      <p><strong>Máquina:</strong> <?= $chamado['nome_cpu'] ?></p>
      <p><strong>Descrição:</strong> <?= $chamado['descricao'] ?></p>
      <p><strong>Base Solicitante:</strong> <?= $chamado['ender_solicitante'] ?></p>
      <p><strong>Data da Solicitação:</strong> <?= date('d/m/Y H:i', strtotime($chamado['dt_solicitacao'])) ?></p>
      <p><strong>Data da Solução:</strong> <?= date('d/m/Y H:i', strtotime($chamado['dt_solucao'])) ?></p>
      <p><strong>Solução Aplicada:</strong> <?= !empty($chamado['solucao']) ? $chamado['solucao'] : 'Não registrada.' ?></p>
    </div>

    <h1>Dados De quem está retirando:</h1>

    <form method="POST" action="retirada_salvar.php">
      <input type="hidden" name="id_chamado" value="<?= $chamado['id'] ?>">

      <label>Responsável pela Retirada:</label>
      <input type="text" name="responsavel" required>

      <label>RF do Responsável:</label>
      <input type="text" name="rf_responsavel" required>

      <label>Graduação do Responsável:</label>
      <input type="text" name="graduacao_responsavel" required>

      <label>Data da Retirada:</label>
      <input type="date" name="data_retirada" id="data_retirada" readonly onkeydown="return false" onmousedown="return false">



      <button type="submit">Confirmar</button>
    </form>

    <a href="chamados_listar.php">⬅ Voltar</a>
  </div>

    <script>
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('data_retirada').value = today;
    </script>


</body>
</html>
