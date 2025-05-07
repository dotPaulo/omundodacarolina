<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'omundodacarolina');

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