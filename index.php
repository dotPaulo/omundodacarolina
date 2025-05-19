<?php
$headerPath = './assets/include/header.php';
$scrollbarPath = './assets/include/scrollbar.php';
$showLogoutMessage = isset($_GET['logout']) && $_GET['logout'] === 'success';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>omundodacarolina</title>
  <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- CSS -->
  <link rel="stylesheet" href="./assets/CSS/index.css">
</head>

<body>
  <main>

    <!-- ===================== HERO ===================== -->
    <section class="hero-slider">
      <div class="landing-hero">
        <div class="hero-text">
          <span>Corações que sonham. Mentes que inovam.<br>Mãos que constroem</span>
          <div class="hero-btn">
            <button id="ReadMore" class="hero-btn1">Ler mais</button>
          </div>
        </div>
      </div>

      <div class="hero-list">
        <div class="hero-item"><img src="assets/Imagens/ftindex3.jpg" alt="Imagem 1"></div>
        <div class="hero-item"><img src="assets/Imagens/ftindex2.jpg" alt="Imagem 2"></div>
        <div class="hero-item"><img src="assets/Imagens/ftindex1.jpg" alt="Imagem 3"></div>
      </div>

      <div class="hero-buttons">
        <button id="prev"><i class="fa-solid fa-chevron-left"></i></button>
        <button id="next"><i class="fa-solid fa-chevron-right"></i></button>
      </div>

      <ul class="hero-dots">
        <li class="active"></li>
        <li></li>
        <li></li>
      </ul>

      <?php if ($showLogoutMessage): ?>
        <div id="logout-message" class="fade-message">Foi desconectado com sucesso.</div>
      <?php endif; ?>
    </section>

    <!-- ===================== HYPERLINKS ===================== -->
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

    <!-- ===================== PROJETOS ===================== -->
    <section id="projetos" class="projeto-container">
      <div class="projeto-header">
        <h1>Últimos Projetos</h1>
        <button onclick="location.href='projetos.php'">Ver todos</button>
      </div>
      <div class="projeto-area">
        <?php
        require_once './app/controllers/users.php';
        $publicacoes = selectAll('publicacoes');

        usort($publicacoes, fn($a, $b) => $b['id'] <=> $a['id']);

        $projetos = array_slice(array_filter($publicacoes, fn($item) => $item['public_type'] === 'projetos'), 0, 6);

        foreach ($projetos as $p) {
          echo '<div class="projeto-box" onclick="location.href=\'publicacoes.php?id=' . $p['id'] . '\'">';
          echo '<div class="projeto-imagem"><img src="' . htmlspecialchars($p['imagem']) . '" alt="' . htmlspecialchars($p['meta_titulo']) . '"></div>';
          echo '<div class="projeto-titulo"><h2>' . htmlspecialchars($p['meta_titulo']) . '</h2></div>';
          echo '<div class="projeto-antevisao"><span>' . htmlspecialchars($p['meta_descricao']) . '</span></div>';
          echo '</div>';
        }
        ?>
      </div>
    </section>

    <!-- ===================== NOTÍCIAS ===================== -->
    <section class="noticias-container">
      <div class="noticias-header">
        <h1>Últimas Notícias</h1>
        <button onclick="location.href='noticia.php'">Ver todas</button>
      </div>
      <div class="noticias-area">
        <?php
        require_once 'app/controllers/users.php';
        $publicacoes = selectAll('publicacoes');

        usort($publicacoes, fn($a, $b) => $b['id'] <=> $a['id']);

        $noticias = array_slice(array_filter($publicacoes, fn($item) => $item['public_type'] === 'noticias'), 0, 4);

        foreach ($noticias as $n) {
          echo '<div class="noticias-box" onclick="location.href=\'publicacoes.php?id=' . $n['id'] . '\'">';
          echo '<div class="noticias-imagem"><img src="' . htmlspecialchars($n['imagem']) . '" alt="' . htmlspecialchars($n['meta_titulo']) . '"></div>';
          echo '<div class="noticias-titulo"><h2>' . htmlspecialchars($n['meta_titulo']) . '</h2></div>';
          echo '<div class="noticias-antevisao"><span>' . htmlspecialchars($n['meta_descricao']) . '</span></div>';
          echo '</div>';
        }
        ?>
      </div>
    </section>

  </main>

  <!-- ===================== SCRIPTS ===================== -->
  <script>
    const showLogoutMessage = <?php echo json_encode($showLogoutMessage); ?>;
    if (showLogoutMessage) {
      const msg = document.getElementById("logout-message");
      msg.style.display = "block";
      setTimeout(() => msg.style.opacity = 0, 3000);
    }
  </script>
  <script src="./assets/JS/logout.js"></script>
  <script src="./assets/JS/buttonsIndex.js"></script>
  <script src="./assets/JS/slider.js"></script>

  <?php include './assets/include/footer.php'; ?>
</body>

</html>
