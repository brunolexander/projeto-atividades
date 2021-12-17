<?php

namespace App\Services;

use App\Model\Task;
use App\Model\User;
use App\Model\Category;
use App\Services\CategoryService;

/**
 * Classe responsável por interagir com o controller
 * e os dados/lógica de atividades.
 */
class TaskService
{
	/**
	 * Mensagem a ser reportada ao cliente
	 * 
	 * @var string
	 */
	public $message;

	/**
	 * Conexão com banco de dados
	 * 
	 * @var mysqli
	 */
	protected $connection;

	/**
	 * "Serviços" utilizados por essa classe
	 * 
	 * @var array<Service>
	 */
	protected $services;

	/**
	 * Construtor
	 * 
	 * @param mysqli $connection 	Conexão com banco de dados
	 */
	public function __construct(\mysqli $connection, CategoryService $category_service)
	{
		$this->connection = $connection;
		$this->services = (object) ['category' => $category_service];
	}

	/**
	 * Insere uma atividade no banco de dados.
	 * 
	 * @param $task 	Atividade para inserir
	 * @return bool
	 */
	public function create(Task $task)
	{
		if (!$this->canCreateUrgentTask($task))
		{
			// Manutenções urgentes não podem ser criadas (nem via edição) após as 13:00 das sextas-feiras.

			return false;
		}

		if (!$this->canFinishTask($task))
		{
			// Atividades de atendimento e manutenção urgentes não podem ser finalizadas se a descrição estiver preenchida com menos de 50 caracteres;

			return false;
		}
		
		$stmt = $this->connection->prepare('INSERT INTO `atividades` (`titulo`, `descricao`, `criado_em`, `categoria`, `autor`, `finalizada`) VALUES(?, ?, ?, ?, ?, ?)');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		// TODO: verificar se todos os dados da atividade são válidos

		$stmt->bind_param('ssiiii', $titulo, $descricao, $criado_em, $categoria, $autor, $status);

		$titulo = $task->getTitle();
		$descricao = $task->getContent();
		$criado_em = time();
		$autor = $task->getAuthor();
		$status = $task->getStatus();
		$categoria = $task->getCategory();


		if ($categoria instanceof Category)
		{
			$categoria = $categoria->getId();
		}

		if ($autor instanceof User)
		{
			$autor = $autor->getId();
		}

		$success = false;

		if ($stmt->execute())
		{
			$success = true;
			$this->message = 'Atividade criada com sucesso!';
		}
		else
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';
		}

		$stmt->close();

