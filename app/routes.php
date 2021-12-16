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

$router->map('logout', '/sair', array('App\Controller\HomeController', 'logout'));


// Rotas da página de usuários
$router->map('users.index', '/usuarios', array('App\Controller\UserController', 'index'));

$router->map('users.create', '/usuarios/criar', array('App\Controller\UserController', 'create'));

$router->map('users.store', '/usuarios/criar', array('App\Controller\UserController', 'store'), 'POST');


// Rotas da página de atividades
$router->map('tasks.index', '/atividades', array('App\Controller\TaskController', 'index'));

$router->map('tasks.create', '/atividades/criar', array('App\Controller\TaskController', 'create'));

$router->map('tasks.store', '/atividades/criar', array('App\Controller\TaskController', 'store'), 'POST');

$router->map('tasks.edit', '/atividades/{id}/editar', array('App\Controller\TaskController', 'edit'));

$router->map('tasks.update', '/atividades/{id}/editar', array('App\Controller\TaskController', 'update'), 'POST');

$router->map('tasks.destroy', '/atividades/{id}/deletar', array('App\Controller\TaskController', 'destroy'));



// Rotas da página de categorias
$router->map('category.index', '/categorias', array('App\Controller\CategoryController', 'index'));

$router->map('category.store', '/categorias/criar', array('App\Controller\CategoryController', 'store'), 'POST');

$router->map('category.destroy', '/categorias/destroy', array('App\Controller\CategoryController', 'destroy'), 'POST');


unset($router);

?>