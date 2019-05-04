<?php
	namespace fruithost;
	
	class RequestFactory {
		private static $instance	= NULL;
		private $_get				= [];
		
		public function __construct() {
			if(empty($this->_get)) {
				$this->init();
			}
		}
		
		public static function getInstance() {
			if(self::$instance === NULL) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function init() {
			if(!empty($this->_get)) {
				return;
			}
			
			// Fix bad behavior of Apache's Alias configguration
			if(empty($_SERVER['QUERY_STRING']) && strtok($_SERVER['REQUEST_URI'], '?') !== false) {
				$split = explode('?', $_SERVER['REQUEST_URI']);
				
				if(count($split) > 1) {
					$_SERVER['QUERY_STRING']	= $split[1];
					parse_str($_SERVER['QUERY_STRING'], $_GET);
				}
			}
			
			$this->_get = $_GET;
		}
		
		public function has($name) {
			return in_array($name, array_keys($this->_get));
		}
		
		public function get($name) {
			if(!$this->has($name)) {
				return null;
			}
			
			return $this->_get[$name];
		}
		
		public function url() {
			return $_SERVER['REQUEST_URI'];
		}
	}
	
	class Request {
		public static function init() {			
			RequestFactory::getInstance()->init();
		}
		
		public static function has($name) {
			return RequestFactory::getInstance()->has($name);
		}
		
		public static function get($name) {
			return RequestFactory::getInstance()->get($name);
		}
		
		public static function url() {
			return RequestFactory::getInstance()->url();
		}
	}
?>