<?php
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	use fruithost\Storage\Database;
	
	if(isset($_POST['action']) && $_POST['action'] === 'save') {
		switch($tab) {
			case 'security':
				$selected = null;
				
				foreach($template->getCore()->getHooks()->applyFilter('2FA_METHODS', [
					(object) [
						'id'		=> 'mail',
						'name'		=> I18N::get('E-Mail'),
						'enabled'	=> true
					]
				]) AS $index => $method) {
					if($method->id == $_POST['2fa_type']) {
						$selected = $method;
						break;
					}
				}
			
				if($selected == null) {
					$template->assign('error', I18N::get('The selected 2FA method is not known.'));
					return;
				}
			
				if(!$selected->enabled) {
					$template->assign('error', sprintf(I18N::get('The 2FA method "%s" is currently deactivated and can therefore not be used.'), $selected->name));
					return;
				}
				
				if(isset($_POST['2fa_enabled'])) {
					Auth::setSettings('2FA_ENABLED', NULL, 'true');
				} else {
					Auth::setSettings('2FA_ENABLED', NULL, 'false');	
				}
				
				Auth::setSettings('2FA_TYPE', NULL, $_POST['2fa_type']);
				
				$template->getCore()->getHooks()->runAction('SAVE_ACCOUNT_SETTINGS_SECURITY', $_POST);
				$template->assign('success', I18N::get('Your security settings has been updated.'));
			break;
			default:
				Auth::setSettings('LANGUAGE', NULL, $_POST['language']);
				Auth::setSettings('TIME_FORMAT', NULL, $_POST['time_format']);
				Auth::setSettings('TIME_ZONE', NULL, $_POST['time_zone']);
				
				$template->getCore()->getHooks()->runAction('SAVE_ACCOUNT_SETTINGS_GLOBAL', $_POST);
				$template->assign('success', I18N::get('Your settings has been updated.'));
				I18N::reload();
			break;
		}
	}
?>