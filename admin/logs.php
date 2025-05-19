<?php
// Inclusão dos arquivos essenciais (ajuste os caminhos conforme seu projeto)
$headerPath = './include/header.php';
$scrollbarPath = './../assets/include/scrollbar.php';
require_once __DIR__ . '/../app/helpers/JwtHelper.php';


// Inclua aqui sua conexão com banco e autenticação, se houver
require_once __DIR__ . '/../app/database/connection.php';

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


$sql = "SELECT logs.*, utilizadores.username AS nome_usuario
        FROM logs 
        LEFT JOIN utilizadores ON logs.user_id = utilizadores.id 
        ORDER BY logs.data_hora DESC";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Relatórios de Alterações | O Mundo da Carolina</title>
    <link rel="shortcut icon" href="./../assets/Imagens/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./assets/CSS/dashboard.css" rel="stylesheet" />
    <link href="./assets/CSS/modal.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS (mesmo do segundo site) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* Você pode adicionar ajustes específicos aqui */
        body.sb-nav-fixed {
            padding-top: 56px; /* Altura da navbar fixa */
        }
        table {
            font-size: 0.9rem;
        }
        .table thead th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include $scrollbarPath; ?>
    <?php include $headerPath; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Relatórios de Alterações</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Logs de Alterações</li>
                </ol>

                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Ação</th>
                                    <th>Tabela</th>
                                    <th>Data/Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nome_usuario'] ?? 'Admin') ?></td>
                                        <td><?= htmlspecialchars($row['acao']) ?></td>
                                        <td><?= htmlspecialchars($row['tabela']) ?></td>
                                        <td><?= htmlspecialchars($row['data_hora']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Nenhum log encontrado.</div>
                <?php endif; ?>
            </div>
        </main>
        <?php include('./include/footer.php'); ?>
    </div>

    <!-- JS HEADER -->
    <script src="./assets/js/dashboard.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 (para futuras mensagens) -->
    <script>
        // Exemplo de uso SweetAlert para mensagens dinâmicas (opcional)
        // Pode adicionar notificações conforme necessário
    </script>
</body>
</html>
