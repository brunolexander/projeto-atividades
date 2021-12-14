<?php

/**
 * Configurar a aplicação
 */

$config = require __DIR__ . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'app.php';

app()->config($config);

date_default_timezone_set($config['timezone']);

mysqli_report(MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE)
{
    session_start();
}

unset($config);

?>