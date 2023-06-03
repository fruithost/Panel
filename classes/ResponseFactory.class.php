<?php
	namespace fruithost;
	
	class ResponseFactory {
		private static $instance	= NULL;
		private $headers			= [
			'Content-Type'	=> 'text/html; charset=UTF-8'
		];
		
		public static function getInstance() {
			if(self::$instance === NULL) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
	
		private function __construct() {
			/* do Nothing */
		}
		
		public function addHeader(string $name, string $value) {
			$this->headers[$name] = $value;
		}
		
		public function header() {
			if(count($this->headers) > 0) {
				foreach($this->headers AS $name => $value) {
					header(sprintf('%s: %s', $name, $value));
				}
			}
		}
		
		public function redirect(string $url) {
			$this->addHeader('Location', $url);
			$this->header();
			exit();
		}
	}
	
?>