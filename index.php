<?php
$headerPath = './assets/include/header.php';
$scrollbarPath = './assets/include/scrollbar.php';
$showLogoutMessage = isset($_GET['logout']) && $_GET['logout'] === 'success';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./assets/CSS/index.css">
  <title>omundodacarolina</title>
  <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
</head>

<body>

  <main>
    <!------------------------------- hero ------------------------------------>
      <section>
        <div class="hero-slider">
          <div class="landing-hero">
            <div class="hero-text">
              <span>Corações que sonham. Mentes que inovam. <br> Mãos que constroem</span>
              <div class="hero-btn">
                <button id="ReadMore" class="hero-btn1">Ler mais</button>
              </div>
            </div>
          </div>
          <div class="hero-list">
            <div class="hero-item">
              <img src="assets/Imagens/ftindex3.jpg" alt="">
            </div>
            <div class="hero-item">
              <img src="assets/Imagens/ftindex2.jpg" alt="">
            </div>
            <div class="hero-item">
              <img src="assets/Imagens/ftindex1.jpg" alt="">
            </div>
          </div>
          <div class="hero-buttons">
            <button id="prev"></button>
            <button id="next"></button>
          </div>
          <ul class="hero-dots">
            <li class="active"></li>
            <li></li>
            <li></li>
          </ul>
          <div id="logout-message">Foi desconectado.</div>
        </div>
      </section>

      <!------------------------------- Hyperlink ------------------------------------>
      <section class="hyperlink-container">

      <a href="projetos.php" class="hyperlink-link">
        <div class="hyperlink-box">
          <div class="hyperlink-icone">
            <i class="fa-solid fa-list-check fa-2xl"></i>
          </div>
          <div class="hyperlink-text">
            <h3>Projetos</h3>
            <span>Descubra os nossos projetos</span>
          </div>
        </div>
      </a>

      <a href="sobre.php" class="hyperlink-link">
        <div class="hyperlink-box">
          <div class="hyperlink-icone">
            <i class="fa-solid fa-circle-info fa-2xl"></i>
          </div>
          <div class="hyperlink-text">
            <h3>Sobre</h3>
            <span>Saiba mais sobre a associação</span>
          </div>
        </div>
      </a>

      <div id="contactClickArea" class="hyperlink-link">
        <div class="hyperlink-box">
          <div class="hyperlink-icone">
            <i class="fa-solid fa-comment fa-flip-horizontal fa-2xl"></i>
          </div>
          <div class="hyperlink-text">
            <h3>Contactos</h3>
            <span>Entre em contacto para mais informações</span>
          </div>
        </div>
      </div>

  </section>

    <!------------------------------- Projetos ------------------------------------>

    <section>
      <div id="projetos" class="projeto-container">
        <div class="projeto-header">
          <h1>Últimos Projetos </h1>
          <button onclick="location.href='projetos.php'">Ver todos</button>
        </div>
        <div class="projeto-area">
          <?php
          
          require_once ('./app/controllers/users.php');
          $publicacoes = selectAll('publicacoes');
        

          // Sort by id
          usort($publicacoes, function ($a, $b) {
            return $b['id'] <=> $a['id'];
          });

          // Filter and limit to 4 projetos
          $filteredProjetos = array_filter($publicacoes, function ($item) {
            return $item['public_type'] == 'projetos';
          });
          $limitedProjetos = array_slice($filteredProjetos, 0, 4);

          foreach ($limitedProjetos as $publicacao) {
            echo '<div onclick="location.href=\'publicacoes.php?id=' . $publicacao['id'] . '\'" class="projeto-box">';
            echo '<div class="projeto-imagem">';
            echo '<img src="' . htmlspecialchars($publicacao['imagem']) . '" alt="' . htmlspecialchars($publicacao['meta_titulo']) . '">';
            echo '</div>';
            echo '<div class="projeto-titulo">';
            echo '<h2>' . htmlspecialchars($publicacao['meta_titulo']) . '</h2>';
            echo '</div>';
            echo '<div class="projeto-antevisao">';
            echo '<span>' . htmlspecialchars($publicacao['meta_descricao']) . '</span>';
            echo '</div>';
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </section>

    <!------------------------------- Notícias ------------------------------------>

    <section>
      <div class="noticias-container">
        <div class="noticias-header">
          <h1>Últimas Notícias </h1>
          <button onclick="location.href='noticia.php'">Ver todas</button>
        </div>
        <div class="noticias-area">
          <?php
          require_once ('app/controllers/users.php');
          $publicacoes = selectAll('publicacoes');

          // Sort by id
          usort($publicacoes, function ($a, $b) {
            return $b['id'] <=> $a['id'];
          });

          // Filter and limit to 4 noticias
          $filteredNoticias = array_filter($publicacoes, function ($item) {
            return $item['public_type'] == 'noticias';
          });
          $limitedNoticias = array_slice($filteredNoticias, 0, 4);

          foreach ($limitedNoticias as $publicacao) {
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
          ?>
        </div>
      </div>
    </section>
  </main>
  <script>
    var showLogoutMessage = <?php echo json_encode($showLogoutMessage); ?>;
  </script>
  <script src="./assets/JS/logout.js"></script>
  <script src="./assets/JS/buttonsIndex.js"></script>
  <script src="./assets/JS/slider.js"></script>
  <?php
  include './assets/include/footer.php'
    ?>
</body>

</html>