<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_chamados";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// 🔢 Gera número automático de solicitação
$dataAtual = date("Ymd");
$numeroAleatorio = rand(100, 999);
$numero_solicitacao = "SOL-" . $dataAtual . "-" . $numeroAleatorio;

// 🔍 Dados do usuário logado
$rf = $_SESSION['rf'];
$nome = $_SESSION['nome'];

// 📋 Dados do formulário
$nome_logico = $_POST['nome_cpu'];
$data_solicitacao = $_POST['dt_solicitacao'];
$tipo_equipamento = $_POST['tipo_equipamento'];
$problema = $_POST['problema'];
$observacoes = $_POST['observacoes'];

// 💾 Inserção no banco (ajuste conforme seu schema de tb_chamado)
$sql = "INSERT INTO tb_chamado 
        (numero_solicitacao, nome_cpu, rf_solicitante, rf, dt_solicitacao, tipo_equipamento, problema, observacoes)
        VALUES 
        ('$numero_solicitacao', '$nome_cpu', '$nome', '$rf', '$data_solicitacao', '$tipo_equipamento', '$problema', '$observacoes')";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Chamado registrado com sucesso! Nº: $numero_solicitacao'); window.location.href='confirmacao.html';</script>";
} else {
  echo "Erro ao registrar chamado: " . $conn->error;
}

$conn->close();
?>
