<?php	
	namespace fruithost;
	
	class Route {
		private string $path		= '/';
		private ?\Closure $callback	= null;
		
		public function getPath() : string {
			return $this->path;
		}
		
		public function setPath(string $path) {
			$this->path = $path;
		}
		
		public function getCallback() : ?\Closure {
			return $this->callback;
		}
		
		public function setCallback(?\Closure $callback) {
			$this->callback = $callback;
		}
	}
?>