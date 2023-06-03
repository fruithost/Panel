<?php	
	namespace fruithost;
	
	class Route {
		private $path		= '/';
		private $callback	= NULL;
		
		public function getPath() : string {
			return $this->path;
		}
		
		public function setPath(string $path) {
			$this->path = $path;
		}
		
		public function getCallback() : callable {
			return $this->callback;
		}
		
		public function setCallback(callable $callback) {
			$this->callback = $callback;
		}
	}
?>