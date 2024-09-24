<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author  Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	foreach([
        'Loader',
        'Core'
    ] as $file) {
		require_once(sprintf('%1$s/classes/System/%2$s.class.php', dirname(__FILE__), $file));
	}

	new fruithost\System\Core();
?>