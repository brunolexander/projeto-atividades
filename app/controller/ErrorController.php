<?php

namespace App\Controller;

use App\Modules\Controller;

/**
 * Classe para manipular erros de HTTP
 */
class ErrorController extends Controller
{
	/**
	 * HTTP 404 Not Found
	 */
	public function notFound()
	{
		app()->module('view')->load('errors/404');
	}
}


?>