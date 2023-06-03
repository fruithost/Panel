<?php	
	namespace fruithost;
	
	class Response {
		public static function addHeader(string $name, string $value) {
			ResponseFactory::getInstance()->addHeader($name, $value);
		}
		
		public static function header() {
			ResponseFactory::getInstance()->header();
		}
		
		public static function redirect(string $url) {
			ResponseFactory::getInstance()->redirect($url);
		}
	}
?>