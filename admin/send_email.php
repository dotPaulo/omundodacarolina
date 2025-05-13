<?php

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    require_once __DIR__ . '/../app/helpers/JwtHelper.php';

    if (!function_exists('validateToken') || !function_exists('isTokenExpired') || !function_exists('clearAuthCookies')) {
        throw new Exception('Funções JWT não disponíveis');
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

    // Validação do token
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

    $username = htmlspecialchars($decoded->username ?? '', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($decoded->email ?? '', ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($decoded->role ?? '', ENT_QUOTES, 'UTF-8');

    if ($role !== 'admin') {
        redirectToUnauthorized();
    }

    // Dados do formulário
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';

    if (empty($to) || empty($subject) || empty($body)) {
        header("Location: reply_email.php?status=error");
        exit();
    }

    require '../vendor/autoload.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Ajustar se for usar domínio personalizado
    $mail->SMTPAuth = true;
    $mail->Username = 'paul0.oliveir42308@gmail.com';
    $mail->Password = 'nnbb janf kkba flmf';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('paul0.oliveir42308@gmail.com', 'O Mundo da Carolina');
    $mail->addAddress($to);

    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();

    header("Location: reply_email.php?status=success");
    exit();

} catch (Exception $e) {
    // Log detalhado para análise
    error_log('Erro crítico ao enviar e-mail ou validar token: ' . $e->getMessage());

    // Redirecionamento para página de erro genérica
    header("Location: ../404.php");
    exit();
}
