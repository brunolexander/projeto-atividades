<?php

namespace App\Modules;

/**
 * Classe responsável por redirecionar a solicitação do cliente.
 */
class Router
{
	/**
	 * Todas as rotas para redirecionar.
	 * 
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Manipula uma solicitação.
	 * 
	 * @param string $path 	Caminho URI da solicitação
	 * @return void
	 */
	public function handle($path)
	{
		foreach ($this->routes as $name => $route)
		{
			$pattern = preg_replace('/\\\\{[a-zA-Z_]{1}\w*\\\\}/', '([^.\/]+)', preg_quote($route['path'], '/'));

			if (preg_match_all("/^$pattern$/i", $path, $matches) == 0)
			{
				continue;
			}

			$args = array_column($matches, 0);

			// $args[] contém os valores dos parâmetros entre chaves { }
			// não precisamos do primeiro item pois $args[0] === $path
			unset($args[0]);

			if (is_array($route['callback']))
			{
				list($object, $method) = $route['callback'];

				$reflection = new \ReflectionMethod($object, $method);

				if ($reflection->isStatic())
				{
					$object::$method(...$args);
				}
				else
				{
					if (!is_object($object))
					{
						$object = new $object;
					}

					$object->$method(...$args);
				}
			}
			else
			{
				$route['callback'](...$args);
			}

			break;
		}
	}

	/**
	 * Mapeia uma rota.
	 * 
	 * O nome da rota deve ser único. Caso já exista, será substituída.
	 * Valores entre chaves { } são cosiderados parâmetros e são passados ao callback.
	 * 
	 * Exemplo:
	 *
	 * map('usuarios', '/usuarios/{id}', function($id) {
	 * 		echo "A id do usuário é $id.";
	 * });
	 * 
	 * @param string $name 			Nome da rota
	 * @param string $path 			Caminho URI da rota
	 * @param callable $callback 	Função da rota
	 * 
	 * @return void
	 */
	public function map($name, $path, $callback)
	{
		if (!is_callable($callback))
		{
			throw new InvalidArgumentException('map() espera que 2° parâmetro seja um callback válido');
		}

		$this->routes[$name] = array('path' => $path, 'callback' => $callback);
	}
}


?>