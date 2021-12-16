<?php

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;
use App\Services\UserService;
use App\Collection\UserCollection;
use App\Model\User;

class UserController extends Controller
{
	protected $services = array();

	protected $collection = array();

	/**
	 * TODO: implementar injeção de dependência
	 * Ver: https://en.wikipedia.org/wiki/Dependency_injection
	 * 
	 * Por hora, instanciamos no próprio construtor.
	 */
	public function __construct($user=UserService::class, $auth=AuthService::class, $collection=UserCollection::class)
	{
		$connection = app()->module('database');

		$this->services = (object) [
			'user' => new $user($connection),
			'auth' => new $auth()
		];

		$this->collection = (object) [
			'user' => new $collection($connection)
		];
	}

	public function run($route)
	{
		if ($this->services->auth->isSignedIn())
		{
			return true;
		}

		app()->module('router')->redirect('login');

		return false;
	}

	public function index()
	{
		$users = $this->collection->user->getAll();
		app()->module('view')->load('users/index', ['users' => $users]);
	}

	public function create()
	{
		app()->module('view')->load('users/create');
	}

	public function store()
	{
		$email = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');

		$user = new User();
		$user->setEmail($email);

		if ($this->services->user->create($user, $password))
		{
			app()->module('session')->flash('notify', $this->services->user->message);
			app()->module('router')->redirect('users.index');
		}
		else
		{
			app()->module('session')->flash('error', $this->services->user->message);
			app()->module('router')->redirect('users.create');
		}
	}

	public function update($id)
	{
		$user = new User();
		$user->setId($id);
		$user->setName(filter_input(INPUT_POST, 'name'));
		$user->setEmail(filter_input(INPUT_POST, 'email'));

		$result = $this->services->user->update($id);

		echo 'atualizar usuário';
	}

	public function destroy($id)
	{
		$result = $this->services->user->delete($id);

		echo 'deletar usuário';
	}
}


?>