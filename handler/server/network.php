<?php
	use fruithost\Hardware\NetworkInterfaces;
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;

	$template->assign('network',			NetworkInterfaces::get());

	switch($action) {
		case 'start':
			if(!Auth::hasPermission('SERVER::MANAGE')) {
				$this->assign('error', I18N::get('You have no permissions for this action!'));
				return;
			}

			if(defined('DEMO') && DEMO) {
				$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
				return;
			}

			$device = NetworkInterfaces::getDevice($tab);

			if(empty($device)) {
				$this->assign('error', sprintf(I18N::get('Unknown Network-Interface: %s'), $tab));
				return;
			}

			$this->assign('success', sprintf(I18N::get('The Network-Interface "%s" will be started!'), $tab));
			$device->enable();
		break;
		case 'stop':
			if(!Auth::hasPermission('SERVER::MANAGE')) {
				$this->assign('error', I18N::get('You have no permissions for this action!'));
				return;
			}

			if(defined('DEMO') && DEMO) {
				$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
				return;
			}

			$device = NetworkInterfaces::getDevice($tab);

			if(empty($device)) {
				$this->assign('error', sprintf(I18N::get('Unknown Network-Interface: %s'), $tab));
				return;
			}

			$this->assign('success', sprintf(I18N::get('The Network-Interface "%s" will be stopped!'), $tab));
			$device->disable();
		break;
		case 'info':
			$device = NetworkInterfaces::getDevice($tab);

			if(empty($device)) {
				$this->assign('error', sprintf(I18N::get('Unknown Network-Interface: %s'), $tab));
				return;
			}

			$this->assign('device', $device);
		break;
	}
?>