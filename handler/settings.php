<?php
    /**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	if(isset($_POST['action']) && $_POST['action'] === 'save') {
		switch($tab) {
			case 'security':
                require_once('settings/security.php');
			break;
			default:
                require_once('settings/default.php');
			break;
		}
	}
?>