<?php
$para = $_POST['to'];
$assunto = $_POST['subject'];
$mensagem = $_POST['body'];

$headers = "From: paul0.oliveir42308@gmail.com\r\n";
$headers .= "Reply-To: paul0.oliveir42308@gmail.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";

if (mail($para, $assunto, $mensagem, $headers)) {
    echo "<div style='padding:20px; font-family:sans-serif;'>E-mail enviado com sucesso. <a href='mensagens.php'>Voltar</a></div>";
} else {
    echo "<div style='padding:20px; font-family:sans-serif; color:red;'>Erro ao enviar e-mail. <a href='mensagens.php'>Voltar</a></div>";
}
?>
