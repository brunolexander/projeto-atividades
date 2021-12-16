<?php 

namespace App\Controller;

use App\Modules\Controller;
use App\Services\AuthService;
use App\Services\TaskService;
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

		$this->services = (object) [
			'task' => $task_service,
			'auth' => $auth_service,
			'category' => $category_service
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
		$tasks = $this->collection->task->getAll();

		app()->module('view')->load('tasks/index', ['tasks' => $tasks]);
	}

	public function create()
	{
		$categories = $this->collection->category->getAll();

		app()->module('view')->load('tasks/create', ['categories' => $categories]);
	}

	public function store()
	{
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
}


?>