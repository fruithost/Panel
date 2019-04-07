<?php	
	namespace fruithost;
	
	class Response {
		public static function addHeader($name, $value) {
			ResponseFactory::getInstance()->addHeader($name, $value);
		}
		
		public static function header() {
			ResponseFactory::getInstance()->header();
		}
		
		public static function redirect($url) {
			ResponseFactory::getInstance()->redirect($url);
		}
	}
?>