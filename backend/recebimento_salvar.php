<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_chamado             = $_POST['id_chamado'];
    $responsavel            = $_POST['responsavel_recebimento'];
    $rf_responsavel         = $_POST['rf_recebimento'];
    $graduacao_responsavel  = $_POST['graduacao_recebimento'];
    $data_recebimento       = $_POST['data_recebimento'];

    // Atualiza os dados de recebimento no chamado
    $sql = "UPDATE tb_chamado 
            SET recebido_em = ?, 
                responsavel_recebimento = ?, 
                rf_recebimento = ?, 
                graduacao_recebimento = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data_recebimento, $responsavel, $rf_responsavel, $graduacao_responsavel, $id_chamado);

    if ($stmt->execute()) {
        header("Location: chamado_editar.php?id=$id_chamado&recebido=1");
        exit();
    } else {
        echo "Erro ao registrar recebimento: " . $conn->error;
    }
}
?>
