<?php
	use fruithost\Auth;
	use fruithost\Database;
	
	if(isset($_POST['action']) && $_POST['action'] === 'save') {
		switch($tab) {
			case 'security':
				if(isset($_POST['2fa_enabled'])) {
					Auth::setSettings('2FA_ENABLED', NULL, 'true');
				} else {
					Auth::setSettings('2FA_ENABLED', NULL, 'false');	
				}
				
				Auth::setSettings('2FA_TYPE', NULL, $_POST['2fa_type']);
				
				$template->assign('success', 'Your security settings has been updated.');
			break;
			default:
				Auth::setSettings('LANGUAGE', NULL, $_POST['language']);
				Auth::setSettings('TIME_FORMAT', NULL, $_POST['time_format']);
				Auth::setSettings('TIME_ZONE', NULL, $_POST['time_zone']);
				
				$template->assign('success', 'Your settings has been updated.');
			break;
		}
	}
?>