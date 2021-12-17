<?php

namespace App\Services;
use App\Model\User as User;
use App\Services\UserService;

class AuthService
{
	public function __construct($user=UserService::class)
	{
		if (session_status() == PHP_SESSION_NONE)
		{
			session_start();
		}

		$connection = app()->module('database');

		$this->services = (object) ['user' => new $user($connection)];
	}

	public function isSignedIn()
	{
		$addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

		if (!isset($_SESSION['addr']) || $_SESSION['addr'] !== $addr)
		{
			return false;
		}

		if (!isset($_SESSION['login']) || $_SESSION['login'] !== true)
		{			
			return false;
		}

		$user = $this->services->user->findById($_SESSION['user']);

		if (!$user)
		{
			$this->signOut();

			return false;
		}

		$time = time();

		if (!isset($_SESSION['last_seen']) || $time - $_SESSION['last_seen'] > 600)
		{
			$user->setLastAccess($time);
			
			$this->services->user->update($user);
		}

		$_SESSION['last_seen'] = $time;

		return true;
	}

	public function passwordSignIn($email, $password)
	{
		if (!($email instanceof User))
		{
			$user = $this->services->user->findByEmail($email);

			if (!$user)
			{
				return false;
			}
		}

		if (!$this->services->user->checkPassword($user, $password))
		{
			return false;
		}

		$this->signIn($user);

		return true;
	}

	public function signIn(User $user)
	{
		$_SESSION['login'] = true;
		$_SESSION['user'] = $user->getId();
		$_SESSION['addr'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);

		$this->services->user->updateSessionHash($user, session_id());
	}

	public function signOut()
	{
		unset($_SESSION['login']);
		unset($_SESSION['user']);
		unset($_SESSION['addr']);
		unset($_SESSION['last_seen']);
	}

	/**
	 * Obtem a ID do usuário atual
	 */
	public function getCurrentUserId()
	{
		return $_SESSION['user'];
	}
}

?>