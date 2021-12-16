<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Editar atividade</h1>
</div>

<?php $session = app()->module('session'); ?>

<?php if (date('w') == 5 && date('G') >= 13): ?>
<div class="alert alert--warning">
	<i class="alert__icon fas fa-exclamation-circle"></i>
	<span class="alert__text">Atividades de urgência poderão ser criadas apenas a partir de amanhã.</span>
</div>
<?php endif; ?>

<?php if (($message = app()->module('session')->get('notify'))): ?>
<div class="alert alert--success">
	<i class="alert__icon fas fa-check-circle"></i>
	<span class="alert__text"><?= $message; ?></span>
</div>
<?php endif; ?>

<?php if (($error = $session->get('error'))): ?>
<div class="alert alert--danger">
	<i class="alert__icon fas fa-times-circle"></i>
	<span class="alert__text"><?= $error; ?></span>
</div>
<?php endif; ?>

<?php

if ($session->has('title'))
{
	$title = $session->get('title');
}
else
{
	$title = $task->getTitle();
}

if ($session->has('category'))
{
	$old_category = $session->get('category');
}
else
{
	$old_category = $task->getCategory();
}

if ($session->has('content'))
{
	$content = $session->get('content');
}
else
{
	$content = $task->getContent();
}

if ($old_category instanceof \App\Model\Category)
{
	$old_category = $old_category->getId();
}

if ($session->has('status'))
{
	$status = $session->get('status');

}
else
{
	$status = $task->getStatus();
}

?>

<form class="form" action="<?= app()->module('router')->url('tasks.update', ['id' => $task->getId()]); ?>" method="POST">

	<div class="card">
		<div class="card__body">
			<div class="form__group">
				<label for="title">Título</label>
				<input type="title" name="title" id="title" class="form__input" value="<?= $title; ?>" required>
			</div>

			<div class="form__group">
				<span>Categoria</span>
				<select class="form__input" name="category">
					<option value="0">(Sem categoria)</option>

				<?php if (isset($categories)): ?>
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
					<option value="1" <?= $task->getStatus() == 1 ? 'selected' : '' ?>>Concluída</option>
				</select>
			</div>

			<div class="form__group">
				<label for="content">Descrição</label>
				<textarea name="content" class="textarea"><?= $content; ?></textarea>
			</div>
		</div>
	</div>

	<div class="button__group--right">
		<button class="button mr-2" type="submit">Salvar alterações</button>
		<a class="button button--2" href="<?= app()->module('router')->url('tasks.destroy', ['id' => $task->getId()]);?>" target="_self">Excluir</a>
	</div>

</form>

<?php app()->module('view')->load('layouts/footer.php'); ?>