<?php
	use fruithost\Auth;
	use fruithost\Database;
	
	if(isset($_POST['action']) && $_POST['action'] === 'save') {
		switch($tab) {
			case 'password':
				if(!isset($_POST['password_current']) || empty($_POST['password_current'])) {
					$template->assign('error', 'Please enter your current password.');
					return;
				}
				
				if(!isset($_POST['password_new']) || empty($_POST['password_new'])) {
					$template->assign('error', 'Please enter your new password.');
					return;
				}
				
				if(!isset($_POST['password_repeated']) || empty($_POST['password_repeated'])) {
					$template->assign('error', 'Please repeat your new password.');
					return;
				}
				
				$result = Database::single('SELECT `password` FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				]);
				
				if(strtoupper(hash('sha512', sprintf('%s%s%s', Auth::getID(), MYSQL_PASSWORTD_SALT, $_POST['password_current']))) !== $result->password) {
					$template->assign('error', 'Your current password is not correct!');
					return;
				}
				
				if(strlen($_POST['password_new']) < 8) {
					$template->assign('error', 'The new password must contain the minimum number of 8 characters.');
					return;
				}
				
				if($_POST['password_new'] !== $_POST['password_repeated']) {
					$template->assign('error', 'Your password and confirmation password do not match.');
					return;
				}
				
				Database::update(DATABASE_PREFIX . 'users', 'id', [
					'id'			=> Auth::getID(),
					'password'		=> strtoupper(hash('sha512', sprintf('%s%s%s', Auth::getID(), MYSQL_PASSWORTD_SALT, $_POST['password_new']))),
					'lost_enabled'	=> 'NO',
					'lost_token'	=> NULL
				]);
				
				$template->assign('success', 'Your password has been updated.');
			break;
			default:
				if(!isset($_POST['email']) || empty($_POST['email'])) {
					$template->assign('error', 'Please enter your E-Mail address.');
					return;
				}
				
				if(!isset($_POST['name_first']) || empty($_POST['name_first'])) {
					$template->assign('error', 'Please enter your first name.');
					return;
				}
				
				if(!isset($_POST['name_last']) || empty($_POST['name_last'])) {
					$template->assign('error', 'Please enter your last name.');
					return;
				}
				
				if(!isset($_POST['phone']) || empty($_POST['phone'])) {
					$template->assign('error', 'Please enter your phone  number.');
					return;
				}
				
				if(!isset($_POST['address']) || empty($_POST['address'])) {
					$template->assign('error', 'Please enter your address.');
					return;
				}
				
				if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				])) {
					Database::update(DATABASE_PREFIX . 'users_data', 'user_id', [
						'user_id'		=> Auth::getID(),
						'phone_number'	=> $_POST['phone'],
						'address'		=> $_POST['address'],
						'name_first'	=> $_POST['name_first'],
						'name_last'		=> $_POST['name_last']
					]);
				} else {
					Database::insert(DATABASE_PREFIX . 'users_data', [
						'id'			=> NULL,
						'user_id'		=> Auth::getID(),
						'phone_number'	=> $_POST['phone'],
						'address'		=> $_POST['address'],
						'name_first'	=> $_POST['name_first'],
						'name_last'		=> $_POST['name_last']
					]);
				}
				
				$template->assign('data', Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				]));
				
				$template->assign('success', 'Your personal data has been updated.');
			break;
		}
	}
?>