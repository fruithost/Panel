<?php
    use fruithost\Accounting\Auth;
    use fruithost\Accounting\Session;
    use fruithost\Network\Response;
    use fruithost\Localization\I18N;
    use fruithost\Storage\Database;
    use PHPMailer\PHPMailer;

    if(isset($_POST['action']) && $_POST['action'] == 'login') {
		try {
			if(isset($_POST['code'])) {
				$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:user_id LIMIT 1', [
					'user_id'	=> Session::get('two_id')
				]);
				
				if($result == null) {
					$template->assign('error', I18N::get('Session Error'));
					return;
				}
				
				$type		= Auth::getSettings('2FA_TYPE', $result->username, 'unknown');
				$selected	= null;
				
				foreach($template->getCore()->getHooks()->applyFilter('2FA_METHODS', [
					(object) [
						'id'		=> 'mail',
						'name'		=> I18N::get('E-Mail'),
						'enabled'	=> true
					]
				], false) AS $index => $method) {
					if($method->id == $type) {
						$selected = $method;
						break;
					}
				}
				
				if($selected == null) {
					$template->assign('two_factor_unknown', true);
					$template->assign('error', I18N::get('The selected 2FA method is not known.'));
					return;
				}
			
				if(!$selected->enabled) {
					$template->assign('two_factor_unknown', true);
					$template->assign('error', sprintf(I18N::get('The 2FA method "%s" is currently deactivated and can therefore not be used.'), $selected->name));
					return;
				}
				
				if($selected->id == 'mail' && Session::get('two_factor') === intval(trim($_POST['code']))) {
					Session::remove('two_factor');
					Session::remove('two_id');
					
					if(empty($result)) {
						$template->assign('error', I18N::get('Your 2FA code is invalid!'));
					} else {
						Session::set('user_name',	$result->username);
						Session::set('user_id',		(int) $result->id);
						Response::redirect('/overview');
					}
				} else {
					Session::remove('two_factor');
					Session::remove('two_id');
					
					if(!$template->getCore()->getHooks()->runAction('2FA_LOGIN-' . $selected->id, (object) [
						'code' 		=> $_POST['code'],
						'template'	=> $template,
						'user_id'	=> (int) $result->id
					], false)) {
						$template->assign('error', I18N::get('Your 2FA code can\'t be handled!'));
					}
				}
			} else if(Auth::getSettings('2FA_ENABLED', $_POST['username'], 'false') === 'true' && Auth::TwoFactorLogin($_POST['username'], $_POST['password'])) {
				$result = Database::single('SELECT *, \'[*** PROTECTED ***]\' AS `password` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
					'username'	=> $_POST['username']
				]);
				Session::set('two_id',		(int) $result->id);
				
				$type		= Auth::getSettings('2FA_TYPE', $result->username, 'unknown');
				$selected	= null;
				
				foreach($template->getCore()->getHooks()->applyFilter('2FA_METHODS', [
					(object) [
						'id'		=> 'mail',
						'name'		=> I18N::get('E-Mail'),
						'enabled'	=> true
					]
				], false) AS $index => $method) {
					if($method->id == $type) {
						$selected = $method;
						break;
					}
				}
				
				if($selected == null) {
					$template->assign('two_factor_unknown', true);
					$template->assign('error', I18N::get('The selected 2FA method is not known.'));
					return;
				}
			
				if(!$selected->enabled) {
					$template->assign('two_factor_unknown', true);
					$template->assign('error', sprintf(I18N::get('The 2FA method "%s" is currently deactivated and can therefore not be used.'), $selected->name));
					return;
				}
				
				if($selected->id == 'mail') {
					$text	= '';
					$code	= rand(100000, 999999);
					$mail	= new PHPMailer;
					Session::set('two_factor',	$code);
					
					if(defined('MAIL_EXTERNAL') && MAIL_EXTERNAL) {
						$mail->Host       = MAIL_HOSTNAME;
						$mail->Port       = MAIL_PORT;
						$mail->SMTPAuth   = true;
						$mail->Username   = MAIL_USERNAME;
						$mail->Password   = MAIL_PASSWORD;
						$mail->isSMTP();
					} else {
						$mail->isSendmail();
					}
					
					$mail->CharSet	= 'utf-8';
					$mail->Subject	= '2FA - Code';
					$mail->setFrom('no-reply@fruithost.de', 'fruithost');
					$mail->addAddress($result->email);
					
					// @ToDo override E-Mail templates if an Theme as set!!!!
					
					$html_file = sprintf('%sdefault/email/2fa-code.html', PATH);
					$text_file = sprintf('%sdefault/email/2fa-code.txt', PATH);
					
					if(file_exists($text_file)) {
						$text = str_replace([
							'$USERNAME',
							'$CODE'
						], [
							$result->username,
							$code
						], file_get_contents($text_file));
					}
					
					if(file_exists($html_file)) {
						$mail->msgHTML(str_replace([
							'$USERNAME',
							'$CODE'
						], [
							$result->username,
							$code
						], file_get_contents($html_file)), __DIR__);
						$mail->AltBody	= $text;
					} else {
						$mail->Body		= $text;
					}
				
					if(!$mail->send()) {
						$template->assign('error', $mail->ErrorInfo);
					} else {
						$template->assign('error', I18N::get('Please complete the two factor authentication:'));
					}
				}

				$template->assign('two_factor', true);				
			} else if(Auth::login($_POST['username'], $_POST['password'])) {
				Response::redirect('/overview');
			} else {
				$template->assign('error', I18N::get('Login was unsuccessful!'));
			}
		} catch(\PDOException $e) {
			$template->assign('error', 'Can\'t connect to database.');
		} catch(\Exception $e) {
			$template->assign('error', $e->getMessage());
		}
	}
	
	$template->assign('username', (isset($_POST['username']) ? $_POST['username'] : ''));
?>