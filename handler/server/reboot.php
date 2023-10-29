<?php
	use fruithost\Database;
	use fruithost\Auth;
	use fruithost\I18N;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'reboot':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					return;
				}
				
				$this->assign('success', I18N::get('The System will be rebooted now!'));
					
				// @ToDo save in DB, execute by daemon!
				shell_exec('/sbin/shutdown -r now');
			break;
		}
	}
?>