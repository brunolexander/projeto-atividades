<?php

namespace App\Services;

use App\Model\User;

/**
 * Classe resposável pela interação entre um controller
 * e dados/lógica de usuários. 
 */
class UserService
{
	/**
	 * Conexão com banco de dados
	 * 
	 * @var mysqli
	 */
	protected $connection;

	/**
	 * Mensagem para ser passada ao cliente.
	 * 
	 * @var string
	 */
	public $message;


	/**
	 * Construtor
	 * 
	 * @param mysqli $connection 	Conexão com banco de dados
	 */
	public function __construct(\mysqli $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Valida e insere um novo usuário no banco de dados.
	 * 
	 * @param App\Model\User $user 	Usuário para inserir
	 * @param string $password 		Senha do usuário
	 * @return bool
	 */
	public function create(User $user, $password)
	{
		if (!$this->validateUser($user))
		{
			return false;
		}

		if (!$this->validatePassword($password))
		{
			return false;
		}

		if ($this->findByEmail($user->getEmail()))
		{
			$this->message = 'Uma conta já foi registrada com este e-mail.';

			return false;
		}

		$stmt = $this->connection->prepare('INSERT INTO `usuarios` (`nome`, `email`, `senha`, `criado_em`, `permissao`) VALUES (?, ?, ?, ?, ?)');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$name = $user->getName();
		$email = $user->getEmail();
		$password = password_hash($password, PASSWORD_BCRYPT);
		$created_at = time();
		$permission = $user->getPermission();
		$stmt->bind_param('sssii', $name, $email, $password, $created_at, $permission);

		$success = false;

		if (!$stmt->execute())
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';
		}
		else
		{
			$this->message = 'Usuário adicionado com sucesso!';

			$success = true;
		}

		$stmt->close();

		return $success;
	}

	/**
	 * Atualiza informações de um usuário já existente.
	 * 
	 * @param App\Model\User $user 		Usuário para atualizar
	 * @return bool
	 */
	public function update(User $user, $password=null)
	{
		if (!$this->validateUser($user))
		{
			return false;
		}

		if (!empty($password) && !$this->validatePassword($password))
		{
			return false;
		}

		$stmt = $this->connection->prepare('SELECT `email`, `senha`, `session_hash` FROM `usuarios` WHERE `id`=?');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$success = false;

		$id = $user->getId();
		$stmt->bind_param('i', $id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$email = $row['email'];
				$password_hash = $row['senha'];
				$session_id = $row['session_hash'];

				$success = true;
			}

			$result->free();
		}

		$stmt->close();

		if (!$success)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$stmt = $this->connection->prepare('UPDATE `usuarios` SET `nome`=?,`email`=?,`permissao`=?, `acesso_em`=?,`senha`=? WHERE `id`=?');

