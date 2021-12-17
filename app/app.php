<?php

/**
 * Obtem a instância do nosso app.
 * Definimos aqui para poder utilizar a função em qualquer outro arquivo.
 * 
 * @return App\Modules\Application
 */
function app()
{
	return App\Modules\Application::instance();
}

/**
 * Carrega as configurações
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';


/**
 * Prepara o manipulador de erros e exceções
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'error_handler.php';


/**
 * Prepara os módulos necessários
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'modules.php';


/**
 * Defini as rotas para redirecionar
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

?>