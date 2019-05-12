<?php
	use fruithost\Database;
	use fruithost\Encryption;
	use PHPMailer\PHPMailer;
	
	if(!empty($token)) {
		$found	= null;
		$result = Database::fetch('SELECT `id`, `username`, `email`, `lost_token`, UPPER(SHA2(CONCAT(`id`, :salt, `email`), 512)) AS `crypted_mail` FROM `fh_users` WHERE `lost_enabled`=\'YES\' AND `lost_time` > DATE_SUB(NOW(), INTERVAL 24 HOUR)', [
			'salt'	=> RESET_PASSWORD_SALT
		]);
		
		foreach($result AS $entry) {
			if(Encryption::decrypt($token, $entry->crypted_mail) === $entry->lost_token) {
				$found = $entry;
				break;
			}
		}
		
		if(empty($found)) {
			$template->assign('error', 'The given Token is invalid or was expired.');
		} else {
			if(isset($_POST['action']) && $_POST['action'] == 'lost-password') {
				if(!isset($_POST['password_new'])) {
					$template->assign('error', 'Please enter a new password.');
					$template->assign('changeable', true);
					return;
				}
				
				if(!isset($_POST['password_repeated'])) {
					$template->assign('error', 'Please enter a new password.');
					$template->assign('changeable', true);
					return;
				}
				
				if(strlen($_POST['password_new']) < 8) {
					$template->assign('error', 'The new password must contain the minimum number of 8 characters.');
					$template->assign('changeable', true);
					return;
				}
				
				if($_POST['password_new'] !== $_POST['password_repeated']) {
					$template->assign('error', 'Your password and confirmation password do not match.');
					$template->assign('changeable', true);
					return;
				}
				
				Database::update('fh_users', 'id', [
					'id'			=> $found->id,
					'password'		=> strtoupper(hash('sha512', sprintf('%s%s%s', $found->id, MYSQL_PASSWORTD_SALT, $_POST['password_new']))),
					'lost_enabled'	=> 'NO',
					'lost_token'	=> NULL
				]);
				
				$template->assign('success', 'Your password has been restored.');
				$template->assign('changeable', true);
				return;
			}
			
			$template->assign('changeable', true);
		}
	} else if(isset($_POST['action']) && $_POST['action'] == 'lost-password') {
		$result = Database::single('SELECT `id`, `username`, `email`, UPPER(SHA2(CONCAT(`id`, :salt, `email`), 512)) AS `crypted_mail` FROM `fh_users` WHERE `email`=:email LIMIT 1', [
			'email'	=> $_POST['email'],
			'salt'	=> RESET_PASSWORD_SALT
		]);
		
		if($result == false) {
			$template->assign('error', 'Please enter an valid and registred E-Mail address!');
		} else {
			$token_raw		= strtoupper(bin2hex(openssl_random_pseudo_bytes(32)));
			$hash			= Encryption::encrypt($token_raw, $result->crypted_mail);
			$recovery_link	= $template->url('/lost-password/' . $hash);
			
			Database::update('fh_users', 'id', [
				'id'			=> $result->id,
				'lost_enabled'	=> 'YES',
				'lost_time'		=> date('Y-m-d H:i:s', time()),
				'lost_token'	=> $token_raw
			]);
			
			$mail			= new PHPMailer;
			$mail->isSendmail();
			$mail->CharSet	= 'utf-8';
			$mail->Subject	= 'Reset your Password';
			$mail->setFrom('no-reply@fruithost.de', 'fruithost');
			$mail->addAddress($result->email);
			
			// @ToDo override E-Mail templates if an Theme as set!!!!
			
			$html_file = sprintf('%sdefault/email/lost-password.html', PATH);
			$text_file = sprintf('%sdefault/email/lost-password.txt', PATH);
			
			if(file_exists($text_file)) {
				$text = str_replace([
					'$USERNAME',
					'$LINK'
				], [
					$result->username,
					$recovery_link
				], file_get_contents($text_file));
			}
			
			if(file_exists($html_file)) {
				$mail->msgHTML(str_replace([
					'$USERNAME',
					'$LINK'
				], [
					$result->username,
					$recovery_link
				], file_get_contents($html_file)), __DIR__);
				$mail->AltBody	= $text;
			} else {
				$mail->Body		= $text;
			}
			
			if(!$mail->send()) {
				$template->assign('error', $mail->ErrorInfo);
			} else {
				$template->assign('success', 'Your request has been sent to your E-Mail adress!');
			}
		}
	}
	
	$template->assign('email', (isset($_POST['email']) ? $_POST['email'] : ''));
?>