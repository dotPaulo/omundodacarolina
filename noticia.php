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
  <link rel="stylesheet" href="./assets/CSS/noticia.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Notícias | omundodacarolina</title>
  <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
</head>

<body>
  <?php include $headerPath; ?>
  <?php include $scrollbarPath; ?>
  <main>
    <div class="noticias-container">
      <div class="noticias-header">
        <h1>Notícias </h1>
      </div>
      <div class="noticias-area">
        <?php
        $publicacoes = selectAll('publicacoes');
        if (!empty($publicacoes)) {
          foreach ($publicacoes as $publicacao) {
            if ($publicacao['public_type'] == 'noticias') {
              echo '<div onclick="location.href=\'publicacoes.php?id=' . $publicacao['id'] . '\'" class="noticias-box">';
              echo '<div class="noticias-imagem">';
              echo '<img src="' . htmlspecialchars($publicacao['imagem']) . '" alt="' . htmlspecialchars($publicacao['meta_titulo']) . '">';
              echo '</div>';
              echo '<div class="noticias-titulo">';
              echo '<h2>' . htmlspecialchars($publicacao['meta_titulo']) . '</h2>';
              echo '</div>';
              echo '<div class="noticias-antevisao">';
              echo '<span>' . htmlspecialchars($publicacao['meta_descricao']) . '</span>';
              echo '</div>';
              echo '</div>';
            }
          }
        }
        ?>
      </div>
    </div>
    </div>
  </main>
  <?php
  include './assets/include/footer.php'
    ?>
</body>

</html>