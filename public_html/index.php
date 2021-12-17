<?php


/**
 * -------------------------------------
 * Prepara o autoloader
 * -------------------------------------
 * 
 * Dependendo da escala do projeto, ter de incluir cada arquivo
 * das classes que serão utilizadas pode se tornar um problema.
 * Para evitar que isso ocorra, vamos utilizar o autoloader
 * para carregar as depêndencias necessárias automaticamente.
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'autoload.php';


/**
 * -------------------------------------
 * Inicializa nossa aplicação
 * -------------------------------------
 * 
 * Antes de podermos utilizar nosso app,
 * precisamos configura-lo e inicia-lo
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'app.php';


/**
 * -------------------------------------
 * Hora de agir!
 * -------------------------------------
 * 
 * Assim que tudo estiver preparado, podemos obter a solicitação
 * e enviar uma resposta de volta ao cliente.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
app()->module('router')->handle($uri);

?>