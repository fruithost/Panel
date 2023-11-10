<?php	
	namespace fruithost\Network;
	
	class Route {
		private string $path		= '/';
		private ?\Closure $callback	= null;
		
		public function getPath() : string {
			return $this->path;
		}
		
		public function setPath(string $path) : void {
			$this->path = $path;
		}
		
		public function getCallback() : ?\Closure {
			return $this->callback;
		}
		
		public function setCallback(?\Closure $callback) : void {
			$this->callback = $callback;
		}
	}
?>