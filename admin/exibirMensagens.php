<?php
// Inclui arquivos essenciais com checagem de existência
try {
    if (!file_exists('./app/users.php') || !file_exists('./include/header.php') || !file_exists('./../assets/include/scrollbar.php')) {
        throw new Exception('Arquivos de inclusão não encontrados.');
    }

    include('./app/users.php');
    $headerPath = './include/header.php';
    $scrollbarPath = './../assets/include/scrollbar.php';

    require_once __DIR__ . '/../app/helpers/JwtHelper.php';

    // Verificação de funções essenciais do JWT
    if (!function_exists('validateToken') || !function_exists('isTokenExpired') || !function_exists('clearAuthCookies')) {
        throw new Exception('Funções essenciais de autenticação não estão disponíveis.');
    }

    // Funções de redirecionamento
    function redirectToLogin() {
        clearAuthCookies();
        header("Location: ../login.php");
        exit();
    }

    function redirectToUnauthorized() {
        header("Location: ../unauthorized.php");
        exit();
    }

    function redirectTo404() {
        header("Location: ../404.php");
        exit();
    }

    // Validação do token JWT
    if (!isset($_COOKIE['jwt'])) {
        redirectToLogin();
    }

    $jwt = $_COOKIE['jwt'];
    if (!isset($key)) {
        throw new Exception('Chave JWT não definida.');
    }

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

    // Simulação de credenciais para teste (substituir em produção)
    $email = 'paul0.oliveir42308@gmail.com';
    $senha = 'nnbb janf kkba flmf';

    // Definição do hostname de acordo com o provedor de e-mail
    if (strpos($email, '@gmail.com') !== false) {
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
    } elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
        $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
    } else {
        throw new Exception('Provedor de e-mail não suportado.');
    }

    // EMAIL FINAL (INSIRA UM HOSTNAME DE EMAIL VÁLIDO, ESTE ESTÁ APENAS PARA DEMONSTRAÇÃO)
    //if (strpos($email, '@omundodacarolina.pt') !== false) {
    //    $hostname = '{mail.omundodacarolina.pt:993/imap/ssl}INBOX';
    //} else {
    //    die('Provedor de e-mail não suportado.');
    //}

    // Conexão com o servidor de e-mail
    $inbox = @imap_open($hostname, $email, $senha);
    if (!$inbox) {
        throw new Exception('Erro ao conectar ao e-mail: ' . imap_last_error());
    }

    // Deleção de e-mails (se for POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_number'])) {
        $emailToDelete = intval($_POST['email_number']);
        if (imap_delete($inbox, $emailToDelete)) {
            imap_expunge($inbox);
            $deleteSuccess = true;
        } else {
            $deleteError = true;
        }
    }
} catch (Exception $e) {
    // Redireciona para a página de erro em caso de qualquer exceção
    error_log("Erro capturado: " . $e->getMessage());
    redirectTo404();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Mensagens | O Mundo da Carolina</title>
    <link rel="shortcut icon" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./assets/CSS/dashboard.css" rel="stylesheet" />
    <link href="./assets/CSS/modal.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                if (isset($deleteSuccess)) {
                    echo "<div class='alert alert-success'>E-mail apagado com sucesso.</div>";
                } elseif (isset($deleteError)) {
                    echo "<div class='alert alert-danger'>Erro ao apagar o e-mail.</div>";
                }

                $email_numbers = imap_search($inbox, 'ALL');
                if ($email_numbers) {
                    $email_numbers = array_slice($email_numbers, -5);
                    $modals = '';

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

                        $bodyPreview = substr(strip_tags($body), 0, 100) . '...';
                        $modalId = "emailModal$index";

                        echo '<div class="card mb-4">';
                        echo '<div class="card-header"><strong>' . htmlspecialchars($subject) . '</strong></div>';
                        echo '<div class="card-body">';
                        echo '<p><strong>De:</strong> ' . htmlspecialchars($message->from) . '</p>';
                        echo '<p><strong>Data:</strong> ' . date('d/m/Y H:i:s', strtotime($message->date)) . '</p>';
                        echo '<p><strong>Resumo:</strong> ' . htmlspecialchars($bodyPreview) . '</p>';
                        echo '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">Ver mais</button>';
                        echo '</div>';
                        echo '</div>';

                        // MODAL COM SWEETALERT
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
                                    <div class="modal-footer d-flex justify-content-between align-items-center px-3" style="border-top: 1px solidrgb(0, 0, 0);">
                                        <div>
                                            <button type="button" class="btn btn-danger me-2 delete-email-btn" data-email="' . $email_number . '">Apagar</button>
                                            <form id="delete-form-' . $email_number . '" method="post" action="" style="display:none;">
                                                <input type="hidden" name="email_number" value="' . $email_number . '">
                                            </form>
                                        </div>
                                        <form method="post" action="reply_email.php">
                                            <input type="hidden" name="email_number" value="' . $email_number . '">
                                            <button type="submit" class="btn btn-success ms-2">Responder</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }

                    echo $modals;
                } else {
                    echo '<div class="alert alert-info">Nenhuma mensagem encontrada.</div>';
                }

                function decodeHeader($text) {
                    $decoded = imap_mime_header_decode($text);
                    $str = '';
                    foreach ($decoded as $part) {
                        $str .= $part->text;
                    }
                    return $str;
                }
                ?>
            </div>
        </main>
        <?php include('./include/footer.php'); ?>
    </div>

    <!-- JS HEADER -->
    <script src="./assets/js/dashboard.js"></script>

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 DELEÇÃO -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-email-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const emailNumber = this.getAttribute('data-email');

                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, apagar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + emailNumber).submit();
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
