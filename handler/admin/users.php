<?php
	use fruithost\Database;
	use fruithost\I18N;
	
	$users = Database::fetch('SELECT *, \'**********\' AS `password` FROM `' . DATABASE_PREFIX . 'users`');
	
	
	if(isset($_POST['action'])) {
		print_r($_POST['action']);
		
		switch($_POST['action']) {
			case 'save':
				/* Save user */
				$this->assign('success', 'User was successfully updated.');
			break;
			case 'delete':
				/* delete user */
				$this->assign('success', 'User was successfully deleted!');
			break;
			case 'create':
				/* create user */
				$this->assign('success', 'User was successfully created!');
			break;
			case 'deletes':
				$messages	= [];
				
				if(isset($_POST['user'])) {	
					$messages[] = sprintf(I18N::get('<strong>%d Users</strong> was successfully deleted'), count($_POST['user']));
				
					$users = Database::fetch('SELECT *, \'**********\' AS `password` FROM `' . DATABASE_PREFIX . 'users`');
				}
				
				if(count($messages) > 0) {
					$this->assign('success', implode(' and ', $messages));
				} else {
					$this->assign('error', I18N::get('Please select some entries you want to delete.'));
				}
			break;
		}
	}
	
	if(is_numeric($tab) && $tab > 0) {
		
	} else if($tab == 'create') {
		
	}
	
	$template->assign('users', $users);
?>