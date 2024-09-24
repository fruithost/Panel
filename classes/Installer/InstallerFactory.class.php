<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Installer;

	class InstallerFactory {
		private static ?InstallerFactory $instance = null;
		
		public function __construct() {}
		
		public static function getInstance() : InstallerFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function getList() {}
	}
?>