<?php
include('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';
require_once __DIR__ . '/../vendor/autoload.php';  // Carregar PHPMailer via Composer

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

// Verificação se o usuário está autenticado via JWT
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
$email_user = htmlspecialchars($decoded->email, ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($decoded->role, ENT_QUOTES, 'UTF-8');

// Somente administradores podem acessar essa página
if ($role !== 'admin') {
    redirectToUnauthorized();
}

$email_number = $_POST['email_number'];
$email = 'paul0.oliveir42308@gmail.com';
$senha = 'nnbb janf kkba flmf';
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';

$inbox = imap_open($hostname, $email, $senha) or die('Erro: ' . imap_last_error());

$overview = imap_fetch_overview($inbox, $email_number, 0)[0];
$structure = imap_fetchstructure($inbox, $email_number);
$body = imap_fetchbody($inbox, $email_number, 1, FT_PEEK);

if ($structure->encoding == 3) {
    $body = imap_base64($body);
} elseif ($structure->encoding == 4) {
    $body = imap_qprint($body);
}

imap_close($inbox);

// Agora vamos usar o PHPMailer para enviar o e-mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to'], $_POST['subject'], $_POST['body'])) {
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $message = $_POST['body'];

    if (empty($to) || empty($subject) || empty($message)) {
        echo "Por favor, preencha todos os campos.";
    } else {
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor de e-mail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $email; // E-mail do remetente
            $mail->Password = $senha; // Senha do remetente
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatário
            $mail->setFrom($email, 'O Mundo da Carolina');
            $mail->addAddress($to);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($message);

            $mail->send();
            echo 'E-mail encaminhado com sucesso!';
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Encaminhar email | O Mundo da Carolina</title>
    <link rel="shortcut icon" type="image/png" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./assets/CSS/dashboard.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
    <?php include $scrollbarPath; ?>
    <?php include $headerPath; ?>

    <div class="container mt-5">
        <h3 class="mb-4">Encaminhar E-mail</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Para:</label>
                <input type="email" name="to" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Assunto:</label>
                <input type="text" name="subject" class="form-control" value="Fwd: <?php echo htmlspecialchars($overview->subject); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mensagem:</label>
                <textarea name="body" rows="10" class="form-control"><?php echo "\nDe: " . $overview->from . "\nData: " . $overview->date . "\n\n" . $body; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="mensagens.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./assets/js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
