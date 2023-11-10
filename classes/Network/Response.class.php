<?php	
	namespace fruithost\Network;

    class Response {
		public static function addHeader(string $name, string $value) : void {
			ResponseFactory::getInstance()->addHeader($name, $value);
		}
		
		public static function header() : void {
			ResponseFactory::getInstance()->header();
		}
		
		public static function redirect(string $url) : void {
			ResponseFactory::getInstance()->redirect($url);
		}
	}
?>