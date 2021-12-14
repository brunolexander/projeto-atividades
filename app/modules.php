<?php

/**
 * --------------------------------------
 * Configurar os módulos da aplicação.
 * --------------------------------------
 *
 * Para facilitar na comunicação entre classes distintas,
 * adicionamos instâncias de tudo que for requerido na classe
 * principal.
 * 
 * O sistema foi pensado visando permitir o desenvolvedor de conectar 
 * e desconectar diferentes módulos sem afetar a funcionalidade da aplicação.
 * Entre tando, isto ainda não é totalmente funcional mas permanece
 * como uma ideia para melhorias no futuro.
 * 
 * Observação: O nome de cada módulo deve ser único, caso contrário, os
 * que forem adicionados irão substituir os mais antigos.
 */

$app = app();


// Router
$app->module(['router' => new App\Modules\Router()]);


// View
$app->module(['view' => new App\Modules\View()]);


// Banco de dados
$config = $app->config('database');

$database = new \mysqli(
	$config['hostname'],
	$config['username'],
	$config['password'],
	$config['database'],
	$config['port'],
	$config['socket']	
);

$app->module(['database' => $database]);

register_shutdown_function(function() use($database) {
	$database->close();
});


unset($config);
unset($database);
unset($app);

?>