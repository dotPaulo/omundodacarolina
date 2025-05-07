<?php
include ('./app/users.php');
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
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
    <title>Dashboard | O Mundo da Carolina</title>
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
                <h1 class="mt-4">Bem-vindo/a <?php echo $username ?>!</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
                <div class="row">
                    <?php
                    $users = selectAll('utilizadores');
                    $publicacoes = selectAll('publicacoes');
                    $projectCount = 0;
                    $newsCount = 0;

                    foreach ($publicacoes as $publicacao) {
                        if ($publicacao['public_type'] == 'projetos') {
                            $projectCount++;
                        } elseif ($publicacao['public_type'] == 'noticias') {
                            $newsCount++;
                        }
                    }

                    ?>

                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Total de projetos</div>
                                <h4 class="mb-3" style="margin-left: 15px;"><?php echo $projectCount; ?></h4>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="Vpublicacoes.php">Ver detalhes</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Total de notícias</div>
                                <h4 class="mb-3" style="margin-left: 15px;"><?php echo $newsCount; ?></h4>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="Vpublicacoes.php">Ver detalhes</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Total de utilizadores</div>
                                <h4 class="mb-3" style="margin-left: 15px;"><?php echo count($users); ?></h4>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="Vutilizadores.php">Ver detalhes</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include ('./include/footer.php'); ?>
    </div>
    </div>

    <!------------------------------- Javascript Links ------------------------------------>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="./assets/js/dashboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="./assets/JS/chart-area-demo.js"></script>
    <script src="./assets/JS/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
</body>

</html>