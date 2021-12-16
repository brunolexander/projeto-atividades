<?php

namespace App\Modules;

class Session
{
	protected $delete = array();

	public function __construct()
	{
		register_shutdown_function([$this, 'shutdown']);

		if (session_status() == PHP_SESSION_NONE)
		{
			session_start();
		}

		if (isset($_SESSION['flash']))
		{
			$this->delete = array_keys($_SESSION['flash']);
		}
	}

	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public function get($key)
	{
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}

		if (isset($_SESSION['flash'][$key]))
		{
			return $_SESSION['flash'][$key];
		}

		return null;
	}

	public function flash($key, $value)
	{
		if (!isset($_SESSION['flash']))
		{
			$_SESSION['flash'] = array();
		}
	
		$_SESSION['flash'][$key] = $value;

		$index = array_search($key, $this->delete);

		if ($index !== false)
		{
			unset($this->delete[$index]);
		}
	}

	public function has($key)
	{
		return (bool) (isset($_SESSION[$key]) || isset($_SESSION['flash'][$key]));
	}

	public function shutdown()
	{
		foreach ($this->delete as $key)
		{
			unset($_SESSION['flash'][$key]);
		}
	}
}


?>