		if (!$stmt)
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}
	
		$stmt->bind_param('ssiisi', $user_name, $user_email, $user_permission, $user_last_access, $user_password, $user_id);

		$user_name = $user->getName();
		$user_email = $user->getEmail();
		$user_permission = $user->getPermission();
		$user_last_access = $user->getLastAccess();
		$user_id = $user->getId();
		$user_password = $password_hash;

		if (!empty($password))
		{
			$user_password = password_hash($password, PASSWORD_BCRYPT);
		}

		$stmt->execute();

		$success = false;

		if ($this->connection->affected_rows > 0)
		{
			// Caso o usuário altere a senha ou e-mail, devemos
			// recriar a sessão por questões de segurança.
			if (strcasecmp($email, $user_email) != 0 || $user_password !== $password_hash)
			{
				if (session_id())
				{
					session_commit();
				}

				session_start();
				$current_session_id = session_id();
				session_commit();

				session_id($session_id);
				session_start();
				session_destroy();
				session_commit();

				session_id($current_session_id);
				session_start();
				session_commit();
			}

			$success = true;

			$this->message = 'Dados atualizados com sucesso!';
		}
		else
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde';
		}

		$stmt->close();

		return $success;
	}

	public function delete(User $user)
	{

	}

	/**
	 * Verifica se a senha especificada é válida para o usuário.
	 * 
	 * @param App\Model\User $user 		Usuário
	 * @param string $password 			Senha
	 * @return bool 
	 */
	public function checkPassword(User $user, $password)
	{
		$stmt = $this->connection->prepare('SELECT `senha` FROM `usuarios` WHERE `id`=?');

		if (!$stmt)
		{
			return false;
		}

		$success = false;

		$id = $user->getId();
		$stmt->bind_param('i', $id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				list($hash) = $result->fetch_row();

				if (password_verify($password, $hash))
				{
					$success = true;
				}
			}

			$result->free();
		}
		
		$stmt->close();

		return $success;
	}

	/**
	 * Busca um usuário no banco de dados por e-mail.
	 * 
	 * @param $email 		E-mail para buscar.
	 * @return App\Model\User ou null
	 */
	public function findByEmail($email)
	{
		$stmt = $this->connection->prepare('SELECT usuario.*, COUNT(atividade.id) AS total_atividades FROM `usuarios` usuario LEFT JOIN `atividades` atividade ON usuario.id=atividade.autor WHERE `email`=?');

		if (!$stmt)
		{
			return null;
		}

		$user = null;

		$stmt->bind_param('s', $email);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();

				$user = new User();
				$user->setId($row['id']);
				$user->setEmail($row['email']);
				$user->setName($row['nome']);
				$user->setCreatedAt($row['criado_em']);
				$user->setLastAccess($row['acesso_em']);
				$user->setPermission($row['permissao']);
				$user->setNumTasksCreated($row['total_atividades']);
			}

			$result->free();
		}

		$stmt->close();

		return $user;
	}

	/**
	 * Busca um usuário no banco de dados por id.
	 * 
	 * @return App\Model\User ou null
	 */
	public function findById($id)
	{
		$stmt = $this->connection->prepare('SELECT usuario.*, COUNT(atividade.id) AS total_atividades FROM `usuarios` usuario LEFT JOIN `atividades` atividade ON usuario.id=atividade.autor WHERE usuario.id=?');

		if (!$stmt)
		{
			return null;
		}

		$user = null;

		$stmt->bind_param('i', $id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();

				$user = new User();
				$user->setId($row['id']);
				$user->setEmail($row['email']);
				$user->setName($row['nome']);
				$user->setCreatedAt($row['criado_em']);
				$user->setLastAccess($row['acesso_em']);
				$user->setPermission($row['permissao']);
				$user->setNumTasksCreated($row['total_atividades']);
			}

			$result->free();
		}

		$stmt->close();

		return $user;
	}

	/**
	 * Valida os dados do usuário.
	 * 
	 * @param App\Model\User $user 		Usuário
	 * @return bool
	 */
	public function validateUser(User $user)
	{
		if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL))
		{
			$this->message = 'O endereço de e-mail precisa ser válido.';

			return false;
		}

		return true;
	}

	/**
	 * Valida a senha do usuário.
	 * 
	 * @param string $password 		Senha
	 * @return bool
	 * 
	 */
	public function validatePassword($password)
	{
		if (strlen($password) < 4)
		{
			$this->message = 'A senha deve conter no mínimo 4 caracteres.';

			return false;
		}

		return true;
	}

	/**
	 * Atualiza o hash da sessão do usuário.
	 * 
	 * @param App\Model\User $user 	Usuário
	 * @param string $hash 			Hash da sessão
	 * @return bool
	 */
	public function updateSessionHash(User $user, $hash)
	{
		$stmt = $this->connection->prepare('UPDATE `usuarios` SET `session_hash`=? WHERE `id`=?');

		if (!$stmt)
		{
			return false;
		}

		$id = $user->getId();
		$success = false;

		$stmt->bind_param('si', $hash, $id);

		if ($stmt->execute() && $this->connection->affected_rows > 0)
		{
			$success = true;
		}

		$stmt->close();

		return $success;
	}
}


?>