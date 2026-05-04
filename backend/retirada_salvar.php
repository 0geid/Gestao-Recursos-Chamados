<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_chamado           = $_POST['id_chamado'];
    $responsavel          = $_POST['responsavel'];
    $rf_responsavel       = $_POST['rf_responsavel'];
    $graduacao_responsavel = $_POST['graduacao_responsavel'];
    $data_retirada        = $_POST['data_retirada'];

    // Atualiza os dados de retirada no chamado
    $sql = "UPDATE tb_chamado 
            SET dt_retirada = ?, 
                responsavel_retirada = ?, 
                rf_responsavel = ?, 
                graduacao_responsavel = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data_retirada, $responsavel, $rf_responsavel, $graduacao_responsavel, $id_chamado);

    if ($stmt->execute()) {
        // Redireciona para a listagem dos chamados do usuário
        header("Location: meus_chamados.php?retirada=1");
        exit();
    } else {
        echo "Erro ao registrar retirada: " . $conn->error;
    }
}
?>
