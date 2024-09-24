<?php
	/**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'save':
				if(!Auth::hasPermission('SERVER::SETTINGS')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					return;
				}

				$template->getCore()->setSettings('PROJECT_NAME', $_POST['project_name']);
				$template->getCore()->setSettings('PROJECT_COPYRIGHT', isset($_POST['project_copyright']) && $_POST['project_copyright'] == 'true');
				$template->getCore()->setSettings('LANGUAGE', $_POST['language']);
				$template->getCore()->setSettings('TIME_FORMAT', $_POST['time_format']);
				$template->getCore()->setSettings('TIME_ZONE', $_POST['time_zone']);
				$template->getCore()->getHooks()->runAction('SAVE_SERVER_SETTINGS', $_POST);
				$this->assign('success', I18N::get('The system settings has been saved.'));
			break;
		}
	}
?>