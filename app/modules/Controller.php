<?php

namespace App\Modules;

/**
 * Classe base para controllers.
 */
abstract class Controller
{
	/**
	 * Este método pode ser utilizado como um filtro.
	 * Por exemplo, permitir accesso apenas para usuários
	 * que estejam autenticados.
	 * 
	 * Deve retornar true para permitir a execução do controller
	 * ou false para negar.
	 * 
	 * @param string $route 	Nome da rota
	 * @return bool
	 */
	public function run($route)
	{
		return true;
	}
}

?>