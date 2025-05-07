<?php
$headerPath = './assets/include/header.php';
$scrollbarPath = './assets/include/scrollbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Algo correu errado</title>
    <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
    <style>
        .error-message {
            margin-top: 120px;
            text-align: center;
            font-size: 24px;
            color: #333;
        }

        .next-steps {
            margin-top: 40px;
            text-align: center;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include $headerPath; ?>
    <?php include $scrollbarPath; ?>
    <main>
        <div class="error-message">
            <h1>Não tem permissão para aceder a esta página.</h1>
        </div>
        <div class="next-steps">
            <p><a href="index.php">Voltar para a página principal</a></p>
        </div>

    </main>
    <?php
    include ('./assets/include/footer.php');
    ?>

</body>

</html>