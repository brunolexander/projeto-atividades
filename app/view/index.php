<?php app()->module('view')->load('layouts/header'); ?>

<div class="page__header">
	<h1>Painel</h1>
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

<?php app()->module('view')->load('layouts/footer'); ?>