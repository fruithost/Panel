<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Network;

    class Response {
		public static function addHeader(string $name, string $value) : void {
			ResponseFactory::getInstance()->addHeader($name, $value);
		}
		
		public static function header() : void {
			ResponseFactory::getInstance()->header();
		}

		public static function setContentType(string $type) : void {
			ResponseFactory::getInstance()->setContentType($type);
		}
		
		public static function redirect(string $url) : void {
			ResponseFactory::getInstance()->redirect($url);
		}
	}
?>