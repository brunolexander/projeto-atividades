<?php

namespace App\Services;

use App\Collection\CategoryCollection;
use App\Model\Category;

class CategoryService
{
	protected $connection;

	public $message;

	protected $collection;

	public function __construct(\mysqli $connection, CategoryCollection $collection)
	{
		$this->connection = $connection;
		$this->collection = $collection;
	}

	/**
	 * Adiciona múltiplas categorias
	 * 
	 * @param array $categories 	Lista com os nomes das categorias para adicionar
	 * 
	 */
	public function create($categories)
	{
		$collection = [];

		foreach (explode(',', $categories) as $name)
		{
			$name = trim($name);

			if (!empty($name))
			{
				$category = new Category();
				$category->setName($name);
				$collection[] = $category;
			}
		}

		if (empty($collection))
		{
			$this->message = 'Você deve digitar ao menos uma categoria.';

			return false;
		}

		if (!$this->collection->addAll($collection))
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$this->message = 'Categorias adicionadas com sucesso!';

		return true;
	}

	/**
	 * Exclui múltiplas categorias
	 * 
	 * @param array $categories 	Lista com ID das categorias para deletar
	 */
	public function destroy($categories)
	{
		if (empty($categories))
		{
			$this->message = 'Você deve selecionar ao menos uma categoria.';

			return false;
		}

		$collection = [];

		foreach ($categories as $id)
		{
			$category = new Category();
			$category->setId($id);
			$collection[] = $category;
		}

		if (!$this->collection->removeAll($collection))
		{
			$this->message = 'Ops! Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.';

			return false;
		}

		$this->message = 'Categorias removidas com sucesso!';

		return true;
	}

	public function findById($id)
	{
		$stmt = $this->connection->prepare('SELECT * FROM `categorias` WHERE `id`=?');

		if (!$stmt)
		{
			return null;
		}

		$category = null;

		$stmt->bind_param('i', $id);

		if ($stmt->execute())
		{
			$result = $stmt->get_result();

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$category = new Category();
				$category->setId($id);
				$category->setName($row['nome']);
			}
		}

		$stmt->close();

		return $category;
	}
}


?>