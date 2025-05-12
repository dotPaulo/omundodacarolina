<?php
include('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';

// if User
if (!function_exists('validateToken') || !function_exists('isTokenExpired') || !function_exists('clearAuthCookies')) {
    die('As funções do JWT não estão disponíveis');
}

$err_jwt = "";

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

if ($role !== 'admin') {
    redirectToUnauthorized();
}

$email = 'paul0.oliveir42308@gmail.com'; 
$senha = 'nnbb janf kkba flmf';


// if de email teste.

if (strpos($email, '@gmail.com') !== false) {
    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
} elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
    $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
} else {
    die('Provedor de e-mail não suportado.');
}

// if para o email oficial.
//$hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
//} elseif (strpos($email, '@omundodacarolina.pt') !== false) {
//    $hostname = '{mail.omundodacarolina.pt:993/imap/ssl}INBOX';
//} else {
//    die('Provedor de e-mail não suportado.');
//}

$inbox = imap_open($hostname, $email, $senha);

if (!$inbox) {
    die('Erro ao conectar ao e-mail: ' . imap_last_error());
}

$email_numbers = imap_search($inbox, 'UNSEEN');

//$unread_count = $emails ? count($emails) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Mensagens | O Mundo da Carolina</title>
    <link rel="shortcut icon" type="image/png" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./assets/CSS/dashboard.css" rel="stylesheet" />
    <link href="./assets/CSS/modal.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php include $scrollbarPath; ?>
    <?php include $headerPath; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Caixa de Entrada</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Mensagens</li>
                </ol>
                <?php
                $email_numbers = imap_search($inbox, 'ALL');

                if ($email_numbers) {
                    $email_numbers = array_slice($email_numbers, -5); 
                    $modals = ''; 

                    foreach ($email_numbers as $index => $email_number) {
                        $overview = imap_fetch_overview($inbox, $email_number, 0);
                        $message = $overview[0];
                        $subject = decodeHeader($message->subject);
                        $structure = imap_fetchstructure($inbox, $email_number);

                        $body = '';
                        if (isset($structure->parts) && count($structure->parts)) {
                            foreach ($structure->parts as $partNumber => $part) {
                                $partBody = imap_fetchbody($inbox, $email_number, $partNumber + 1);
                                if ($part->encoding == 3) {
                                    $body .= imap_base64($partBody);
                                } elseif ($part->encoding == 4) {
                                    $body .= imap_qprint($partBody);
                                } else {
                                    $body .= $partBody;
                                }
                            }
                        } else {
                            $body = imap_fetchbody($inbox, $email_number, FT_PEEK);
                            if ($structure->encoding == 3) {
                                $body = imap_base64($body);
                            } elseif ($structure->encoding == 4) {
                                $body = imap_qprint($body);
                            }
                        }

                        $body = preg_replace_callback('/(https?:\/\/[^\s]+)/', function($matches) {
                            $url = htmlspecialchars($matches[1]);
                            return '<a href="' . $url . '" target="_blank" style="color: #4184F3; text-decoration: underline;">' . $url . '</a>';
                        }, $body);

                        // Preview do corpo (100 primeiros caracteres)
                        $bodyPreview = substr($body, 0, 100) . '...';
                        $modalId = "emailModal$index";

                        // Card
                        echo '<div class="card mb-4">';
                        echo '<div class="card-header"><strong>' . htmlspecialchars($subject) . '</strong></div>';
                        echo '<div class="card-body">';
                        echo '<p><strong>De:</strong> ' . htmlspecialchars($message->from) . '</p>';
                        echo '<p><strong>Data:</strong> ' . date('d/m/Y H:i:s', strtotime($message->date)) . '</p>';
                        echo '<p><strong>Resumo:</strong> ' . htmlspecialchars($bodyPreview) . '</p>';
                            echo '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">Ver mais</button>';
                        echo '</div>';
                        echo '</div>';

                        // Modal
                        $modals .= '
                        <div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-custom"> <!-- Modal com a classe personalizada -->
                                <div class="modal-content modal-content-custom"> <!-- Modal com o conteúdo personalizado -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="' . $modalId . 'Label">' . htmlspecialchars($subject) . '</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>De:</strong> ' . htmlspecialchars($message->from) . '</p>
                                        <p><strong>Data:</strong> ' . date('d/m/Y H:i:s', strtotime($message->date)) . '</p>
                                        <hr>
                                        <div style="white-space: pre-wrap;">' . $body . '</div>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="post" action="#" style="display:inline;">
                                            <input type="hidden" name="email_number" value="' . $email_number . '">
                                            <button type="submit" class="btn btn-danger">Apagar</button>
                                        </form>

                                        <form method="post" action="forward_email.php" style="display:inline;">
                                            <input type="hidden" name="email_number" value="' . $email_number . '">
                                            <button type="submit" class="btn btn-secondary">Encaminhar</button>
                                        </form>

                                        <form method="post" action="reply_email.php" style="display:inline;">
                                            <input type="hidden" name="email_number" value="' . $email_number . '">
                                            <button type="submit" class="btn btn-success">Responder</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }

                    echo $modals; // imprime todos os modais ao final
                } else {
                    echo '<div class="alert alert-info">Nenhuma mensagem encontrada.</div>';
                }
                ?>
            </div>
        </main>
        <?php include('./include/footer.php'); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>

