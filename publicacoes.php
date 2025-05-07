<?php
$headerPath = './assets/include/header.php';
$scrollbarPath = './assets/include/scrollbar.php';
require_once ('./app/controllers/users.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/CSS/posts.css">
    <title>Projetos | omundodacarolina</title>
    <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
</head>

<body>
    <?php include $headerPath; ?>
    <?php include $scrollbarPath; ?>
    <main>
        <section>
            <div class="post-container">
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $publicacao = selectOne('publicacoes', ['id' => $id]);

                    if ($publicacao) {
                        echo '<div class="post-box">';
                        echo '<div class="image-container">';
                        echo '<img src="' . htmlspecialchars($publicacao['imagem']) . '" alt="' . htmlspecialchars($publicacao['nome']) . '">';
                        echo '</div>';
                        echo '<div class="content">';
                        echo '<div class="title-container">';
                        echo '<h1>' . htmlspecialchars($publicacao['nome']) . '</h1>';
                        echo '</div>';
                        echo '<p>' . ($publicacao['descricao']) . '</p>';

                        if (!empty($publicacao['id_categorias'])) {
                            echo '<div class="categorias">';
                            $categorias_ids = explode(',', $publicacao['id_categorias']);
                            foreach ($categorias_ids as $categoria_id) {
                                $categoria = selectOne('categorias', ['id' => trim($categoria_id)]);
                                if ($categoria) {
                                    echo '<span class="category">' . htmlspecialchars($categoria['nome']) . '</span>';
                                }
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';

                    } else {
                        echo "<p>Publicação não encontrada.</p>";
                    }
                } else {
                    echo "<p>Pedido inválido.</p>";
                }
                ?>
            </div>
        </section>
    </main>
    <?php include './assets/include/footer.php'; ?>
</body>

</html>