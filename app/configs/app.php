<?php

/**
 * ----------------------------------------
 * Arquivo de configuração app.php
 * ----------------------------------------
 */

return array(

	# Banco de dados
	'database' => [
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'projeto-atividades',
		'port' => 3306,
		'socket' => null
	],

	# Fuso horário 
	'timezone' => 'America/Sao_Paulo',

	# Localidade para setlocale
	'locale' => ['pt_BR', 'pt_BR.utf8', 'pt_BR.iso-8859-1', 'portuguese'],

	# Modo de depuração
	'debug' => true
);

?>