<?php
include('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';
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
<body class="bg-light">
<?php include $scrollbarPath; ?>
<?php include $headerPath; ?>
<div class="container mt-5">
    <h3 class="mb-4">Responder E-mail</h3>
    <form action="send_email.php" method="post">
        <div class="mb-3">
            <label class="form-label">Para:</label>
            <input type="email" name="to" class="form-control" value="<?php echo htmlspecialchars($overview->from); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Assunto:</label>
            <input type="text" name="subject" class="form-control" value="Re: <?php echo htmlspecialchars($overview->subject); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mensagem:</label>
            <textarea name="body" rows="10" class="form-control"><?php echo "\n\n---------- Mensagem original ----------\n" . $body; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Enviar</button>
        <a href="mensagens.php" class="btn btn-secondary">Cancelar</a>
    </form>
    <?php include('./include/footer.php'); ?>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>
