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

<?php 

$error = app()->module('session')->get('error');
$email = app()->module('session')->get('email');

?>

<div class="container">
	<main class="login">
		<div class="card login__form">
			<div class="card__head">
				<img src="/img/admin.png" alt="Painel" class="login__image">
				<span class="login__title">Painel</span>
			</div>

			<div class="card__body">
				
				<?php if ($error): ?>
					<div class="alert alert--danger">
						<i class="alert__icon fas fa-times-circle"></i>
						<span class="alert__text"><?= $error; ?></span>
					</div>
				<?php endif; ?>

				<form class="form" action="<?= app()->module('router')->url('login.submit'); ?>" method="POST">
					<div class="form__group">
						<label for="email">E-mail</label>
						<input type="email" class="form__input" name="email" id="email" value="<?=$email;?>">
					</div>

					<div class="form__group">
						<label for="password">Senha</label>
						<input type="password" class="form__input" name="password" id="password">
					</div>

					<div class="form__group">
						<button class="button" type="submit">Entrar</button>
					</div>
				</form>

				<p>
					Email: admin@admin.com
					<br>Senha: admin
				</p>

			</div>
		</div>
	</main>
</div>
</body>
</html>