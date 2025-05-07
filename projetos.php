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
  <link rel="stylesheet" href="./assets/CSS/projetos.css">
  <title>Projetos | omundodacarolina</title>
  <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
</head>

<body>
  <?php include $headerPath; ?>
  <?php include $scrollbarPath; ?>
  <main>
    <div id="projetos" class="projeto-container">
      <div class="projeto-header">
        <h1>Projetos </h1>
      </div>
      <div class="projeto-area">
        <?php
        $publicacoes = selectAll('publicacoes');
        if (!empty($publicacoes)) {
          foreach ($publicacoes as $publicacao) {
            if ($publicacao['public_type'] == 'projetos') {
              echo '<div onclick="location.href=\'publicacoes.php?id=' . $publicacao['id'] . '\'" class="projeto-box">';
              echo '<div class="projeto-imagem">';
              echo '<img src="' . htmlspecialchars($publicacao['imagem']) . '" alt="' . htmlspecialchars($publicacao['meta_titulo']) . '">';
              echo '</div>';
              echo '<div class="projeto-titulo">';
              echo '<h2>' . ($publicacao['meta_titulo']) . '</h2>';
              echo '</div>';
              echo '<div class="projeto-antevisao">';
              echo '<span>' . ($publicacao['meta_descricao']) . '</span>';
              echo '</div>';
              echo '</div>';
            }
          }
        }
        ?>
      </div>
    </div>
  </main>
  <?php include './assets/include/footer.php' ?>
</body>

</html>