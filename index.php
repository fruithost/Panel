<?php
	if(is_readable('.DEBUG') || is_readable('../.DEBUG')) {
		define('DEBUG', true);
	}
	
	if(defined('DEBUG') && DEBUG) {
		@ini_set('display_errors', true);
		error_reporting(E_ALL);
	}
	
	if(!defined('PATH')) {
		define('PATH', sprintf('%s/', dirname(__FILE__)));
	}
	
	define('BS', '\\');
	define('DS', DIRECTORY_SEPARATOR);
	
	require_once(sprintf('%s/classes/Core.class.php', PATH));
	
	new \fruithost\Core();
?>