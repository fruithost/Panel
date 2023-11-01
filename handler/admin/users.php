<?php
	use fruithost\Database;
	use fruithost\I18N;
	
	$users = Database::fetch('SELECT *, \'**********\' AS `password` FROM `' . DATABASE_PREFIX . 'users`');
	
	// @ToDO?
	
	$template->assign('users', $users);
?>