		return $success;
	}

	/**
	 * Atualiza uma atividade.
	 * 
	 * @param App\Model\Task $task 		Atividade para atualizar
	 * @return bool
	 */
	public function update(Task $task)
	{
		if (!$this->canCreateUrgentTask($task))
		{
			// Manutenções urgentes não podem ser criadas (nem via edição) após as 13:00 das sextas-feiras.

			return false;
		}

		if (!$this->canFinishTask($task))
		{
			// Atividades de atendimento e manutenção urgentes não podem ser finalizadas se a descrição estiver preenchida com menos de 50 caracteres;

			return false;
		}

		$stmt = $this->connection->prepare('UPDATE `atividades` SET `titulo`=?,`descricao`=?,`atualizado_em`=?,`categoria`=?,`finalizada`=? WHERE `id`=?');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		// TODO: verificar se todos os dados da atividade são válidos

		$stmt->bind_param('ssiiii', $titulo, $descricao, $atualizado_em, $categoria, $status, $id);

		$titulo = $task->getTitle();
		$descricao = $task->getContent();
		$atualizado_em = time();
		$categoria = $task->getCategory();
		$status = $task->getStatus();
		$id = $task->getId();

		if ($categoria instanceof Category)
		{
			$categoria = $categoria->getId();
		}

		$success = false;

		if ($stmt->execute() && $this->connection->affected_rows > 0)
		{
			$success = true;
			$this->message = 'Atividade atualizada com sucesso!';
		}
		else
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';
		}

		$stmt->close();

		return $success;
	}

	/**
	 * Remove uma atividade por id.
	 * 
	 * @param int $id 	ID da atividade
	 * @return bool
	 */
	public function delete($id)
	{
		// Atividades de manutenção urgente não podem ser removidas, apenas finalizadas;

		if (!$this->canDeleteUrgentTask($id))
		{
			return false;
		}

		$stmt = $this->connection->prepare('DELETE FROM `atividades` WHERE `id`=?');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$success = false;

		$stmt->bind_param('i', $id);

		if ($stmt->execute() && $this->connection->affected_rows > 0)
		{
			$success = true;
			$this->message = 'Atividade removida com sucesso!';
		}
		else
		{
			$this->message = 'Não foi possível deletar a atividade.';
		}

		$stmt->close();

		return $success;
	}

	/**
	 * Busca uma atividade por id.
	 * 
	 * @param int $id 	ID da atividade
	 * @return App\Model\Task ou null
	 */
	function findById($id)
	{
		$stmt = $this->connection->prepare('SELECT atividade.*, categoria.nome AS categoria_nome, COALESCE(usuario.nome, usuario.email) AS usuario_nome FROM atividades atividade LEFT JOIN categorias categoria ON atividade.categoria=categoria.id LEFT JOIN usuarios usuario ON atividade.autor=usuario.id WHERE atividade.id=?');

		if (!$stmt)
		{
			return null;
		}

		$task = null;

		$stmt->bind_param('i', $id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();

				$task = new Task();
				$task->setId($row['id']);
				$task->setTitle($row['titulo']);
				$task->setContent($row['descricao']);
				$task->setCreatedAt($row['criado_em']);
				$task->setUpdatedAt($row['atualizado_em']);
				$task->setStatus($row['finalizada']);

				$user = new User();
				$user->setId($row['autor']);
				$user->setName($row['usuario_nome']);
				$task->setAuthor($user);
				
				if ($row['categoria'] != null)
				{
					$category = new Category();
					$category->setId($row['categoria']);
					$category->setName($row['categoria_nome']);
					$task->setCategory($category);
				}
			}

			$result->free();
		}

		return $task;
	}

	/**
	 * Verifica se uma atividade urgente pode ser criada.
	 * 
	 * @param App\Model\Task $task 		Atividade a ser verificada
	 * @return bool
	 */
	public function canCreateUrgentTask($task)
	{
		$this->message = 'Atividades de urgência podem ser criadas somente até as 13:00 das sextas-feiras.';

		$category = $task->getCategory();

		if (!($category instanceof Category))
		{
			$category = $this->services->category->findById($category);

			// Assume que tipo da atividade foi definida como "Sem categoria"
			if (!$category)
			{
				return true;
			}
		}

		$is_friday = (bool) (date('w') == 5 && date('G') >= 13);
		
		// Devido a possibilidade de remover e adicionar categorias,
		// comparar somente a ID não irá funcionar como esperamos. Precisamos 
		// encontrar uma outra maneira de determinar se a categoria é urgente ou não.
		// Provavelmente, isso iria requerer um sistema onde o usuario define
		// quais são as categorias consideradas urgentes. 
		// Por hora, apenas verificamos se o nome contém a palavra "urgente".

		return (bool) (!$is_friday || stripos($category->getName(), 'urgente') === false);
	}

	/**
	 * Verifica se atividade urgente pode ser removida.
	 * 
	 * @param int $task_id 		ID da atividade
	 * @return bool
	 */
	public function canDeleteUrgentTask($task_id)
	{
		$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde';

		$stmt = $this->connection->prepare('SELECT categoria.nome FROM `categorias` categoria INNER JOIN `atividades` atividade ON categoria.id=atividade.categoria WHERE atividade.id=?;');

		if (!$stmt)
		{
			return false;
		}

		$can_delete = true;

		$stmt->bind_param('i', $task_id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				list($category) = $result->fetch_row();

				// Devido a possibilidade de remover e adicionar categorias,
				// comparar somente a ID não irá funcionar como esperamos. Precisamos 
				// encontrar uma outra maneira de determinar se a categoria é urgente ou não.
				// Provavelmente, isso iria requerer um sistema onde o usuario define
				// quais são as categorias consideradas urgentes. 
				// Por hora, apenas verificamos se o nome contém a palavra "urgente".

				if (stripos($category, 'urgente') !== false)
				{
					$can_delete = false;

					$this->message = 'Atividades urgentes não podem ser excluídas, apenas finalizadas.';
				}
			}

			$result->free();
		}
		else
		{
			// Se chegar aqui, é provável que ocorreu um erro
			$can_delete = false;
		}

		$stmt->close();

		return $can_delete;
	}

	/**
	 * Verifica se atividade pode ser marcada como "concluída".
	 * 
	 * @param App\Model\Task $task 		Atividade a ser verificada
	 * @return bool
	 */
	public function canFinishTask($task)
	{
		if ($task->getStatus() != 1)
		{
			return true;
		}

		$category = $task->getCategory();

		if (!($category instanceof Category))
		{
			$category = $this->services->category->findById($category);

			if (!$category)
			{
				return true;
			}
		}

		$can_finish = true;

		// Devido a possibilidade de remover e adicionar categorias,
		// comparar somente a ID não irá funcionar como esperamos. Precisamos 
		// encontrar uma outra maneira de determinar se a categoria é urgente ou não.
		// Provavelmente, isso iria requerer um sistema onde o usuario define
		// quais são as categorias consideradas urgentes. 
		// Por hora, apenas verificamos se o nome contém a palavra "urgente" ou "atendimento".

		foreach (['urgente', 'atendimento'] as $word)
		{
			if (stripos($category->getName(), $word) !== false)
			{
				$can_finish = false;

				break;
			}
		}

		// Descrição deve conter no mínimo 50 caracteres.

		if ($can_finish || preg_match('/([^\s](?:\s?)){50}/', $task->getContent()) > 0)
		{
			return true;
		}

		$this->message = 'Atividades de atendimento ou urgência podem ser finalizadas somente com o mínimo de 50 caracteres.';

		return false;
	}
}

?>