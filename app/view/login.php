<?php app()->module('view')->load('layouts/header'); ?>

<?php if (!empty($error)): ?>
	<div class="error"><?= $error; ?></div>
<?php endif; ?>

<form action="<?= app()->module('router')->url('login.submit'); ?>" method="POST">
	<label for="user_email">Email</label>
	<input type="email" class="form__input" name="email" id="user_email" value="<?=$email;?>">

	<label for="user_pass">Senha</label>
	<input type="password" class="form__input" name="password" id="user_pass">

	<button type="submit">Entrar</button>
</form>

<p>Email: admin@admin.com</p>
<p>Senha: admin</p>

<?php app()->module('view')->load('layouts/footer'); ?>