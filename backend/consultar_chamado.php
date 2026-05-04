<?php
session_start();
$retirada_msg = isset($_GET['retirada']) && $_GET['retirada'] == 1;
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// Verifica se o usuário é administrador (tipo 1)
if ($_SESSION['tipo'] != 1) {
    header("Location: dashboard.php");
    exit();
}

$nome = $_SESSION['nome'];

// Monta filtros dinâmicos
$where = "WHERE c.dt_retirada IS NOT NULL";
$params = [];
$types = "";

// Filtro por número do chamado
if (!empty($_GET['id'])) {
    $where .= " AND c.id = ?";
    $params[] = $_GET['id'];
    $types .= "i";
}

// Filtro por endereço/base do solicitante
if (!empty($_GET['base'])) {
    $where .= " AND c.ender_solicitante LIKE ?";
    $params[] = "%" . $_GET['base'] . "%";
    $types .= "s";
}

// Filtro por intervalo de datas
if (!empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
    $where .= " AND DATE(c.dt_retirada) BETWEEN ? AND ?";
    $params[] = $_GET['data_inicio'];
    $params[] = $_GET['data_fim'];
    $types .= "ss";
} elseif (!empty($_GET['data_inicio'])) {
    $where .= " AND DATE(c.dt_retirada) >= ?";
    $params[] = $_GET['data_inicio'];
    $types .= "s";
} elseif (!empty($_GET['data_fim'])) {
    $where .= " AND DATE(c.dt_retirada) <= ?";
    $params[] = $_GET['data_fim'];
    $types .= "s";
}

$sql = "SELECT c.*, e.tipo AS equipamento, u.nome AS nome_solicitante, u.rf AS rf_solicitante, s.descricao AS solucao
        FROM tb_chamado c
        JOIN tb_equipamento e ON c.id_equipamento = e.id
        JOIN tb_usuario u ON c.rf_solicitante = u.id
        LEFT JOIN tb_solucao s ON c.id_solucao = s.id
        $where
        ORDER BY c.dt_retirada DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados Retirados</title>
    <link rel="stylesheet" href="../frontend/consultar_chamado.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
    <header>
        <img src="../assets/logo.png" alt="logo">
        <h1>Gestão De Recursos e Chamados</h1>
        <h2>Divisão De Tecnologia Informação E Comunicação</h2>
        <h3>Chamados Concluídos</h3>
        <p>Bem-vindo, <?= $nome ?>!</p>
    </header>
<!--Filtro de Pesquisa-->
    <form method="GET" class="filtro-form">

        <label for="base">Base:</label>
            <input type="text" name="base" id="base" placeholder="Ex: SMSU">

        <label for="nome_cpu">Máquina:</label>
            <input type="text" name="nome_cpu" id="nome_cpu" placeholder="SMSUGBC/SIMPCP">    

        <label for="id">Número do Chamado:</label>
            <input type="text" name="id" id="id" placeholder="Ex: 1023">

        <label for="data_inicio">Data de Retirada (De):</label>
            <input type="date" name="data_inicio" id="data_inicio">

        <label for="data_fim">Data de Retirada (Até):</label>
            <input type="date" name="data_fim" id="data_fim">

        <button type="submit">Filtrar</button>
    </form>
<!-- Centro Resultados-->
    <div class="center">
        <div class="chamados-container">
            <?php while ($c = $result->fetch_assoc()): ?>
                <a href="chamado_vizualizar.php?id=<?= $c['id'] ?>" class="chamado-link">
                    <div class="chamado-bolha finalizado">
                        <p><strong>OS:</strong> <?= $c['id'] ?></p>
                        <p><strong>Solicitante:</strong> <?= $c['nome_solicitante'] ?></p>
                        <p><strong>RF:</strong> <?= $c['rf_solicitante'] ?></p>
                        <p><strong>Máquina:</strong> <?= $c['nome_cpu'] ?></p>
                        <p><strong>Equipamento:</strong> <?= $c['equipamento'] ?></p>
                        <p><strong>Endereço/Base:</strong> <?= $c['ender_solicitante'] ?></p>
                        <p><strong>Status:</strong> ✅ Retirado</p>
                        <p><strong>Data de Retirada:</strong> <?= date('d/m/Y', strtotime($c['dt_retirada'])) ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <a href="dashboard.php">⬅ Voltar</a>
    </div>
</body>
</html>
