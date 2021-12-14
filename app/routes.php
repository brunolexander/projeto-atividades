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


// Rotas da página principal
$router->map('index', '/', array('App\Controller\HomeController', 'index'));


// Rotas da página de login
$router->map('login', '/entrar', array('App\Controller\HomeController', 'login'));

$router->map('login.submit', '/entrar', array('App\Controller\HomeController', 'loginSubmit'), 'POST');


unset($router);

?>