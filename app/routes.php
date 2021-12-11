<?php

/**
 * ----------------------------------------
 * Configurar as rotas da aplicação
 * ----------------------------------------
 * 
 * As rotas são utilizadas para redirecionar o cliente
 * quando um determinado URL é acessado.
 */

$router = app()->module('router');

$router->map('home', '/', function() {
	echo 'index<br>';
});

unset($router);


?>