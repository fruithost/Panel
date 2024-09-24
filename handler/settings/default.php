<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author  Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;

    Auth::setSettings('LANGUAGE', NULL, $_POST['language']);
    Auth::setSettings('TIME_FORMAT', NULL, $_POST['time_format']);
    Auth::setSettings('TIME_ZONE', NULL, $_POST['time_zone']);

    $template->getCore()->getHooks()->runAction('SAVE_ACCOUNT_SETTINGS_GLOBAL', $_POST);
    $template->assign('success', I18N::get('Your settings has been updated.'));
    I18N::reload();
?>