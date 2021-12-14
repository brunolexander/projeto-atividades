<?php

namespace App\Model;

class User
{
	public $data = array();

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->data))
		{
			return $this->data[$key];
		}

		return null;
	}
}

?>