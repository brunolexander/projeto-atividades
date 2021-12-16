<?php

namespace App\Collection;
use App\Model\User;

class UserCollection
{
	protected $connection;

	public function __construct(\mysqli $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Obtem todos os usuários
	 * 
	 * @return array App\Model\User
	 */
	public function getAll()
	{
		$stmt = $this->connection->prepare('SELECT *, COUNT(atividade.id) AS total_atividades FROM `usuarios` usuario LEFT JOIN `atividades` atividade ON usuario.id=atividade.autor ORDER BY COALESCE(`nome`, `email`) ASC;');
		
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
					$user = new User();

					$user->setId($row['id']);
					$user->setEmail($row['email']);
					$user->setName($row['nome']);
					$user->setPermission($row['permissao']);
					$user->setCreatedAt($row['criado_em']);
					$user->setLastAccess($row['acesso_em']);
					$user->setNumTasksCreated($row['total_atividades']);

					$collection[] = $user;
				}
			}

			$result->free();
		}
		
		$stmt->close();

		return $collection;
	}
}

?>