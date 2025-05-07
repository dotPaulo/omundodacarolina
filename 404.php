<?php
$headerPath = __DIR__ . './assets/include/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">

</head>
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

<body>
    <?php include $headerPath; ?>
    <div class="error-message">
        <h1>404</h1>
        <p>Parece que esta página não foi encontrada, pedimos desculpa pela a inconveniência </p>
    </div>
    <div class="next-steps">
        <p><a href="index.php">Voltar para a página principal</a></p>
    </div>
    <main></main>
    <?php include __DIR__ . '/assets/include/footer.php'; ?>
</body>

</html>