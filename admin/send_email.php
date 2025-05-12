<?php
require_once __DIR__ . '/../app/helpers/JwtHelper.php';

if (!function_exists('validateToken') || !function_exists('isTokenExpired') || !function_exists('clearAuthCookies')) {
    die('As funções do JWT não estão disponíveis');
}

function redirectToLogin()
{
    clearAuthCookies();
    header("Location: ../login.php");
    exit();
}

function redirectToUnauthorized()
{
    header("Location: ../unauthorized.php");
    exit();
}

// Verifica se o JWT está presente
if (!isset($_COOKIE['jwt'])) {
    redirectToLogin();
}

$jwt = $_COOKIE['jwt'];

if (isTokenExpired($jwt, $key)) {
    redirectToLogin();
}

$decoded = validateToken($jwt, $key);
if (!$decoded) {
    redirectToLogin();
}

$username = htmlspecialchars($decoded->username, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($decoded->email, ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($decoded->role, ENT_QUOTES, 'UTF-8');

// Somente administradores podem enviar e-mails
if ($role !== 'admin') {
    redirectToUnauthorized();
}

// Se chegou até aqui, o usuário é autorizado
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$to = $_POST['to'] ?? '';
$subject = $_POST['subject'] ?? '';
$body = $_POST['body'] ?? '';

if (empty($to) || empty($subject) || empty($body)) {
    header("Location: reply_email.php?status=error");
    exit();
}

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
    $mail->addAddress($to);

    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    header("Location: reply_email.php?status=success");
    exit();
} catch (Exception $e) {
    error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
    header("Location: reply_email.php?status=error");
    exit();
}
