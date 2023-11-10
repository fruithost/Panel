<?php
	namespace fruithost\Accounting;
	
	class Session {
		public static function init() : void {
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
		
		public static function has(string $name) : bool {
			self::init();
			
			if(isset($_SESSION[$name])) {
				return true;
			}
			
			return false;
		}
		
		public static function get(string $name) : mixed  {
			self::init();
			
			if(isset($_SESSION[$name])) {
				return $_SESSION[$name];
			}
			
			return null;
		}
		
		public static function set(string $name, mixed $value) : void {
			self::init();
			
			$_SESSION[$name] = $value;
		}
		
		public static function remove(string $name) : void {
			self::init();
			
			$_SESSION[$name] = null;
			unset($_SESSION[$name]);
		}
	}
?>