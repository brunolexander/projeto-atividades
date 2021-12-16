<?php

namespace App\Collection;

use App\Model\Task;
use App\Model\User;
use App\Model\Category;

/**
 * Classe responsável por manipular operações relacionadas
 * a múltiplas atividades.
 */
class TaskCollection
{
	protected $connection;

	public function __construct(\mysqli $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Obtem todos as tarefas
	 * 
	 * @return array App\Model\Task ou null
	 */
	public function getAll()
	{
		$stmt = $this->connection->prepare('SELECT atividade.*, categoria.nome AS categoria_nome, COALESCE(usuario.nome, usuario.email) AS usuario_nome FROM atividades atividade LEFT JOIN categorias categoria ON atividade.categoria=categoria.id LEFT JOIN usuarios usuario ON atividade.autor=usuario.id ORDER BY atividade.finalizada ASC, atividade.criado_em DESC');
		
		if (!$stmt)
		{
			return null;
		}

		$collection = null;

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$task = new Task();

					$task->setId($row['id']);
					$task->setTitle($row['titulo']);
					$task->setContent($row['descricao']);
					$task->setCreatedAt($row['criado_em']);
					$task->setUpdatedAt($row['atualizado_em']);
					$task->setExpiryAt($row['expira_em']);
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

					$collection[] = $task;
				}
			}

			$result->free();
		}
		
		$stmt->close();

		return $collection;
	}
}

?>