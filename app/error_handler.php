<?php

function display_error_page($errno, $errstr, $errfile, $errline, $backtrace)
{

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ocorreu um erro</title>

	<style type="text/css">
		*, *::before, *::after {
			box-sizing: border-box;
		}

		body {
			font-family: system-ui, 'Open Sans', 'Verdana', 'Arial';
			margin: 0;
			background-color: #272822;
			color: #fff;
		}

		.heading {
			background-color: #0f0f0f;
			color: #d8780f;
			margin-top: 0;
			padding: 40px;
			margin-bottom: 60px;
		}


		.error__title {
			background-color: #0f0f0f;
			color: #d8780f;
			padding: 10px;
			font-weight: bold;
			font-size: 1.3em;
			text-align: center;
		}

		.error__file {
			padding: 20px;
			overflow: hidden;
			background-color: #1d1d1d;
		}

		.backtrace {
			margin-bottom: 40px;
			margin-top: 20px;
			width: 70%;
		}

		.backtrace__content {
			height: 400px;
			white-space: pre;
			background-color: #0f0f0f;
			color: #d8780f;
			padding: 20px;
			font-family: inherit;
			width: 100%;
		}

		.container {
			display: flex;
			flex-direction: column;
			align-items: center;
		}

	</style>
</head>
<body>
	<h1 class="heading">Ocorreu um erro</h1>

	<div class="container">
		<div class="error">
			<div class="error__title"><?= $errstr ?> (código <?= $errno ?>) </div>
			<div class="error__file">
				<?= $errfile ?>
				<br>
				Linha <?= $errline ?>
			</div>
		</div>

		<div class="backtrace">
			<h3 class="backtrace__title">Backtrace</h3>

			<textarea spellcheck="false" class="backtrace__content">
				<?= $backtrace ?>
			</textarea>
		</div>
	</div>
</body>
</html>

<?php

exit();

}

/**
 * Manipular erros
 */
set_error_handler(function($errno, $errstr, $errfile, $errline) {

	if (app()->config('debug'))
	{
		// Mostrar erro ao desenvolvedor

		$backtrace = print_r(debug_backtrace(0));

		display_error_page($errno, $errstr, $errfile, $errline, $backtrace);
	}
	else
	{
		// Logar erro para arquivo /logs/error_<ano><mês><dia>

		$filename = '/logs/error_' . date('Ymd');

		$content = sprintf('[%s] %s em %s na linha %d; erro número %d', 
			date('H:i:s'), $errstr, $errfile, $errline, $errno);

		file_put_contents($filename, $content, FILE_APPEND);
	}
	
	return true;
});


/**
 * Manipular exceções
 */
set_exception_handler(function($exception) {

	if (app()->config('debug'))
	{
		// Mostrar erro ao desenvolvedor

		$backtrace = print_r(debug_backtrace(0), true);

		display_error_page($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $backtrace);
	}
	else
	{
		// Mostrar página de erro ao usuário
		include __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '500.php';
	}
	
	return true;
});

?>