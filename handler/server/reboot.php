<?php
	
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'reboot':
				if(!Auth::hasPermission('SERVER::REBOOT')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					
					return;
				}
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					
					return;
				}
				$this->assign('success', I18N::get('The System will be rebooted now!'));
				$template->getCore()->setSettings('REBOOT', date('Y-m-d H:i:s', time()));
				$template->getCore()->getHooks()->runAction('SERVER_REBOOT', time());
				break;
		}
	}
	$rebooting = $template->getCore()->getSettings('REBOOT', null);
	if(!empty($rebooting)) {
		$this->assign('success', I18N::get('The System will be rebooted now!').sprintf('<p><small>%s</small></p>', $rebooting));
	}
?>