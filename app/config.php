<?php

/**
 * Configurar a aplicação
 */

$config = require __DIR__ . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'app.php';

app()->config($config);

unset($config);

?>