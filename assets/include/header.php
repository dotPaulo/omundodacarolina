<?php

require_once __DIR__ . '/../../app/helpers/JwtHelper.php';
require_once ('./app/controllers/users.php');

$projectNames = [];
$publicacoes = selectAll('publicacoes');
if (!empty($publicacoes)) {
    foreach ($publicacoes as $publicacao) {
        if ($publicacao['public_type'] == 'projetos') {
            $projectNames[] = [
                'id' => $publicacao['id'],
                'nome' => $publicacao['meta_titulo']
            ];
        }
    }
}

header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/CSS/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>

<body>
    <header class="header">
        <a href="index.php"><img src="assets/Imagens/logo.png" class="logo" alt="omundodacarolina"></a>
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <nav class="navbar">
            <ul>
                <li>
                    <a class="noticia" href="noticia.php">Notícias</a>
                </li>
                <div class="dropdown">
                    <li>
                        <a class="projetos" href="projetos.php">Projetos<i class="fas fa-angle-down"></i></a>
                    </li>
                    <div class="dropdown-content">
                        <?php
                        foreach ($projectNames as $project) {
                            echo '<a href="publicacoes.php?id=' . htmlspecialchars($project['id']) . '">' . htmlspecialchars($project['nome']) . '</a>';
                        }
                        ?>
                    </div>
                </div>
                <li>
                    <a class="sobre" href="sobre.php">Sobre</a>
                </li>
            </ul>
        </nav>
        <div class="header-sociais">
            <?php
            if (isset($_COOKIE['jwt'])) {
                $jwt = $_COOKIE['jwt'];
                $decoded = validateToken($jwt, $key);
                if ($decoded) {
                    $username = htmlspecialchars($decoded->username, ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="dropdown">
                        <a class="user-menu" href="#"> <?php echo $username; ?><i style="margin-left: 15px;margin-right: 20px;"
                                class="fa-solid fa-user fa-xl" style="color: #0000000;"></i></a>
                        <div class="dropdown-content">
                            <a href="../omundodacarolina/admin/dashboard.php">Dashboard</a>
                            <a href="../omundodacarolina/app/controllers/logout.php">Logout</a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <a href="https://www.facebook.com/omundodacarolina/" target="_blank"><i
                    class="fa-brands fa-facebook fa-2xl"></i></a>
            <a href="https://www.youtube.com/channel/UC0bI4TjGgFKTBNAOBFbGSgA/videos" target="_blank"><i
                    class="fa-brands fa-youtube fa-2xl"></i></a>
        </div>
    </header>
    <script defer src="./assets/JS/menu.js"></script>
</body>

</html>