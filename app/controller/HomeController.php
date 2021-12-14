<?php

namespace App\Controller;
use App\Services\AuthService as AuthService;
use App\Collection\UserCollection as UserCollection;

class HomeController
{
	protected $users;

	protected $auth;

	/**
	 * TODO: implementar injeção de dependência
	 * Ver: https://en.wikipedia.org/wiki/Dependency_injection
	 * 
	 * Por hora, instanciamos no próprio construtor.
	 */
	public function __construct(UserCollection $users=null, AuthService $auth=null)
	{
		$this->users = new UserCollection;
		$this->auth = new AuthService;
	}

	public function index()
	{
		if (!$this->auth->isSignedIn())
		{
			app()->module('router')->redirect('login');

			return;
		}
		
		app()->module('view')->load('index');
	}

	public function login()
	{
		if ($this->auth->isSignedIn())
		{
			app()->module('router')->redirect('index');

			return;
		}

		$data = array('error' => '', 'email' => '');

		if (isset($_SESSION['error']))
		{
			$data['error'] = htmlspecialchars($_SESSION['error']);
			unset($_SESSION['error']);
		}

		if (isset($_SESSION['email']))
		{
			$data['email'] = filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL);
		}

		app()->module('view')->load('login', $data);
	}

	public function loginSubmit()
	{
		$email = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');

		$user = $this->users->findByLogin($email, $password);

		if ($user)
		{
			$this->auth->signIn($user);
			app()->module('router')->redirect('index');
		}

		$_SESSION['error'] = 'Usuário e/ou senha inválido(s)';
		$_SESSION['email'] = $email;
	
		app()->module('router')->redirect('login');
	}

	public function logoutSubmit()
	{
		$this->auth->signOut();
		app()->module('router')->redirect('login');
	}
}

?>