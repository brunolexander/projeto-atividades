<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Adicionar atividade</h1>
</div>

<?php if (date('w') == 5 && date('G') >= 13): ?>
<div class="alert alert--warning">
	<i class="alert__icon fas fa-exclamation-circle"></i>
	<span class="alert__text">Atividades de urgência poderão ser criadas apenas a partir de amanhã.</span>
</div>
<?php endif; ?>

<?php if (($error = app()->module('session')->get('error'))): ?>
<div class="alert alert--danger">
	<i class="alert__icon fas fa-times-circle"></i>
	<span class="alert__text"><?= $error; ?></span>
</div>
<?php endif; ?>

<form class="form" action="<?= app()->module('router')->url('tasks.store'); ?>" method="POST">

	<div class="card">
		<div class="card__body">
			<div class="form__group">
				<label for="title">Título</label>
				<input type="title" name="title" id="title" class="form__input" value="<?= app()->module('session')->get('title'); ?>" required>
			</div>

			<div class="form__group">
				<span>Categoria</span>
				<select class="form__input" name="category">
					<option value="0">(Sem categoria)</option>

				<?php if (isset($categories)): ?>
					<?php $old_category = app()->module('session')->get('category'); ?>
					<?php foreach($categories as $category): ?>
						<option value="<?= $category->getId(); ?>" <?= ($category->getId() === $old_category) ? 'selected' : '' ?>>
							<?= ucfirst(htmlspecialchars($category->getName())); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
					
				</select>
			</div>

			<div class="form_group">
				<label for="status">Status</label>
				<select class="form__input" name="status" id="status">
					<option value="0">Pendente</option>
					<option value="1" <?= app()->module('session')->get('status') == 1 ? 'selected' : '' ?>>Concluída</option>
				</select>
			</div>

			<div class="form__group">
				<label for="content">Descrição</label>
				<textarea name="content" class="textarea"><?= app()->module('session')->get('content'); ?></textarea>
			</div>
		</div>
	</div>

	<button class="button ml-auto" type="submit">Criar atividade</button>

</form>

<?php app()->module('view')->load('layouts/footer.php'); ?>