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
	 * @param string $path 				Caminho URI da solicitação
	 * @param string $request_method 	Método da solicitação, como GET ou POST
	 * 									Caso nulo, obtem da váriavel $_SERVER
	 * @return void
	 */
	public function handle($path, $request_method=null)
	{
		if (is_null($request_method))
		{
			$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
		}

		foreach ($this->routes as $name => $route)
		{
			if (strcasecmp($route['request_method'], $request_method) != 0)
			{
				continue;
			}

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
	 * @param string $name 				Nome da rota
	 * @param string $path 				Caminho URI da rota
	 * @param callable $callback 		Função da rota
	 * @param string $request_method	Método da solicitação, como GET ou POST
	 * @return void
	 */
	public function map($name, $path, $callback, $request_method='GET')
	{
		if (!is_callable($callback))
		{
			throw new \InvalidArgumentException('map() espera que 3° parâmetro seja um callback válido');
		}

		$this->routes[$name] = array(
			'path' => $path,
			'callback' => $callback,
			'request_method' => $request_method
		);
	}

	/**
	 * Formata argumentos e retorna URI da rota.
	 * 
	 * A função irá "lançar" uma exceção caso a rota não
	 * tenha sido definida ou se $data não conter os valores
	 * de todos os argumentos entre chaves { }.
	 * 
	 * @param string $name 		Nome da rota
	 * @param array $data		Valores dos argumentos
	 * @return string
	 */
	public function url($name, $data=[])
	{
		if (!array_key_exists($name, $this->routes))
		{
			throw new \LogicException("rota \"$name\" não foi definida");
		}

		$path = $this->routes[$name]['path'];

		if (preg_match_all('/\{[a-zA-Z_]{1}\w*\}/', $path, $matches) > 0)
		{
			$args = $matches[0];

			foreach ($args as $arg_name)
			{
				$key = trim($arg_name, "{}");

				if (!array_key_exists($key, $data))
				{
					throw new \LogicException("Parâmetro \"$key\" exigido por rota ausente");
				}
			}

			$path = str_replace($args, $data, $path);
		}
		
		return $path;
	}

	/**
	 * Redireciona cliente para a rota especificada.
	 * 
	 * @param string $name 		Nome da rota
	 * @param array $data		Valores dos argumentos
	 * @return void
	 */
	public function redirect($name, $data=[])
	{
		$path = $this->url($name, $data);
		header("Location: $path", true, 303);
	}
}

?>