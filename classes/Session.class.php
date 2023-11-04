<?php
	namespace fruithost;
	
	class Session {
		public static function init() {
			if(!isset($_SESSION)) {
				#@session_save_path(sprintf('%s%stemp', dirname(PATH), DS));
				
				@session_start(/*[
					'cookie_lifetime'	=> 3600,
					'read_and_close'	=> false
				]*/);
			}
		}
				
		public static function getID() : string | false {
			return session_id();
		}
		
		public static function has($name) : bool {
			self::init();
			
			if(isset($_SESSION[$name])) {
				return true;
			}
			
			return false;
		}
		
		public static function get($name) : bool | int | float | string | array | object | null  {
			self::init();
			
			if(isset($_SESSION[$name])) {
				return $_SESSION[$name];
			}
			
			return NULL;
		}
		
		public static function set(string $name, bool | int | float | string | array | object | null $value) {
			self::init();
			
			$_SESSION[$name] = $value;
		}
		
		public static function remove(string $name) {
			self::init();
			
			$_SESSION[$name] = NULL;
			unset($_SESSION[$name]);
		}
	}
?>