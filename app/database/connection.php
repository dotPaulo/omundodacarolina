<?php


//DB INFO
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'omundodacarolina.pt');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'omdcarol');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'pah4xzf8foztvyxsgk@');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'omdcarol_omundodacarolinav2');
}

try {
   
    $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    mysqli_set_charset($con,"utf8");

    if ($con === false) {
      
        throw new Exception("ERRO: Não foi possível conectar com a base de dados.");
    }
    
   
} catch (mysqli_sql_exception $e) {
    header('Location:' . __DIR__ . ' /../error_page.php');
    exit();
} catch (Exception $e) {
    header('Location:' . __DIR__ . ' /../error_page.php');
    exit();
}
?>