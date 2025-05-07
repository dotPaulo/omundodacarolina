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

$id = htmlspecialchars($decoded->id, ENT_QUOTES, 'UTF-8');
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
                <h1 class="mt-4">Utilizadores</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item">Utilizadores Registados</li>
                </ol>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Editar Utilizador
                                    <a href="Vutilizadores.php" class="btn btn-danger float-end">Voltar atrás</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php
                                if (!isset($_GET['id']) || empty($_GET['id'])) {
                                    echo "Não foram feitas alterações";
                                    echo "<script>setTimeout(function(){ window.location.href = 'Vutilizadores.php'; }, 2000);</script>";
                                } else {
                                    $id_atual = $_GET['id'];
                                    $user = selectOne('utilizadores', ['id' => $id_atual]);

                                    if (!$user) {
                                        echo "O utilizador com esse ID não existe";
                                        echo "<script>setTimeout(function(){ window.location.href = 'Vutilizadores.php'; }, 2000);</script>";
                                    } else {
                                        ?>
                                        <form action="EditUtilizadores.php" method="POST">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="username">Nome de utilizador</label>
                                                    <input type="text" name="username"
                                                        value="<?= htmlspecialchars($user['username']) ?>" class="form-control">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="email">Email</label>
                                                    <input type="text" name="email"
                                                        value="<?= htmlspecialchars($user['email']) ?>" class="form-control">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <?php
                                                    if ($id_atual != $decoded->id) {
                                                        ?>
                                                        <label for="role">Cargo</label>
                                                        <select name="role" required class="form-control">
                                                            <option value="">--Selecione um cargo--</option>
                                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                                                Admin</option>
                                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User
                                                            </option>
                                                        </select>
                                                        <?php
                                                    } else {
                                                        echo '<p style="margin-top: 20px;">Não pode alterar o cargo do utilizador atual</p>';
                                                    }
                                                    ?>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <button type="submit" name="update-btn-utilizadores"
                                                        class="btn btn-primary">Atualizar</button>
                                                </div>
                                            </div>
                                        </form>
                                        <?php
                                    }
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include ('./include/footer.php'); ?>

        <!----------------------------------------------------- Javascript Links -------------------------------------------------------->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
        <script src="./assets/js/dashboard.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"
            crossorigin="anonymous"></script>
        <script src="./assets/JS/chart-area-demo.js"></script>
        <script src="./assets/JS/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
            crossorigin="anonymous"></script>
</body>

</html>
<?php
ob_end_flush();
?>