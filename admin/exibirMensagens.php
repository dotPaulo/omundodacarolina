<?php
include('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';

// Autenticação do e-mail (usa variáveis de ambiente no futuro, por segurança)
$email = '8goncaloalvesgomes@gmail.com';
$senha = 'iauw rzza uodp bfxr';

if (strpos($email, '@gmail.com') !== false) {
    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
} elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
    $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
} else {
    die('Provedor de e-mail não suportado.');
}

$inbox = imap_open($hostname, $email, $senha);

if (!$inbox) {
    die('Erro ao conectar ao e-mail: ' . imap_last_error());
}


// Função para decodificar o header (caso venha codificado)
function decodeHeader($str) {
    $elements = imap_mime_header_decode($str);
    $decoded = '';
    foreach ($elements as $element) {
        $decoded .= $element->text;
    }
    return $decoded;
}

$email_numbers = imap_search($inbox, 'UNSEEN'); // Só e-mails não lidos
?>

<!DOCTYPE html>
<html lang="pt">
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
            if ($email_numbers) {
                $email_numbers = array_slice($email_numbers, -5); // últimos 5 e-mails
                $modals = ''; // armazenar todos os modais

                foreach ($email_numbers as $index => $email_number) {
                    $overview = imap_fetch_overview($inbox, $email_number, 0);
                    $message = $overview[0];
                    $subject = decodeHeader($message->subject ?? '(Sem Assunto)');
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
                        $body = imap_fetchbody($inbox, $email_number, 1);
                        if ($structure->encoding == 3) {
                            $body = imap_base64($body);
                        } elseif ($structure->encoding == 4) {
                            $body = imap_qprint($body);
                        }
                    }

                    // Tornar links clicáveis
                    $body = preg_replace_callback('/(https?:\/\/[^\s]+)/', function($matches) {
                        $url = htmlspecialchars($matches[1]);
                        return '<a href="' . $url . '" target="_blank" style="color: #4184F3; text-decoration: underline;">' . $url . '</a>';
                    }, $body);

                    $bodyPreview = substr(strip_tags($body), 0, 100) . '...';
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
                            <div class="modal-dialog modal-lg modal-dialog-custom">
                                <div class="modal-content modal-content-custom">
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
                                </div>
                            </div>
                        </div>';
                }

                echo $modals; // imprime os modais no fim
            } else {
                echo '<div class="alert alert-info">Nenhuma mensagem encontrada.</div>';
            }

            imap_close($inbox);
            ?>
        </div>
    </main>
    <?php include('./include/footer.php'); ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="./assets/js/dashboard.js"></script>
</body>
</html>
