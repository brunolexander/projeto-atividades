<?php

/**
 * Autoload de classes
 */

spl_autoload_register(function($class) {
	$separator = DIRECTORY_SEPARATOR;
	$filename = __DIR__ . $separator . '..' . $separator . str_replace('\\', $separator, $class) . '.php';

	if (is_readable($filename))
	{
		include $filename;
	}
});


?>