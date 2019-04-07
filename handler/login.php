<?php
	use fruithost\Auth;
	use fruithost\Response;
	
	if(isset($_POST['action']) && $_POST['action'] == 'login') {
		try {
			if(Auth::login($_POST['username'], $_POST['password'])) {
				Response::redirect('/overview');
			} else {
				$template->assign('error', 'Login war fehlerhaft!');
			}
		} catch(\PDOException $e) {
			$template->assign('error', 'Can\'t connect to database.');
		} catch(\Exception $e) {
			$template->assign('error', $e->getMessage());
		}
	}
	
	$template->assign('username', (isset($_POST['username']) ? $_POST['username'] : ''));
	$template->assign('remember', isset($_POST['remember']));
?>