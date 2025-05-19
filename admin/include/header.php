<?php
require_once ('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../../app/helpers/JwtHelper.php';

$jwt = $_COOKIE['jwt'];

$decoded = validateToken($jwt, $key);

$user_id = htmlspecialchars($decoded->id, ENT_QUOTES, 'UTF-8');

header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Referrer-Policy: strict-origin-when-cross-origin");

$email = '';
$senha = '';

if (strpos($email, '@gmail.com') !== false) {
    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
} elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
    $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
} else {
    die('Provedor de e-mail não suportado.');
}

// EMAIL FINAL
//if (strpos($email, '@omundodacarolina.pt') !== false) {
//    $hostname = '{mail.omundodacarolina.pt:993/imap/ssl}INBOX';
//} else {
//    die('Provedor de e-mail não suportado.');
//}

$inbox = @imap_open($hostname, $email, $senha);
$unreadCount = 0;

if ($inbox) {
    $unreadEmails = imap_search($inbox, 'UNSEEN');
    $unreadCount = $unreadEmails ? count($unreadEmails) : 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="shortcut icon" type="image/png" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./../assets/CSS/dashboard.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>

<body>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="../index.php"><i class="fa-solid fa-arrow-left" style="color: #ffffff;"></i>
            Página Inicial</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fa-solid fa-user fa-xl" style="color: #fff;"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php

                    $users = selectAll('utilizadores');
                    if (!$con) {
                        die("Connection failed: " . mysqli_connect_error());
                    } else {
                        foreach ($users as $user) {
                            if ($user['id'] == $user_id) {
                                echo '<li><a class="dropdown-item" href=EditUtilizadores.php?id=' . $user['id'] . '">Editar informações da conta</a></li>';
                            }
                        }
                    }
                    ?>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="../app/controllers/logout.php">Logout</a></li>
                </ul>
            </li>
            <span style="color: white; text-align: center;"><?php echo $username ?></span>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">INTERFACE</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt " style="color: #fff"></i>
                            </div>
                            Dashboard
                        </a>
                        <a class="nav-link" href="logs.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt " style="color: #fff"></i>
                            </div>
                            Relatórios
                        </a>
                        <div class="sb-sidenav-menu-heading">GESTÃO</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open" style="color: #fff"></i></div>
                            Publicações
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down" style="color: #fff"></i>
                            </div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="Vpublicacoes.php">Ver todas as Publicações</a>
                                <a class="nav-link" href="Cpublicacao.php">Criar uma Publicação</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages"
                            aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-users" style="color: #fff"></i></div>
                            Utilizadores
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down" style="color: #fff"></i>
                            </div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link" href="Vutilizadores.php"> Utilizadores Registados</a>
                                <a class="nav-link" href="Cutilizador.php">Registar Utilizador</a>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading">NOTIFICAÇÕES</div>
                        <a class="nav-link" href="exibirMensagens.php" style="position: relative;">
                            <div class="sb-nav-link-icon" style="position: relative;">
                                <i class="fas fa-bell" style="color: #fff; font-size: 1.25rem;"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span id="notificacao-badge"
                                        class="position-absolute badge rounded-circle bg-danger d-flex justify-content-center align-items-center"
                                        style="
                                            top: -5px;
                                            right: -10px;
                                            font-size: 0.75rem;
                                            width: 1.3rem;
                                            height: 1.3rem;
                                        ">
                                        <?= $unreadCount ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            Mensagens
                        </a>
                    </div>
                </div>
            </nav>
        </div>
</body>

</html>