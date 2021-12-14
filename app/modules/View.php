<?php

namespace App\Modules;

/**
 * Classe responsável por renderizar a resposta no navegador,
 * ou aquilo que o usuário final irá ver.
 */
class View
{
	/**
	 * Carrega um arquivo da view.
	 *
	 * O nome do arquivo não deve conter o diretório, a menos
	 * que a pasta seja uma subpasta da view.
	 * 
	 * Caso a extensão não seja definida, a função tentará
	 * encontrar qualquer arquivo .php
	 * 
	 * Exemplo caso diretório do arquivo seja app/view/subpasta/arquivo.php
	 * load('subpasta/arquivo')
	 * 
	 * Todas as keys na array $data são convertidas em váriaveis.
	 * 
	 * @param string $path 		Nome do arquivo, sem caminho completo
	 * @param array $data		Valores para serem passados
	 */
	public function load($path, $data=[])
	{
		$extension = pathinfo($path, PATHINFO_EXTENSION);

		if (empty($extension))
		{
			$path .= '.php';
		}

		$path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
		$path = app()->getViewDir() . DIRECTORY_SEPARATOR . $path;

		extract($data);

		include $path;
	}
}

?>