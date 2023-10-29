<?php
	use fruithost\Database;
	use fruithost\Auth;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'command':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', 'You have no permissions for this action!');
					return;
				}
				
				$result = shell_exec($_POST['command'] . ' 2>&1');
				print 'fruithost@localhost:~# ';
				
				if($result == null) {
					print 'Error';
				} else if($result == false) {
					print 'Pipe Error';
				} else {
					print $result;
				}
				exit();
			break;
		}
	}
?>