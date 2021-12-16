
<?php

/**
 * Retorna diferença entre $timestamp e tempo atual
 * em um formato amigável.
 * 
 * 1 ano
 * 1 ano e 5 meses
 * 5 meses
 * 2 semanas
 * 5 dias e 1 hora
 * 2 horas e 30 minutos
 * 20 minutes e 15 segundos
 * 40 segundos
 */
function humanTimeDiff($timestamp)
{
	$time = new \DateTime();
	$time->setTimestamp($timestamp);
	$now = new \DateTime();
	$interval = $time->diff($now);

	$time = '';
	$weeks = floor($interval->days / 7.0);

	if ($interval->y > 0)
	{
		$time .= sprintf('%d %s', $interval->y, $interval->y > 1 ? 'anos' : 'ano');

		if ($interval->m > 0)
		{
			$time .= sprintf(' e %d %s', $interval->m, $interval->m > 1 ? 'meses' : 'mês');
		}
	}
	else if ($interval->m > 0)
	{
		$time .= sprintf('%d %s', $interval->m, $interval->m > 1 ? 'meses' : 'mês');
	}
	else if ($weeks > 0)
	{
		$time .= sprintf('%d %s', $weeks, $weeks > 1 ? 'semanas' : 'semana');
	}
	else if ($interval->days > 0)
	{
		$time .= sprintf('%d %s', $interval->days, $interval->days > 1 ? 'dias' : 'dia');

		if ($interval->h > 0)
		{
			$time .= sprintf(' e %d %s', $interval->h, $interval->h > 1 ? 'horas' : 'hora');
		}
	}
	else if ($interval->h > 0)
	{
		$time .= sprintf('%d %s', $interval->h, $interval->h > 1 ? 'horas' : 'hora');

		if ($interval->i > 0)
		{
			$time .= sprintf(' e %d %s', $interval->i, $interval->i > 1 ? 'minutos' : 'minuto');
		}
	}
	else if ($interval->i > 0)
	{
		$time .= sprintf('%d %s', $interval->i, $interval->i > 1 ? 'minutos' : 'minuto');

		if ($interval->s > 0)
		{
			$time .= sprintf(' e %d %s', $interval->s, $interval->s > 1 ? 'segundos' : 'segundo');
		}
	}
	else
	{

		$time .= 'a alguns segundos';
		//$time .= sprintf('%d %s', $interval->s, $interval->s > 1 ? 'segundos' : 'segundo');
	}

	return $time;
}

?>

<?php app()->module('view')->load('layouts/header.php'); ?>

<div class="page__header">
	<h1>Usuários</h1>
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
	<div class="card__body">
		<table class="table">
			<thead class="thead">
				<tr class="tr">
					<th class="th">Nome</th>
					<th class="th">Email</th>
					<th class="th">Atividades criadas</th>
					<th class="th">Último acesso</th>
				</tr>
			</thead>

			<tbody class="tbody">

			<?php if (isset($users)): ?>

				<?php foreach ($users as $user): ?>

				<tr class="tr">
					<td class="td"><?= empty($user->getName()) ? '-' : htmlspecialchars($user->getName()); ?></td>
					<td class="td"><?= $user->getEmail(); ?></td>
					<td class="td"><?= $user->getNumTasksCreated(); ?></td>

					<?php $last_access = $user->getLastAccess(); ?>

					<td class="td"><?= $last_access != 0 ? humanTimeDiff($last_access) : '-'; ?></td>
				</tr>

				<?php endforeach; ?>

			<?php else: ?>

				<tr class="tr">
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

<?php app()->module('view')->load('layouts/footer.php'); ?>