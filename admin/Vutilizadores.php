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

$user_id = htmlspecialchars($decoded->id, ENT_QUOTES, 'UTF-8');
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
                                <h4>Utilizadores Registados</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Cargo</th>
                                            <th>Editar</th>
                                            <th>Apagar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $users = selectAll('utilizadores');
                                        if (!$con) {
                                            die("Connection failed: " . mysqli_connect_error());
                                        } else {
                                            foreach ($users as $user) {
                                                echo "<tr>";
                                                echo "<td>" . $user['id'] . "</td>";
                                                echo "<td>" . $user['username'] . "</td>";
                                                echo "<td>" . $user['email'] . "</td>";
                                                echo "<td>" . $user['role'] . "</td>";
                                                echo "<td><a href='EditUtilizadores.php?id=" . $user['id'] . "' class='btn btn-info'>Editar</a></td>";

                                                if ($user['id'] != $user_id) {
                                                    echo "<td>
                                                            <form action='app/users.php' method='POST' onsubmit='return confirmDelete()'>
                                                                <input type='hidden' name='user_id' value='" . $user['id'] . "'>
                                                                <button type='submit' class='btn btn-danger' name='delete-btn'>Apagar</button>
                                                            </form>
                                                            </td>";
                                                } else {
                                                    echo "<td><button type='disabled' class='btn btn-danger' name='delete-btn-utilizadores' disabled>Apagar</button> </td>"; // Or any other placeholder for the current user
                                                }

                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php include ('./include/footer.php'); ?>

        <!------------------------------- Javascript Links ------------------------------------>

        <script>
            function confirmDelete() {
                return confirm("Tem a certeza que deseja apagar este utilizador?");
            }
        </script>
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