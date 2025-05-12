<?php
$para = $_POST['to'];
$assunto = $_POST['subject'];
$mensagem = $_POST['body'];

$headers = "From: paul0.oliveir42308@gmail.com\r\n";
$headers .= "Reply-To: paul0.oliveir42308@gmail.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'paul0.oliveir42308@gmail.com';  // Seu e-mail
    $mail->Password = 'nnbb janf kkba flmf';           // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('paul0.oliveir42308@gmail.com', 'Administrador');
    $mail->addAddress($_POST['to']);

    $mail->Subject = $_POST['subject'];
    $mail->Body    = $_POST['body'];

    $mail->send();
    echo "<div style='color:green;'>E-mail enviado com sucesso. <a href='mensagens.php'>Voltar</a></div>";
} catch (Exception $e) {
    echo "<div style='color:red;'>Erro ao enviar e-mail: {$mail->ErrorInfo}. <a href='mensagens.php'>Voltar</a></div>";
}
?>
?>
