<?php
$headerPath = './assets/include/header.php';
$scrollbarPath = './assets/include/scrollbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/CSS/sobre.css">
    <title>Sobre | omundodacarolina</title>
    <link rel="shortcut icon" type="image/png" href="./assets/Imagens/favicon.ico">
</head>

<body>
    <?php include $headerPath; ?>
    <?php include $scrollbarPath; ?>
    <main>
        <section class="sobre-container">
            <div class="box">
                <h1>O Mundo da Carolina é uma Associação <span>sem fins lucrativos.</span></h1>
            </div>
            <div class="wave">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#ff69b4;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:white;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path
                        d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                        fill="url(#grad1)"></path>
                </svg>
            </div>
        </section>
        <section>
            <div class="box_missao">
                <h2 class="text-heading">A nossa Missão</h2>
                <p>Apoiar e contribuir para o desenvolvimento de crianças e jovens, com especial enfoque nos que sofrem
                    de doenças crónicas e/ou em condição sócio-económica desfavorável. Desenvolvimento de atividades que
                    promovam a literacia digital, a participação cívica e o espírito solidário.</p>
            </div>
            <div class="box_missao">
                <p>Mais do que uma homenagem à memória da Carolina Pombo, este projeto, inspirado na sua coragem,
                    determinação e felicidade contagiante, prentende levar milhões de sorrisos e esperança à s crianças.
                </p>
            </div>
            <div class="box_missao">
                <p>Com base no lema 'Ajude-nos a ajudar', Ο Mundo da Carolina tem privilegiado o estabelecimento de
                    parcerias com outras instituições solidárias, por forma a proporcionar uma valorização humana a
                    crianças e jovens através de diferentes vivências culturais, musicais, cívicas e lúdicas.</p>
            </div>
            <img class="borboletas" src="assets/Imagens/borboletas.png" alt="Borboletas">
        </section>
    </main>
    <script src="./assets/JS/sobre.js"></script>
    <?php
    include './assets/include/footer.php'
        ?>
</body>

</html>