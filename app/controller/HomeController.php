<?php

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;

class HomeController extends Controller
{
	protected $services;

	protected $auth;

	/**
	 * TODO: implementar injeção de dependência
	 * Ver: https://en.wikipedia.org/wiki/Dependency_injection
	 * 
	 * Por hora, instanciamos no próprio construtor.
	 */
	public function __construct(AuthService $auth=null)
	{
		$connection = app()->module('database');

		$this->services = (object) [
			'auth' => new AuthService()
		];
	}

	public function index()
	{
		if ($this->services->auth->isSignedIn())
		{
			app()->module('view')->load('index');
		}
		else
		{
			app()->module('router')->redirect('login');
		}
	}

	public function login()
	{
		if ($this->services->auth->isSignedIn())
		{
			app()->module('router')->redirect('index');
		}
		else
		{
			app()->module('view')->load('login');
		}
	}

	public function loginSubmit()
	{
		$email = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');

		app()->module('session')->set('email', $email);

		if ($this->services->auth->passwordSignIn($email, $password))
		{
			app()->module('router')->redirect('index');
		}
		else
		{
			app()->module('session')->flash('error', 'Usuário e/ou senha inválido(s)');
			app()->module('router')->redirect('login');
		}
	}

	public function logout()
	{
		$this->services->auth->signOut();
		app()->module('router')->redirect('login');
	}
}

?>