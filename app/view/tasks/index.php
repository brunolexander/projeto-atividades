<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Atividades</h1>
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

<i class="hint">Clique em uma atividade para exibir detalhes.</i>

<div class="card">

	<div class="card__body">
		<table class="table">
			<thead class="thead">
				<tr class="tr">
					<th class="th">Título</th>
					<th class="th">Tipo</th>
					<th class="th">Criado por</th>
					<th class="th">Criado em</th>
					<th class="th">Status</th>
				</tr>
			</thead>

			<tbody class="tbody">

			<?php if (isset($tasks)): ?>

				<?php foreach ($tasks as $task): ?>

			<?php

				$category = $task->getCategory();

				if (isset($category))
				{
					$category = ucfirst(htmlspecialchars($category->getName()));
				}
				else
				{
					$category = '<i>(Sem categoria)</i>';
				}

				$title = htmlspecialchars($task->getTitle());
				$author = htmlspecialchars($task->getAuthor()->getName());
			?>

			<div class="modal modal--<?=$task->getId();?>">
				<div class="modal__container">
					<div class="modal__content">
						<div class="modal__head">
							<span class="modal__title"><?= $title; ?></span>
							<button type="submit" class="modal__close ml-auto fas fa-times" data-dimiss=".modal"></button>
						</div>

						<div class="modal__body">
							<div class="task__description"><?= htmlspecialchars($task->getContent()); ?></div>

							<ul class="task__info">
								<li><i class="icon fas fa-tag"></i>Tipo: <?= $category ?></li>
								<li><i class="icon fas fa-user"></i>Criado por: <?= $author; ?></li>
								<li><i class="icon far fa-calendar"></i>Data: <?= date('F\, d \'Y \á\s H:i', $task->getCreatedAt()); ?></li>
<li>
	<i class="icon far fa-lightbulb"></i>Status: 

<?php switch ($task->getStatus()): ?>
<?php case 1: ?>
<span class="text--finished">Concluída <i class="fas fa-check-circle"></i></span>
<?php break; ?>
<?php default: ?>
<span class="text--pending">Pendente <i class="fas fa-exclamation-triangle"></i></span>
<?php break; ?>
<?php endswitch; ?>

</li>
							</ul>
						</div>

						<div class="modal__footer">
							<a href="<?= app()->module('router')->url('tasks.destroy', ['id' => $task->getId()]); ?>" class="button modal__button button--2" target="_self"><i class="far fa-trash-alt"></i> Excluir</a>

							<a href="<?= app()->module('router')->url('tasks.edit', ['id' => $task->getId()]); ?>" class="button modal__button button--1" target="_self"><i class="fas fa-pen"></i> Editar</a>

							<button type="button" class="button modal__button" data-dimiss=".modal"><i class="fas fa-times"></i> Fechar</button>
						</div>
					</div>
				</div>
			</div>

				<tr class="tr">
					<td class="td task__title" data-show=".modal--<?= $task->getId(); ?>"><?= $title; ?></td>
					<td class="td"><?= $category; ?></td>
					<td class="td"><?= $author; ?></td>
					<td class="td"><?= date('M d, Y \à\s H:i', $task->getCreatedAt()); ?></td>

<?php switch ($task->getStatus()): ?>
<?php case 1: ?>
	<td class="td"><span class="status status--finished">Concluída <i class="fas fa-check-circle"></i></span></td>
<?php break; ?>
<?php default: ?>
	<td class="td"><span class="status status--pending">Pendente <i class="fas fa-exclamation-circle"></i></span></td>
<?php break; ?>
<?php endswitch; ?>

				</tr>

				<?php endforeach; ?>

			<?php else: ?>

				<tr class="tr">
					<td class="td">-</td>
					<td class="td">-</td>
					<td class="td">-</td>
					<td class="td">-</td>
					<td class="td">-</td>
				</tr>

			<?php endif; ?>

			</tbody>
		</table>
	</div>
</div>

<script src="/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script type="text/javascript">
	$(function() {
		$('[data-dimiss]').click(function() {
			var modal = $(this).data('dimiss');
			$(modal).fadeOut('slow');
			$('.modal__content', $(modal)).animate({height: 'hide', opacity: 'hide'}, 'slow');
		});

		$('[data-show]').click(function() {
			var modal = $(this).data('show');
			$(modal).fadeIn();
			$('.modal__content', $(modal)).animate({height: 'show', opacity: 'show'}, 'slow');
		});

		$(document).on('keydown', function(e) {
			if (e.keyCode === 27)
			{
				$('.modal:visible').fadeOut('slow');
				$('.modal:visible .modal__content').animate({height: 'hide', opacity: 'hide'}, 'slow');
			}
		});
	});
</script>

<?php app()->module('view')->load('layouts/footer.php'); ?>