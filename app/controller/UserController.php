<?php

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;
use App\Services\UserService;
use App\Collection\UserCollection;
use App\Model\User;

/**
 * Classe responsável por interagir entre a view
 * e os dados/lógica de usuários.
 */
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

	/**
	 * Filtro pré aplicado antes de redirecionar solicitação
	 * a um método do controller.
	 */
	public function run($route)
	{
		if ($this->services->auth->isSignedIn())
		{
			return true;
		}

		// Caso o usuário não esteja autenticado, o redirecionamos
		// à página de login.

		app()->module('router')->redirect('login');

		return false;
	}

	public function index()
	{
		if ($this->checkUserPermission('ver_usuários'))
		{
			$users = $this->collection->user->getAll();

			app()->module('view')->load('users/index', ['users' => $users]);
		}
	}

	/**
	 * Página de adicionar usuário
	 */
	public function create()
	{
		if ($this->checkUserPermission('criar_usuários'))
		{
			app()->module('view')->load('users/create');
		}
	}

	/**
	 * Salvar usuário no banco de dados
	 */
	public function store()
	{
		if (!$this->checkUserPermission('criar_usuários'))
		{
			return;
		}

		$args = filter_input_array(INPUT_POST, array(
			'email' => FILTER_DEFAULT,
			'password' => FILTER_DEFAULT,
			'permissions' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_REQUIRE_ARRAY
			)
		));

		$email = $args['email'];
		$password = $args['password'];
		$permissions = $args['permissions'];

		$user = new User();
		$user->setEmail($email);
		$user->setPermission($permissions);

		if ($this->services->user->create($user, $password))
		{
			app()->module('session')->flash('notify', $this->services->user->message);
			app()->module('router')->redirect('users.index');
		}
		else
		{
			app()->module('session')->flash('error', $this->services->user->message);
			app()->module('session')->flash('input_email', $email);
			app()->module('router')->redirect('users.create');
		}
	}

	/**
	 * Atualizar usuário no banco de dados
	 */
	public function update($id)
	{
		if (!$this->checkUserPermission('editar_usuários'))
		{
			return;
		}

		$user = new User();
		$user->setId($id);
		$user->setName(filter_input(INPUT_POST, 'name'));
		$user->setEmail(filter_input(INPUT_POST, 'email'));

		$result = $this->services->user->update($id);

		echo 'atualizar usuário';
	}

	/**
	 * Remover usuário do banco de dados
	 */
	public function destroy($id)
	{
		if (!$this->checkUserPermission('remover_usuários'))
		{
			return;
		}

		// TODO: implementar deletar usuário

		$result = $this->services->user->delete($id);

		echo 'deletar usuário';
	}

	/**
	 * Verifica se usuário atual possui permissão.
	 * 
	 * @param string $permission 		Permissão para verificar
	 * @return bool
	 */
	protected function checkUserPermission($permission)
	{
		$user = $this->services->user->findById($this->services->auth->getCurrentUserId());

		if (in_array($permission, $user->getPermission()))
		{
			return true;
		}

		// Caso o usuário não tenha a permissão, o redirecionamos
		// à página principal do painel, com mensagem de erro

		http_response_code(403);

		app()->module('session')->flash('error', 'Você não possui permissões suficientes para acessar esta página.');

		app()->module('router')->redirect('index');

		return false;
	}
}


?>