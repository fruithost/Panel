<?php
	use fruithost\Auth;
	use fruithost\Response;
	use fruithost\Session;
	use PHPMailer\PHPMailer;
	use fruithost\I18N;
	use fruithost\Database;
	
	if(isset($_POST['action']) && $_POST['action'] == 'login') {
		try {
			if(isset($_POST['code'])) {
				if(Session::get('two_factor') === intval(trim($_POST['code']))) {
					$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:user_id LIMIT 1', [
						'user_id'	=> Session::get('two_id')
					]);
					
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
					$template->assign('error', I18N::get('Your 2FA code is invalid!'));
				}
			} else if(Auth::getSettings('2FA_ENABLED', $_POST['username'], 'false') === 'true' && Auth::TwoFactorLogin($_POST['username'], $_POST['password'])) {
				$text	= '';
				$code	= rand(100000, 999999);
				$result = Database::single('SELECT *, \'[*** PROTECTED ***]\' AS `password` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
					'username'	=> $_POST['username']
				]);
				
				Session::set('two_factor',	$code);
				Session::set('two_id',		(int) $result->id);
				
				$mail			= new PHPMailer;
				
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