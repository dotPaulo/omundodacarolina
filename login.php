<?php

$baseDir = __DIR__ . '/assets/include/';
$headerPath = $baseDir . 'header.php';
$scrollbarPath = $baseDir . 'scrollbar.php';
include (__DIR__ . '/app/controllers/users.php');
require_once __DIR__ . '/app/helpers/JwtHelper.php';

if (isset($_COOKIE['jwt'])) {
  $jwt = $_COOKIE['jwt'];

  $decoded = validateToken($jwt, $key);

  if ($decoded) {
    $username = htmlspecialchars($decoded->username, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($decoded->email, ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($decoded->role, ENT_QUOTES, 'UTF-8');

    if ($role !== 'admin') {
      header("Location: index.php");
      exit();
    } else {
      header("Location: ./admin/dashboard.php");
    }
  } else {
    header("Location: login.php");
    exit();
  }
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/CSS/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <title>Login | omundodacarolina</title>
  <link rel='shortcut icon' type='image/png' href='./assets/Imagens/favicon.ico'>
</head>

<body class="login-page">
  <main>
    <?php include $headerPath; ?>
    <?php include $scrollbarPath; ?>
    <section>
      <div class="hero-slider">
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
        <div class="login-section">
          <div class="login-form-container">
            <h2>Iniciar Sessão</h2>
            <p class="login-error"> <?php echo $login_err; ?></p>
            <form action="login.php" method="POST">
              <div id="login-form">
                <div class="container">
                </div>
                <div class="input-group">
                  <input type="text" id="email" name="email" class="input_field" required>
                  <label for="email" class="label">Email</label>
                  <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="input-group">
                  <div class="password-container">
                    <input type="password" id="pass" name="password" class="input_field" required>
                    <label for="pass" class="label">Palavra-passe</label>
                    <i class="fa-regular fa-eye" id="togglePassword"></i>
                  </div>
                </div>
              </div>
              <input type="submit" class="submit-button" name="login-btn" value="Login">
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script src="./assets/JS/slider.js"></script>
  <script src="./assets/JS/hidepsw.js"></script>
  <script src="./assets/JS/login.js"></script>
  <?php include $baseDir . 'footer.php'; ?>
</body>

</html>