<?php
	use fruithost\Database;
	use fruithost\Response;
	use fruithost\Auth;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'command':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', 'You have no permissions for this action!');
					return;
				}
				
				Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
				
				$result = shell_exec('export TERM=xterm-256color;' . $_POST['command'] . ' 2>&1');
				print 'fruithost@localhost:~# ';
				
				if($result == null) {
					print 'Error';
				} else if($result == false) {
					print 'Pipe Error';
				} else {
					$result = preg_replace("/\t/", " ", $result);
					print nl2br($result);
				}
				exit();
			break;
		}
	}
?>