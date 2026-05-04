<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
  header("Location: index.php");
  exit();
}

// Verifica se o usuário é do tipo 1 (admin)
if ($_SESSION['tipo'] != 1) {
  header("Location: meus_chamados.php");
  exit();
}

$nome = $_SESSION['nome'];

// Consulta de chamados com status e observação
$sql = "SELECT c.id, c.nome_cpu, c.dt_solucao, c.recebido_em, c.dt_retirada, c.observacao_confirmacao, u.nome AS solicitante
        FROM tb_chamado c 
        JOIN tb_usuario u ON c.rf_solicitante = u.id 
        WHERE c.dt_retirada IS NULL
        ORDER BY c.dt_solicitacao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Chamados</title>
  <link rel="stylesheet" href="../frontend/chamados_listar.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
  <header>
    <img src="../assets/logo.png" alt="logo">
    <h1>Gestão De Recursos e Chamado</h1>
    <h2>Divisão De Tecnologia Informação E Comunicação</h2>
    <h3>Lista de Chamados</h3>
    <p>Bem-vindo, <?= $nome ?>!</p>
  </header>

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
                    //link de Vizualização
                    $link = $c['dt_solucao'] ? 'chamado_vizualizar.php' : 'chamado_editar.php';
        ?>
        <a href="<?= $link ?>?id=<?= $c['id'] ?>" class="chamado-link">
          <div class="chamado-bolha <?= $c['dt_solucao'] ? 'finalizado' : '' ?>">
            <p><strong>OS:</strong> <?= $c['id'] ?></p>
            <p><strong>Solicitante:</strong> <?= $c['solicitante'] ?></p>
            <p><strong>Máquina:</strong> <?= $c['nome_cpu'] ?></p>
            <p><strong>Status:</strong> <?= $status ?></p>

            <?php if (!empty($c['observacao_confirmacao']) && empty($c['recebido_em'])): ?>
                            <p><strong>Observação da Confirmação:</strong> <?= $c['observacao_confirmacao'] ?></p>
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
