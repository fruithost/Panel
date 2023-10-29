<?php
	use fruithost\Database;
	use fruithost\Auth;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'reboot':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', 'You have no permissions for this action!');
					return;
				}
				
				$this->assign('success', 'The System will be rebooted now!');
					
				// @ToDo save in DB, execute by daemon!
				shell_exec('/sbin/shutdown -r now');
			break;
		}
	}
?>