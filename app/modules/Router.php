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
	 * Rotas para códigos de HTTP
	 * 
	 * @var array 
	 */
	protected $http_routes = array();

	/**
	 * Nome da rota atual.
	 * 
	 * @var string
	 */
	protected $current;

	/**
	 * Manipula solicitações e códigos de status HTTP.
	 * 
	 * @param string $path 				Caminho URI da solicitação
	 * @param string $request_method 	Método da solicitação, como GET ou POST
	 * 									Caso nulo, obtem da váriavel $_SERVER
	 * @return void
	 */
	public function handle($path, $request_method=null)
	{
		// Manipular a solicitação
		$this->handleRequest($path, $request_method);

		// Manipular qualquer código de status HTTP
		$this->handleHttpResponseCode();
	}

	/**
	 * Manipula uma solicitação URI. Caso inválido, retorna HTTP 404.
	 * 
	 * @param string $path 				Caminho URI da solicitação
	 * @param string $request_method 	Método da solicitação, como GET ou POST
	 * 									Caso nulo, obtem da váriavel $_SERVER
	 * @return bool
	 */
	public function handleRequest($path, $request_method)
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

			list($object, $method) = $route['callback'];

			if (!is_object($object))
			{
				$object = new $object;
			}

			if ($object->run($name) == false)
			{
				continue;
			}

			$this->current = $name;

			$args = array_column($matches, 0);

			// $args[] contém os valores dos parâmetros entre chaves { }
			// não precisamos do primeiro item pois $args[0] === $path
			unset($args[0]);

			$object->$method(...$args);
	
			return true;
		}

		http_response_code(404);

		return false;
	}

	/**
	 * Manipula um código de status HTTP
	 * 
	 * @param int $code 	Código do status
	 *						Para uma lista completa dos códigos, consulte:
	 * 						https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
	 * 
	 * @return bool
	 */
	public function handleHttpResponseCode($code=null)
	{
		if ($code === null)
		{
			$code = http_response_code();
		}

		if (!array_key_exists($code, $this->http_routes))
		{
			return false;
		}

		$object = $this->http_routes[$code]['controller'];
		$method = $this->http_routes[$code]['method'];

		if (!is_object($object))
		{
			$object = new $object();
		}

		$object->$method();

		return true;
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
		list($object, $method) = $callback;

		if (!method_exists($object, $method))
		{
			throw new \InvalidArgumentException('map() espera que 3° parâmetro seja uma classe ou objeto com um método válido');
		}

		$this->routes[$name] = array(
			'path' => $path,
			'callback' => $callback,
			'request_method' => $request_method
		);
	}

	/**
	 * Mapeia uma rota para manipular erros de HTTP, por exemplo, 404 Not Found.
	 * 
	 * @param int $code 	Código do status
	 * 						Para uma lista completa dos errors, consulte:
	 * 						https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
	 * 
	 * @param array $controller 	Classe e método do controller
	 * 
	 */
	public function mapHttpResponseCode($code, $controller)
	{
		list($object, $method) = $controller;

		if (!method_exists($object, $method))
		{
			throw new \InvalidArgumentException('map() espera que 2° parâmetro seja uma classe ou objeto com um método válido');
		}

		$this->http_routes[$code] = [
			'controller' => $object,
			'method' => $method
		];
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