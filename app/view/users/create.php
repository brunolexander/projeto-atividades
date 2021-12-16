<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Adicionar usuário</h1>
</div>

<?php if (($error = app()->module('session')->get('error'))): ?>
<div class="alert alert--danger">
	<i class="alert__icon fas fa-times-circle"></i>
	<span class="alert__text"><?= $error; ?></span>
</div>
<?php endif; ?>

<form class="form" action="<?= app()->module('router')->url('users.store'); ?>" method="POST">

	<div class="card">
		<div class="card__body">
			<div class="form__group">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" class="form__input" required>
			</div>

			<div class="form__group">
				<label for="password">Senha</label>
				<input type="password" name="password" id="password" class="form__input" required>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card__head">
		<span class="card__title">Permissões</span>
		</div>

		<div class="card__body">
			<fieldset class="form__group">
				<legend>Usuários</legend>

				<input type="checkbox" name="permissions[]" value="view_users" id="view_users">
				<label for="view_users">Ver usuários</label>

				<input type="checkbox" name="permissions[]" value="add_users" id="add_users">
				<label for="add_users">Criar usuários</label>

				<input type="checkbox" name="permissions[]" value="edit_users" id="edit_users">
				<label for="edit_users">Editar usuários</label>

				<input type="checkbox" name="permissions[]" value="remove_users" id="remove_users">
				<label for="remove_users">Remover usuários</label>
			</fieldset>
			
			<fieldset class="form__group">
				<legend>Atividades</legend>

				<input type="checkbox" name="permissions[]" value="view_tasks" id="view_tasks" checked>
				<label for="view_tasks">Ver atividades</label>

				<input type="checkbox" name="permissions[]" value="add_tasks" id="add_tasks">
				<label for="add_tasks">Criar atividades</label>

				<input type="checkbox" name="permissions[]" value="edit_tasks" id="edit_tasks">
				<label for="edit_tasks">Editar atividades</label>

				<input type="checkbox" name="permissions[]" value="remove_tasks" id="remove_tasks">
				<label for="remove_tasks">Remover atividades</label>

				<input type="checkbox" name="permissions[]" value="end_tasks" id="end_tasks">
				<label for="end_tasks">Finalizar atividades</label>
			</fieldset>
		</div>

	</div>

	<button class="button ml-auto" type="submit">Criar usuário</button>
</form>

<?php app()->module('view')->load('layouts/footer.php'); ?>