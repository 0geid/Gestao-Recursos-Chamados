<?php
function enviarEmail($destinatario, $assunto, $mensagem) {
    $headers = "From: chamados@empresa.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // ⚠️ IMPORTANTE: o servidor precisa estar configurado para enviar e-mails via PHP mail()
    return mail($destinatario, $assunto, $mensagem, $headers);
}
?>
