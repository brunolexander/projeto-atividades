<?php 

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;
use App\Services\TaskService;
use App\Services\UserService;
use App\Services\CategoryService;
use App\Collection\TaskCollection;
use App\Collection\CategoryCollection;

use App\Model\Task;

class TaskController
{
	protected $services = array();

	protected $collection = array();

	/**
	 * TODO: implementar injeção de dependência
	 * Ver: https://en.wikipedia.org/wiki/Dependency_injection
	 * 
	 * Por hora, instanciamos no próprio construtor.
	 */
	public function __construct()
	{
		$connection = app()->module('database');

		$category_collection = new CategoryCollection($connection);
		$category_service = new CategoryService($connection, $category_collection);
		$task_collection = new TaskCollection($connection);
		$task_service = new TaskService($connection, $category_service);
		$auth_service = new AuthService();
		$user_service = new UserService($connection);

		$this->services = (object) [
			'task' => $task_service,
			'auth' => $auth_service,
			'category' => $category_service,
			'user' => $user_service
		];

		$this->collection = (object) [
			'task' => $task_collection,
			'category' =>$category_collection
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
		if (!$this->checkUserPermission('ver_atividades'))
		{
			return;
		}

		$tasks = $this->collection->task->getAll();

		app()->module('view')->load('tasks/index', ['tasks' => $tasks]);
	}

	public function create()
	{
		if (!$this->checkUserPermission('criar_atividades'))
		{
			return;
		}

		$categories = $this->collection->category->getAll();

		app()->module('view')->load('tasks/create', ['categories' => $categories]);
	}

	public function store()
	{
		if (!$this->checkUserPermission('criar_atividades'))
		{
			return;
		}

		$title = filter_input(INPUT_POST, 'title');
		$content = filter_input(INPUT_POST, 'content');
		$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		$category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT, ['options' => [
			'default' => null,
			'min_range' => 1]
		]);

		if (!$category)
		{
			$category = null;
		}

		$task = new Task();
		$task->setTitle($title);
		$task->setContent($content);
		$task->setCategory($category);
		$task->setStatus($status);
		$task->setAuthor($this->services->auth->getCurrentUserId());

		if (!$this->services->task->create($task))
		{
			app()->module('session')->flash('error', $this->services->task->message);
			app()->module('session')->flash('title', $title);
			app()->module('session')->flash('content', $content);
			app()->module('session')->flash('status', $status);
			app()->module('session')->flash('category', $category);
			
			app()->module('router')->redirect('tasks.create');
		}
		else
		{
			app()->module('session')->flash('notify', $this->services->task->message);

			app()->module('router')->redirect('tasks.index');
		}
	}

	public function edit($id)
	{
		if (!$this->checkUserPermission('editar_atividades'))
		{
			return;
		}

		$task = $this->services->task->findById($id);

		if (!$task)
		{
			app()->module('router')->redirect('tasks.index');
		}
		else
		{
			$categories = $this->collection->category->getAll();
			app()->module('view')->load('tasks/edit', ['task' => $task, 'categories' => $categories]);
		}
	}

	public function update($id)
	{
		if (!$this->checkUserPermission('editar_atividades'))
		{
			return;
		}

		$title = filter_input(INPUT_POST, 'title');
		$content = filter_input(INPUT_POST, 'content');
		$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		$category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT, ['options' => [
			'default' => null,
			'min_range' => 1]
		]);

		$task = new Task();
		$task->setId($id);
		$task->setTitle($title);
		$task->setContent($content);
		$task->setStatus($status);
		$task->setCategory($category);

		if (!$this->services->task->update($task))
		{
			app()->module('session')->flash('error', $this->services->task->message);
			app()->module('session')->flash('title', $title);
			app()->module('session')->flash('content', $content);
			app()->module('session')->flash('category', $category);
			app()->module('session')->flash('status', $status);

			app()->module('router')->redirect('tasks.edit', ['id' => $id]);
		}
		else
		{
			app()->module('session')->flash('notify', $this->services->task->message);

			app()->module('router')->redirect('tasks.edit', ['id' => $id]);
		}
	}

	public function destroy($id)
	{
		if (!$this->checkUserPermission('remover_atividades'))
		{
			return;
		}

		if ($this->services->task->delete($id))
		{
			app()->module('session')->flash('notify', $this->services->task->message);

			app()->module('router')->redirect('tasks.index');
		}
		else
		{
			app()->module('session')->flash('error', $this->services->task->message);

			app()->module('router')->redirect('tasks.edit', ['id' => $id]);
		}
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