<?php


//DB INFO
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'omundodacarolina');
}


try {
   
    $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

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