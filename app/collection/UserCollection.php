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
		$query = <<<SQL
			SELECT
			    usuario.*,
			    COUNT(DISTINCT atividade.id) AS total_atividades,
			    GROUP_CONCAT(permissao.nome) AS permissoes
			FROM
			    `usuarios` usuario
			LEFT JOIN `atividades` atividade ON
			    usuario.id = atividade.autor
			LEFT JOIN `permissoes_usuario` permissao_usuario ON
			    usuario.id = permissao_usuario.usuario_id
			LEFT JOIN `permissoes` permissao ON
			    permissao_usuario.permissao_id = permissao.id
			GROUP BY
				usuario.id
			ORDER BY COALESCE(usuario.nome, usuario.email) ASC;
SQL;

		$stmt = $this->connection->prepare($query);

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
					$user->setPermission(explode(',', $row['permissoes']));
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