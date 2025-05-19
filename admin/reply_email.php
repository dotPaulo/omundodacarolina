<?php
try {
    // Verificações de inclusão de arquivos
    if (!file_exists('./app/users.php') || !file_exists('./include/header.php') || !file_exists('./../assets/include/scrollbar.php')) {
        throw new Exception('Arquivos essenciais não encontrados.');
    }

    include('./app/users.php');
    $headerPath = './include/header.php';
    $scrollbarPath = './../assets/include/scrollbar.php';
    require_once __DIR__ . '/../app/helpers/JwtHelper.php';

    // Variáveis de e-mail (credenciais de teste)
    $email_number = $_POST['email_number'] ?? null;
    $imap_email = '';
    $senha = '';
    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX'; // INSIRA O HOSTNAME VÁLIDO

    $email_clean = '';

    if ($email_number !== null) {
        $inbox = @imap_open($hostname, $imap_email, $senha);
        if (!$inbox) {
            throw new Exception('Erro ao conectar ao servidor de e-mail: ' . imap_last_error());
        }

        $overviewArr = imap_fetch_overview($inbox, $email_number, 0);
        if (!$overviewArr || !isset($overviewArr[0])) {
            throw new Exception('Não foi possível obter o overview do e-mail.');
        }

        $overview = $overviewArr[0];
        $structure = imap_fetchstructure($inbox, $email_number);
        $body = imap_fetchbody($inbox, $email_number, 1, FT_PEEK);

        if ($structure && isset($structure->encoding)) {
            if ($structure->encoding == 3) {
                $body = imap_base64($body);
            } elseif ($structure->encoding == 4) {
                $body = imap_qprint($body);
            }
        }

        $raw_from = $overview->from ?? '';
        $decoded = mb_decode_mimeheader($raw_from);

        if (preg_match('/<(.+?)>/', $decoded, $matches)) {
            $email_clean = $matches[1];
        } else {
            $email_clean = filter_var($decoded, FILTER_VALIDATE_EMAIL) ? $decoded : '';
        }

        imap_close($inbox);
    }

} catch (Exception $e) {
    // Log do erro para debug interno
    error_log("Erro em reply_email.php: " . $e->getMessage());

    // Redireciona para página 404 amigável
    header("Location: ../404.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Responder email | O Mundo da Carolina</title>
    <link rel="shortcut icon" type="image/png" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./assets/CSS/dashboard.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<?php include $scrollbarPath; ?>
<?php include $headerPath; ?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Responder E-mail</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="exibirMensagens.php">Mensagens</a></li>
                <li class="breadcrumb-item active">Responder</li>
            </ol>

            <?php if (isset($_GET['status'])): ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const status = "<?php echo $_GET['status']; ?>";
                        if (status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "E-mail enviado com sucesso!",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (status === "error") {
                            Swal.fire({
                                icon: "error",
                                title: "Erro ao enviar o e-mail",
                                text: "Verifique as configurações e tente novamente.",
                                showConfirmButton: true
                            });
                        }
                    });
                </script>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header"></i> Formulário de Resposta</div>
                <div class="card-body">
                    <form action="send_email.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Para:</label>
                            <input type="email" name="to" class="form-control" value="<?php echo htmlspecialchars($email_clean); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assunto:</label>
                            <input type="text" name="subject" class="form-control" value="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensagem:</label>
                            <textarea name="body" rows="10" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Enviar</button>
                        <a href="exibirMensagens.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('./include/footer.php'); ?>
</div>

<!-- JS scripts compatíveis com o dashboard -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="./assets/js/dashboard.js"></script>
</body>
</html>
