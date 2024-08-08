<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Network;
	
	class ResponseFactory {
		private static ?ResponseFactory $instance	= null;
		private array $headers						= [
			'Content-Type'	=> 'text/html; charset=UTF-8'
		];
		
		public static function getInstance() : ResponseFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
	
		private function __construct() {
			if(defined('DEBUG') && DEBUG) {
				$this->addHeader('DEBUG', DEBUG);
			}
			
			if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				//$this->addHeader('Content-Encoding', 'gzip');
				// @ToDo Debug output encoding
			}
		}
		
		public function addHeader(string $name, string $value) : void {
			$this->headers[$name] = $value;
		}
		
		public function header() : void {
			if(count($this->headers) > 0) {
				foreach($this->headers AS $name => $value) {
					header(sprintf('%s: %s', $name, $value));
				}
			}
		}
		
		public function redirect(string $url) : void {
			$this->addHeader('Location', $url);
			$this->header();
			exit();
		}
	}
	
?>