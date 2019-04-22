<?php
	namespace fruithost;
	
	class Session {
		public static function init() {
			if(!isset($_SESSION)) {
				@session_save_path(sprintf('%s%stemp', dirname(PATH), DS));
				
				@session_start([
					'cookie_lifetime'	=> 3600,
					'read_and_close'	=> false
				]);
			}
		}
		
		public static function get($name) {
			self::init();
			
			if(isset($_SESSION[$name])) {
				return $_SESSION[$name];
			}
			
			return NULL;
		}
		
		public static function set($name, $value) {
			self::init();
			
			$_SESSION[$name] = $value;
		}
		
		public static function remove($name) {
			self::init();
			
			$_SESSION[$name] = NULL;
			unset($_SESSION[$name]);
		}
	}
?>