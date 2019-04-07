<?php	
	namespace fruithost;
	
	class Route {
		private $path		= '/';
		private $callback	= NULL;
		
		public function getPath() {
			return $this->path;
		}
		
		public function setPath($path) {
			$this->path = $path;
		}
		
		public function getCallback() {
			return $this->callback;
		}
		
		public function setCallback($callback) {
			$this->callback = $callback;
		}
	}
?>