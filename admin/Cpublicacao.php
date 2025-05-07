<?php
include ('./app/users.php');
$scrollbarPath = './../assets/include/scrollbar.php';
$headerPath = './include/header.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';

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

header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Referrer-Policy: strict-origin-when-cross-origin");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard | omundodacarolina</title>
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
                <h1 class="mt-4">Publicações</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item">Criar uma nova publicação</li>
                </ol>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Criar uma nova publicação</h4>
                            </div>
                            <div class="card-body">
                                <form action="app/users.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">


                                        <div class="col-md-12 mb-3"> <br />
                                            <label for="public_type">Tipo de Publicações</label>
                                            <select name="public_type" required class="form-control">
                                                <option value="">Selecione o tipo de publicação</option>
                                                <option value="noticias">Notícia</option>
                                                <option value="projetos">Projeto</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="nome">Nome</label>
                                            <input type="text" name="nome" class="form-control" required>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="">Descrição</label>
                                            <textarea name="descricao" id="summernote" class="form-control"
                                                rows="4"></textarea>
                                        </div>

                                        <div class="col-md-12 mb-3"> <br />
                                            <label for="">Titulo Meta</label>
                                            <input type="text" name="meta_titulo" max="255" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Descrição Meta</label>
                                            <textarea name="meta_descricao" class="form-control" rows="4"></textarea>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="">Imagem</label>
                                            <input type="file" name="imagem" class="form-control" required>
                                        </div>

                                        <div style="margin-top: 22px;" class="col-md-6 mb-3">
                                            <button type="submit" name="add-post-btn" class="btn btn-primary">Adicionar
                                                publicação</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
        <?php include ('./include/footer.php'); ?>

        <!------------------------------- Javascript Links ------------------------------------------------------------------>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
        <script src="./assets/js/dashboard.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"
            crossorigin="anonymous"></script>
        <script src="./assets/JS/chart-area-demo.js"></script>
        <script src="./assets/JS/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#summernote").summernote({
                    placeholder: 'Escreva aqui a descrição da publicação',
                    height: 200
                });
                $('.dropdown-toggle').dropdown();
            });
        </script>
</body>

</html>

</html>