<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Título da página</title>

	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<link rel="stylesheet" type="text/css" href="/lib/fontawesome/css/all.min.css">
</head>
<body class="body">

<div class="sidenav">
	<span class="nav__brand">Painel</span>

	<ul class="nav">
		
		<span class="menu__title">Atividades</span>

		<li>
			<a class="nav__link" href="<?= app()->module('router')->url('tasks.index'); ?>">
				<i class="nav__icon far fa-clipboard"></i> Ver todas
			</a>
		</li>

		<li>
			<a class="nav__link" href="<?= app()->module('router')->url('tasks.create'); ?>">
				<i class="nav__icon fas fa-plus"></i> Adicionar
			</a>
		</li>

		<li>
			<a class="nav__link" href="<?= app()->module('router')->url('category.index'); ?>">
				<i class="nav__icon fas fa-tags"></i> Categorias
			</a>
		</li>

		<span class="menu__title">Usuários</span>

		<li>
			<a class="nav__link" href="<?= app()->module('router')->url('users.index'); ?>">
				<i class="nav__icon fas fa-user"></i> Ver todos
			</a>
		</li>

		<li>
			<a class="nav__link" href="<?= app()->module('router')->url('users.create'); ?>">
				<i class="nav__icon fas fa-plus"></i> Adicionar
			</a>
		</li>
	</ul>
</div>

<div class="container">
	<nav class="topnav">
		<div class="topnav__user">
			<img class="user__image" src="/img/user_avatar.png" alt="Usuário">

			<span class="user__name">Admin</span>

			<div class="dropdown">
				<ul class="dropdown__menu">
					<li><a href="<?= app()->module('router')->url('logout'); ?>" class="dropdown__item">Sair</a></li>
				</ul>
			</div>
		</div>
	</nav>	

	<main class="main">