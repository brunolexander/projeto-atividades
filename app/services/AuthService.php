<?php

namespace App\Services;
use App\Model\User as User;

class AuthService
{
	const STATUS_TIMEOUT = -1;
	const STATUS_LOGOUT = 0;
	const STATUS_AUTHENTICATED = 1;

	public function __construct()
	{
		if (session_status() == PHP_SESSION_NONE)
		{
			session_start();
		}
	}

	public function isSignedIn()
	{
		$addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

		if (!isset($_SESSION['addr']) || $_SESSION['addr'] !== $addr)
		{
			return self::STATUS_TIMEOUT;
		}

		if (!isset($_SESSION['login']) || $_SESSION['login'] !== true)
		{
			return self::STATUS_LOGOUT;
		}

		return self::STATUS_AUTHENTICATED;
	}

	public function signIn(User $user)
	{
		$_SESSION['login'] = true;
		$_SESSION['user'] = $user->id;
		$_SESSION['addr'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	}

	public function signOut()
	{
		unset($_SESSION['login']);
		unset($_SESSION['user']);
		unset($_SESSION['addr']);
	}
}

?>