<?php
	namespace fruithost;
	
	class Request {
		public static function url() {
			return $_SERVER['REQUEST_URI'];
		}
	}
?>