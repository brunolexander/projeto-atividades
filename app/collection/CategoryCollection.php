<?php

namespace App\Collection;

use App\Model\Category;

class CategoryCollection
{
	/**
	 * Conexão com banco de dados
	 * 
	 * @var mysqli
	 */
	protected $connection;

	/**
	 * Construtor
	 */
	public function __construct(\mysqli $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Obtem todos as categorias
	 * 
	 * @return array App\Model\Category
	 */
	public function getAll()
	{
		$stmt = $this->connection->prepare('SELECT * FROM `categorias` ORDER BY `nome` ASC;');
		
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
					$category = new Category();

					$category->setId($row['id']);
					$category->setName($row['nome']);

					$collection[] = $category;
				}
			}

			$result->free();
		}
		
		$stmt->close();

		return $collection;
	}

	/**
	 * Adiciona todas a categorias.
	 * Caso a operação falhe, nenhuma categoria é adicionada.
	 * 
	 * @param array App\Model\Category $categories
	 * @return bool
	 */
	public function addAll($categories)
	{
		$stmt = $this->connection->prepare('INSERT INTO `categorias` (`nome`) VALUES (?)');

		var_dump($categories);

		if (!$stmt)
		{
			return false;
		}

		$success = true;

		$this->connection->begin_transaction();

		$stmt->bind_param('s', $name);

		foreach ($categories as $category)
		{
			$name = $category->getName();

			if (!$stmt->execute())
			{
				$success = false;

				break;
			}
		}

		if ($success)
		{
			$this->connection->commit();
		}
		else
		{
			$this->connection->rollback();
		}

		return $success;
	}

	/**
	 * Exclui múltiplas categorias
	 * 
	 * @param array App\Model\Category $categories 		Lista com categorias para deletar
	 */
	public function removeAll($categories)
	{
		$stmt = $this->connection->prepare('DELETE FROM `categorias` WHERE `id`=?');

		if (!$stmt)
		{
			return false;
		}

		$stmt->bind_param('i', $id);

		foreach ($categories as $category)
		{
			$id = $category->getId();

			$stmt->execute();
		}

		$stmt->close();

		return true;
	}
}

?>