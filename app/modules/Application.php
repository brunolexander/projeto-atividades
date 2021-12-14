<?php

namespace App\Modules;

/**
 * -------------------------
 * Classe principal
 * -------------------------
 * 
 * Esta classe é o "processador" central de todo o sistema.
 *
 * A classe foi desenvolvida com modelo singleton em mente,
 * portanto, apenas uma instância deve existir.
 */
class Application
{
	/**
	 * Todos os módulos da aplicação.
	 * 
	 * Para facilitar na comunicação entre diferentes classes,
	 * adicionamos instancias de cada uma na principal
	 * 
	 * @var array
	 */
	protected $modules = array();


	/**
	 * Todas as configuração da aplicação.
	 * 
	 * @var array
	 */
	protected $configs = array();


	/**
	 * Desabilita a construção de outras instâncias.
	 */
	protected function __construct() {}

	final private function __clone() {}

	final private function __wakeup() {}

	final private function __sleep() {}


	/**
	 * Retorna a instância da classe.
	 * 
	 * @return App\Modules\Application
	 */
	final public static function instance()
	{
		static $instance = false;

		if ($instance === false)
		{
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Obtem a instância de um módulo da aplicação.
	 * Módulos com mesmo nome são substituídos.
	 * 
	 * Para adicionar módulos, passe uma array no primeiro parâmetro.
	 * Exemplo: module(['mymodule' => new MyModule()])
	 * 
	 * @param array|string $module 	Nome do módulo ou array
	 * @return mixed
	 */
	public function module($module)
	{		
		if (!is_array($module))
		{
			if (array_key_exists($module, $this->modules))
			{
				return $this->modules[$module];
			}

			return null;
		}

		$this->modules = array_merge($this->modules, $module);
	}

	/**
	 * Obtem o valor de uma opção das configurações da aplicação.
	 * Opções com mesmo nome são substituídas.
	 * 
	 * Para definir uma opção, passe uma array no primeiro parâmetro.
	 * Exemplo: config(['key' => 'value'])
	 * 
	 * @param array|string $option 	Nome da opção ou array
	 * @return mixed
	 */
	public function config($option, $default=null)
	{
		if (!is_array($option))
		{
			if (array_key_exists($option, $this->configs))
			{
				return $this->configs[$option];
			}

			return $default;
		}

		$this->configs = array_merge($this->configs, $option);
	}

	/**
	 * Retorna o caminho para o diretório de arquivos da View. 
	 * 
	 * @return string
	 */
	public function getViewDir()
	{
		return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'view';
	}
}


?>