<?php

namespace App\Collection;
use App\Model\User as User;

class UserCollection
{
	protected $secret_columns = [
		'senha'
	];

	public function findByLogin($email, $password)
	{
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		
		if (empty($email) || empty($password))
		{
			return null;
		}

		$query = 'SELECT * FROM `usuarios` WHERE `email`=? LIMIT 1';
		$result = $this->sqlQuery($query, 's', array($email));

		if (!$result)
		{
			return null;
		}

		$row = $result[0];

		if (!password_verify($password, $row['senha']))
		{
			return null;
		}

		$user = new User();

		foreach ($row as $key => $value)
		{
			if (!in_array($key, $this->secret_columns))
			{
				$user->$key = $value;
			}
		}

		return $user;
	}

	public function getDatabase()
	{
		return app()->module('database');
	}

	protected function sqlQuery($query, $types=null, ...$values)
	{
		$connection = $this->getDatabase();
		$stmt = $connection->prepare($query);

		if (!$stmt)
		{
			return null;
		}

		$data = null;

		if (isset($values))
		{
			foreach ($values as $value)
			{
				$stmt->bind_param($types, ...$value);

				if (!$stmt->execute())
				{
					continue;
				}

				$result = $stmt->get_result();

				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						$data[] = $row;
					}
				}

				$result->free();
			}
		}
		else
		{
			if ($stmt->execute())
			{
				$result = $stmt->get_result();

				if ($result->num_rows > 0)
				{
					$data = $result->fetch_all(MYSQLI_ASSOC);
				}

				$result->free();
			}
		}
	
		$stmt->close();

		return $data;
	}
}

?>