<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Categorias</h1>
</div>

<?php if (($message = app()->module('session')->get('notify'))): ?>
<div class="alert alert--success">
	<i class="alert__icon fas fa-check-circle"></i>
	<span class="alert__text"><?= $message; ?></span>
</div>
<?php endif; ?>

<?php if (($error = app()->module('session')->get('error'))): ?>
<div class="alert alert--danger">
	<i class="alert__icon fas fa-times-circle"></i>
	<span class="alert__text"><?= $error; ?></span>
</div>
<?php endif; ?>

<div class="card">
	<div class="card__head">
		<span class="card__title">Adicionar categoria</span>
	</div>

	<div class="card__body">
		<form action="<?= app()->module('router')->url('category.store'); ?>" class="form" method="POST">
			<div class="form__group">
				<input type="name" name="name" id="name" class="form__input" required>
				<span class="form__text">Separe as categorias por uma vírgula para adicionar múltiplas (ex.: desenvolvimento , urgente , manutenção)</span>
			</div>

			<div class="form__group">
				<button class="button" type="submit">Adicionar</button>
			</div>
		</form>
	</div>
</div>

<?php if (isset($categories)): ?>

<form action="<?= app()->module('router')->url('category.destroy'); ?>" method="POST">

<div class="card">

	<div class="card__body">


		<ul class="categories">
		
		<?php

			$color = 0;
			$colors = ['red', 'green', 'blue', 'orange'];
			$num_colors = count($colors);
		?>
		
		<?php foreach ($categories as $category): ?>

		<li>
			<label class="category category--<?=$colors[$color];?>">
				<input type="checkbox" name="category[]" value="<?= $category->getId(); ?>">
				<?= ucfirst(htmlspecialchars($category->getName())); ?>
			</label>
		</li>
			
		<?php $color = ++$color % $num_colors; ?>

		<?php endforeach; ?>

		</ul>

	</div>

</div>

	<button class="button ml-auto" type="submit">Excluir selecionados</button>

</form>

<?php endif; ?>
		
<?php app()->module('view')->load('layouts/footer.php'); ?>