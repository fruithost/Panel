<?php
	use fruithost\Database;
	use fruithost\I18N;
	
	$users = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'users`');
	$template->assign('users', $users);
?>