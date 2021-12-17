<?php 

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\UserService;
use App\Collection\CategoryCollection;
use App\Model\Category;

class CategoryController
{
	protected $services = array();

	protected $collection = array();

	/**
	 * TODO: implementar injeção de dependência
	 * Ver: https://en.wikipedia.org/wiki/Dependency_injection
	 * 
	 * Por hora, instanciamos no próprio construtor.
	 */
	public function __construct($category=CategoryService::class, $auth=AuthService::class, $collection=CategoryCollection::class)
	{
		$connection = app()->module('database');

		$this->collection = (object) [
			'category' => new $collection($connection)
		];

		$this->services = (object) [
			'category' => new $category($connection, $this->collection->category),
			'auth' => new $auth(),
			'user' => new UserService($connection)
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
		$categories = $this->collection->category->getAll();
		app()->module('view')->load('category/index', ['categories' => $categories]);
	}

	public function store()
	{
		$user = $this->services->user->findById($this->services->auth->getCurrentUserId());

		if (!in_array('adicionar_categorias', $user->getPermission()))
		{
			app()->module('session')->flash('error', 'Você não possui permissões suficientes para adicionar categorias.');

			app()->module('router')->redirect('category.index');

			return;
		}

		$name = filter_input(INPUT_POST, 'name');

		if (!$this->services->category->create($name))
		{
			app()->module('session')->flash('error', $this->services->category->message);
		}
		else
		{
			app()->module('session')->flash('notify', $this->services->category->message);
		}

		app()->module('router')->redirect('category.index');
	}

	public function destroy()
	{
		$user = $this->services->user->findById($this->services->auth->getCurrentUserId());

		if (!in_array('remover_categorias', $user->getPermission()))
		{
			app()->module('session')->flash('error', 'Você não possui permissões suficientes para remover categorias.');

			app()->module('router')->redirect('category.index');

			return;
		}

		$categories = filter_input_array(INPUT_POST, array(
			'category' => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags' => FILTER_REQUIRE_ARRAY
			)
		), false);

		if (!$this->services->category->destroy($categories['category']))
		{
			app()->module('session')->flash('error', $this->services->category->message);
		}
		else
		{
			app()->module('session')->flash('notify', $this->services->category->message);
		}

		app()->module('router')->redirect('category.index');
	}
}


?>