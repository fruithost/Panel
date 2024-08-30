<?php
	
	use fruithost\Accounting\Auth;
	use fruithost\Accounting\Session;
	use fruithost\Accounting\User;
	use fruithost\Localization\I18N;
	use fruithost\Network\Response;
	use fruithost\Security\Encryption;
	use fruithost\Storage\Database;
	use PHPMailer\PHPMailer;
	
	$users = Database::fetch('SELECT *, \'[*** PROTECTED ***]\' AS `password` FROM `'.DATABASE_PREFIX.'users` WHERE `deleted`=\'NO\' ORDER BY `id`');
	if(isset($_POST['action'])) {
		$user = new User();
		if(is_numeric($tab) && $tab > 0) {
			$user->fetch($tab);
		}
		switch($_POST['action']) {
			/* Save user */
			case 'save':
				switch($action) {
					case 'settings':
						if(!Auth::hasPermission('USERS::EDIT')) {
							$this->assign('error', I18N::get('You have no permissions for this action!'));
						} else {
							if(isset($_POST['language'])) {
								$user->setSettings('LANGUAGE', null, $_POST['language']);
							}
							if(isset($_POST['time_format'])) {
								$user->setSettings('TIME_FORMAT', null, $_POST['time_format']);
							}
							if(isset($_POST['time_format'])) {
								$user->setSettings('TIME_ZONE', null, $_POST['time_zone']);
							}
							$data         = $_POST;
							$data['user'] = $user;
							$template->getCore()->getHooks()->runAction('SAVE_ACCOUNT_SETTINGS_GLOBAL', $data);
							$this->assign('success', sprintf(I18N::get('Settings for <strong>%s</strong> was successfully updated.'), $user->getUsername()));
							I18N::reload();
						}
						break;
					case 'password':
						if(!Auth::hasPermission('USERS::EDIT')) {
							$this->assign('error', I18N::get('You have no permissions for this action!'));
						} else {
							if(!empty($_POST['password_new']) && $_POST['password_new'] !== $_POST['password_repeated']) {
								$template->assign('error', I18N::get('Please repeat your new password.'));
							} else if(!empty($_POST['password_new']) && strlen($_POST['password_new']) < 8) {
								$template->assign('error', I18N::get('The new password must contain the minimum number of 8 characters.'));
							} else if(!empty($_POST['password_new'])) {
								Database::update(DATABASE_PREFIX.'users', 'id', [
									'id'           => $user->getID(),
									'password'     => strtoupper(hash('sha512', sprintf('%s%s%s', $user->getID(), MYSQL_PASSWORTD_SALT, $_POST['password_new']))),
									'lost_enabled' => 'NO',
									'lost_token'   => null
								]);
							}
							if(isset($_POST['2fa_enabled'])) {
								$user->setSettings('2FA_ENABLED', null, 'true');
							} else {
								$user->setSettings('2FA_ENABLED', null, 'false');
							}
							if(isset($_POST['2fa_type'])) {
								$user->setSettings('2FA_TYPE', null, $_POST['2fa_type']);
							}
							if(isset($_POST['password_recovery']) && count($_POST['password_recovery']) == 1) {
								if(!filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
									$template->assign('error', I18N::get('Can\'t send recovery instructions, because the user has an <strong>invalid E-Mail address</strong>.'));
								} else {
									$token_raw     = strtoupper(bin2hex(openssl_random_pseudo_bytes(32)));
									$hash          = Encryption::encrypt($token_raw, $user->getCryptedMail());
									$recovery_link = $template->url('/lost-password/'.$hash);
									$password      = strtoupper(bin2hex(openssl_random_pseudo_bytes(8)));
									if(array_key_exists('generate', $_POST['password_recovery'])) {
										Database::update(DATABASE_PREFIX.'users', 'id', [
											'id'           => $user->getID(),
											'password'     => strtoupper(hash('sha512', sprintf('%s%s%s', $user->getID(), MYSQL_PASSWORTD_SALT, $password))),
											'lost_enabled' => 'NO',
											'lost_token'   => null
										]);
									} else {
										Database::update(DATABASE_PREFIX.'users', 'id', [
											'id'           => $user->getID(),
											'lost_enabled' => 'YES',
											'lost_time'    => date('Y-m-d H:i:s', time()),
											'lost_token'   => $token_raw
										]);
									}
									$mail = new PHPMailer;
									if(defined('MAIL_EXTERNAL') && MAIL_EXTERNAL) {
										$mail->Host     = MAIL_HOSTNAME;
										$mail->Port     = MAIL_PORT;
										$mail->SMTPAuth = true;
										$mail->Username = MAIL_USERNAME;
										$mail->Password = MAIL_PASSWORD;
										$mail->isSMTP();
									} else {
										$mail->isSendmail();
									}
									$mail->CharSet = 'utf-8';
									if(array_key_exists('generate', $_POST['password_recovery'])) {
										$mail->Subject = I18N::get('New password for your account');
									} else {
										$mail->Subject = I18N::get('Reset your Password');
									}
									$mail->setFrom('no-reply@fruithost.de', 'fruithost');
									$mail->addAddress($user->getMail());
									// @ToDo override E-Mail templates if an Theme as set!!!!
									if(array_key_exists('generate', $_POST['password_recovery'])) {
										$html_file = sprintf('%sdefault/email/new-password.html', PATH);
										$text_file = sprintf('%sdefault/email/new-password.txt', PATH);
									} else {
										$html_file = sprintf('%sdefault/email/lost-password.html', PATH);
										$text_file = sprintf('%sdefault/email/lost-password.txt', PATH);
									}
									if(file_exists($text_file)) {
										$text = str_replace([
											'$USERNAME',
											'$LINK',
											'$PASSWORD'
										], [
											$user->getUsername(),
											$recovery_link,
											$password
										], file_get_contents($text_file));
									}
									if(file_exists($html_file)) {
										$mail->msgHTML(str_replace([
											'$USERNAME',
											'$LINK',
											'$PASSWORD'
										], [
											$user->getUsername(),
											$recovery_link,
											$password
										], file_get_contents($html_file)), __DIR__);
										$mail->AltBody = $text;
									} else {
										$mail->Body = $text;
									}
									if(!$mail->send()) {
										$template->assign('error', $mail->ErrorInfo);
									} else {
										if(array_key_exists('generate', $_POST['password_recovery'])) {
											$template->assign('success', I18N::get('The new generated password has been sent to users E-Mail address!'));
										} else {
											$template->assign('success', I18N::get('Your password request has been sent to users E-Mail address!'));
										}
									}
								}
							}
							$template->assign('success', sprintf(I18N::get('Security-Settings for <strong>%s</strong> was successfully updated.'), $user->getUsername()));
						}
						break;
					case 'permissions':
						if(!Auth::hasPermission('USERS::PERMISSIONS')) {
							$this->assign('error', I18N::get('You have no permissions for this action!'));
						} else if($user->getID() == 1) {
							$this->assign('error', I18N::get('You can\'t edit the rights of the super-user!'));
						} else if($user->getID() == Auth::getID()) {
							$this->assign('error', I18N::get('You can\'t edit your own rights!'));
						} else {
							// Delete All Permissions
							Database::delete(DATABASE_PREFIX.'users_permissions', [
								'user_id' => $user->getID()
							]);
							if(isset($_POST['permissions_global']) && $_POST['permissions_global'] === 'true') {
								if(!Database::exists('SELECT `id` FROM `'.DATABASE_PREFIX.'users_permissions` WHERE `user_id`=:user_id AND `permission`=:permission LIMIT 1', [
									'user_id'    => $user->getID(),
									'permission' => '*'
								])) {
									Database::insert(DATABASE_PREFIX.'users_permissions', [
										'id'         => null,
										'user_id'    => $user->getID(),
										'permission' => '*'
									]);
								}
							} else {
								if(Database::exists('SELECT `id` FROM `'.DATABASE_PREFIX.'users_permissions` WHERE `user_id`=:user_id AND `permission`=:permission LIMIT 1', [
									'user_id'    => $user->getID(),
									'permission' => '*'
								])) {
									Database::delete(DATABASE_PREFIX.'users_permissions', [
										'user_id'    => $user->getID(),
										'permission' => '*'
									]);
								}
							}
							// Insert new Permissions
							if(isset($_POST['permissions'])) {
								foreach($_POST['permissions'] as $name => $value) {
									Database::insert(DATABASE_PREFIX.'users_permissions', [
										'id'         => null,
										'user_id'    => $user->getID(),
										'permission' => $name
									]);
								}
							}
							$template->assign('success', sprintf(I18N::get('The user\'s permissions for the user <strong>%s</strong> have been adjusted.'), $user->getUsername()));
						}
						break;
					default:
						if(!Auth::hasPermission('USERS::EDIT')) {
							$this->assign('error', I18N::get('You have no permissions for this action!'));
						} else {
							if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
								$template->assign('error', I18N::get('Please enter a valid E-Mail address.'));
							}
							Database::update(DATABASE_PREFIX.'users_data', 'user_id', [
								'user_id'      => $user->getID(),
								'phone_number' => Encryption::encrypt($_POST['phone'], ENCRYPTION_SALT),
								'address'      => Encryption::encrypt($_POST['address'], ENCRYPTION_SALT),
								'name_first'   => Encryption::encrypt($_POST['name_first'], ENCRYPTION_SALT),
								'name_last'    => Encryption::encrypt($_POST['name_last'], ENCRYPTION_SALT)
							]);
							Database::update(DATABASE_PREFIX.'users', 'id', [
								'id'    => $user->getID(),
								'email' => $_POST['email']
							]);
							/* @ToDo rename User, with all hosted files! */
							$template->assign('success', sprintf(I18N::get('User <strong>%s</strong> was successfully edited.'), $user->getUsername()));
						}
						break;
				}
				break;
			/* delete user */
			case 'delete':
				if(!Auth::hasPermission('USERS::DELETE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
				} else if($user->getID() == 1) {
					$template->assign('error', I18N::get('You cant delete the admin account!'));
				} else {
					$user->delete();
					Session::set('success', sprintf(I18N::get('The user <strong>%s</strong> was successfully deleted.'), $user->getUsername()));
					Response::redirect('/admin/users');
					exit();
				}
				break;
			/* Delete users */
			case 'deletes':
				if(!Auth::hasPermission('USERS::DELETE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
				} else {
					$messages = [];
					if(isset($_POST['user'])) {
						foreach($_POST['user'] as $id) {
							$user = new User();
							$user->fetch($id);
							if($user->getID() == 1) {
								$template->assign('error', I18N::get('You cant delete the admin account!'));
							} else {
								$messages[] = sprintf(I18N::get('<strong>%s</strong> (%s)'), $user->getUsername(), $user->getMail());
								$user->delete();
							}
						}
					}
					if(count($messages) > 0) {
						$template->assign('success', sprintf('Following users was successfully deleted:<br />%s', implode(', ', $messages)));
					} else {
						if(!$template->isAssigned('error')) {
							$template->assign('error', I18N::get('Please select some entries you want to delete.'));
						}
					}
					$users = Database::fetch('SELECT *, \'[*** PROTECTED ***]\' AS `password` FROM `'.DATABASE_PREFIX.'users` WHERE `deleted`=\'NO\'');
				}
				break;
			/* create user */
			case 'create':
				if(!Auth::hasPermission('USERS::CREATE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					
					return;
				}
				/* Username */
				if(!isset($_POST['username']) || empty($_POST['username'])) {
					$template->assign('error', I18N::get('Please enter the username.'));
					
					return;
				}
				$template->assign('username', $_POST['username']);
				if(strlen($_POST['username']) < 2) {
					$template->assign('error', I18N::get('The new username must contain the minimum number of 2 characters.'));
					
					return;
				}
				if(Database::exists('SELECT `id` FROM `'.DATABASE_PREFIX.'users` WHERE `username`=:username LIMIT 1', [ 'username' => $_POST['username'] ])) {
					$template->assign('error', I18N::get('The username is already in use.'));
					
					return;
				}
				/* E-Mail */
				if(!isset($_POST['email']) || empty($_POST['email'])) {
					$template->assign('error', I18N::get('Please enter an E-Mail address.'));
					
					return;
				}
				$template->assign('email', $_POST['email']);
				if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
					$template->assign('error', I18N::get('Please enter a valid E-Mail address.'));
					
					return;
				}
				if(Database::exists('SELECT `id` FROM `'.DATABASE_PREFIX.'users` WHERE `email`=:email LIMIT 1', [ 'email' => $_POST['email'] ])) {
					$template->assign('error', I18N::get('The E-Mail address is already in use.'));
					
					return;
				}
				if(!isset($_POST['password_new']) || empty($_POST['password_new'])) {
					$template->assign('error', I18N::get('Please enter the password.'));
					
					return;
				}
				if(!isset($_POST['password_repeated']) || empty($_POST['password_repeated'])) {
					$template->assign('error', I18N::get('Please repeat the password.'));
					
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
				$id = Database::insert(DATABASE_PREFIX.'users', [
					'id'           => null,
					'username'     => $_POST['username'],
					'email'        => $_POST['email'],
					'lost_enabled' => 'NO',
					'lost_token'   => null,
					'lost_time'    => null
				]);
				Database::update(DATABASE_PREFIX.'users', 'id', [
					'id'       => $id,
					'password' => strtoupper(hash('sha512', sprintf('%s%s%s', $id, MYSQL_PASSWORTD_SALT, $_POST['password_new'])))
				]);
				Session::set('success', sprintf(I18N::get('The user <strong>%s</strong> was successfully created.'), $_POST['username']));
				Response::redirect('/admin/users');
				exit();
				break;
		}
	}
	if(!Auth::hasPermission('USERS::*')) {
		$this->assign('error', I18N::get('You have no permissions for this action!'));
		
		return;
	}
	if(!Auth::hasPermission('USERS::EDIT') && is_numeric($tab) && $tab > 0) {
		$this->assign('error', I18N::get('You have no permissions for this action!'));
	} else if(!Auth::hasPermission('USERS::CREATE') && $tab == 'create') {
		$this->assign('error', I18N::get('You have no permissions for this action!'));
	}
	if(Auth::hasPermission('USERS::EDIT') && is_numeric($tab) && $tab > 0) {
		$user = new User();
		$user->fetch($tab);
		$this->assign('user', $user);
		$this->assign('timezones', json_decode(file_get_contents(dirname(PATH).'/config/timezones.json')));
	} else if(Auth::hasPermission('USERS::CREATE') && $tab == 'create') {
		$this->assign('timezones', json_decode(file_get_contents(dirname(PATH).'/config/timezones.json')));
	} else {
		$this->assign('users', $users);
	}
?>