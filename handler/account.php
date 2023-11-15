<?php

use fruithost\Accounting\Auth;
use fruithost\Localization\I18N;
use fruithost\Security\Encryption;
use fruithost\Storage\Database;

if(isset($_POST['action']) && $_POST['action'] === 'save') {
		switch($tab) {
			case 'password':
				if(empty($_POST['password_current'])) {
					$template->assign('error', I18N::get('Please enter your current password.'));
					return;
				}
				
				if(empty($_POST['password_new'])) {
					$template->assign('error', I18N::get('Please enter your new password.'));
					return;
				}
				
				if(empty($_POST['password_repeated'])) {
					$template->assign('error', I18N::get('Please repeat your new password.'));
					return;
				}
				
				$result = Database::single('SELECT `password` FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				]);
				
				if(strtoupper(hash('sha512', sprintf('%s%s%s', Auth::getID(), MYSQL_PASSWORTD_SALT, $_POST['password_current']))) !== $result->password) {
					$template->assign('error', I18N::get('Your current password is not correct!'));
					return;
				}
				
				if(strlen($_POST['password_new']) < 8) {
					$template->assign('error', I18N::get('The new password must contain the minimum number of 8 characters.'));
					return;
				}
				
				if($_POST['password_new'] !== $_POST['password_repeated']) {
					$template->assign('error', I18N::get('Your password and confirmation password do not match.'));
					return;
				}
				
				Database::update(DATABASE_PREFIX . 'users', 'id', [
					'id'			=> Auth::getID(),
					'password'		=> strtoupper(hash('sha512', sprintf('%s%s%s', Auth::getID(), MYSQL_PASSWORTD_SALT, $_POST['password_new']))),
					'lost_enabled'	=> 'NO',
					'lost_token'	=> NULL
				]);
				
				$template->assign('success', I18N::get('Your password has been updated.'));
			break;
			default:
				if(empty($_POST['email'])) {
					$template->assign('error', I18N::get('Please enter your E-Mail address.'));
					return;
				}
				
				if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
					$template->assign('error', I18N::get('Please enter a valid E-Mail address.'));
					return;
				}
				
				if(empty($_POST['name_first'])) {
					$template->assign('error', I18N::get('Please enter your first name.'));
					return;
				}
				
				if(empty($_POST['name_last'])) {
					$template->assign('error', I18N::get('Please enter your last name.'));
					return;
				}
				
				if(empty($_POST['phone'])) {
					$template->assign('error', I18N::get('Please enter your phone  number.'));
					return;
				}
				
				if(empty($_POST['address'])) {
					$template->assign('error', I18N::get('Please enter your address.'));
					return;
				}
				
				if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				])) {
					Database::update(DATABASE_PREFIX . 'users_data', 'user_id', [
						'user_id'		=> Auth::getID(),
						'phone_number'	=> Encryption::encrypt($_POST['phone'], ENCRYPTION_SALT),
						'address'		=> Encryption::encrypt($_POST['address'], ENCRYPTION_SALT),
						'name_first'	=> Encryption::encrypt($_POST['name_first'], ENCRYPTION_SALT),
						'name_last'		=> Encryption::encrypt($_POST['name_last'], ENCRYPTION_SALT)
					]);
				} else {
					Database::insert(DATABASE_PREFIX . 'users_data', [
						'id'			=> NULL,
						'user_id'		=> Auth::getID(),
						'phone_number'	=> Encryption::encrypt($_POST['phone'], ENCRYPTION_SALT),
						'address'		=> Encryption::encrypt($_POST['address'], ENCRYPTION_SALT),
						'name_first'	=> Encryption::encrypt($_POST['name_first'], ENCRYPTION_SALT),
						'name_last'		=> Encryption::encrypt($_POST['name_last'], ENCRYPTION_SALT)
					]);
				}
				
				$data = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				]);
				
				Database::update(DATABASE_PREFIX . 'users', 'id', [
					'id'			=> Auth::getID(),
					'email'			=> $_POST['email']
				]);
				
				foreach($data AS $index => $entry) {
					if(in_array($index, [ 'id', 'user_id' ])) {
						continue;
					}
					
					$data->{$index} = Encryption::decrypt($entry, ENCRYPTION_SALT);
				}
				
				$template->assign('data', $data);
				
				$template->assign('success', I18N::get('Your personal data has been updated.'));
			break;
		}
	}
?>