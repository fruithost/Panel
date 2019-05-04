<?php
	namespace fruithost;
	
	class Request {
		private static $get = [];
		
		public static function init() {
			self::$get = $_GET;
		}
		
		public static function has($name) {
			return in_array($name, self::$get);
		}
		
		public static function get($name) {
			if(self::has($name)) {
				return null;
			}
			
			return self::$get[$name];
		}
		
		public static function url() {
			return $_SERVER['REQUEST_URI'];
		}
	}
?>