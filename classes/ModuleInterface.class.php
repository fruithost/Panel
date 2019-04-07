<?php
	namespace fruithost;
	
	class ModuleInterface {
		private $core = null;
		
		public function __construct($core) {
			$this->core = $core;
			
			$this->init();
		}
		
		protected function init() {
			/* Override Me */
		}
		
		public function getCore() {
			return $this->core;
		}
		
		/* Filter */
		public function addFilter($name, $method, $priority = 10) {
			return $this->core->getHooks()->addFilter($name, $method, $priority);
		}
		
		public function removeFilter($name, $method, $priority = 10) {
			return $this->core->getHooks()->removeFilter($name, $method, $priority);
		}
		
		public function hasFilter($name, $method = false) {
			return $this->core->getHooks()->hasFilter($name, $method);
		}
		
		public function applyFilter($name, $arguments) {
			return $this->core->getHooks()->applyFilter($name, $arguments);
		}
		
		/* Actions */
		public function addAction($name, $method, $priority = 10) {
			return $this->core->getHooks()->addAction($name, $method, $priority);
		}
		
		public function removeAction($name, $method, $priority = 10) {
			return $this->core->getHooks()->removeAction($name, $method, $priority);
		}
		
		public function hasAction($name, $method = false) {
			return $this->core->getHooks()->hasAction($name, $method);
		}
		
		public function runAction($name, $arguments) {
			return $this->core->getHooks()->runAction($name, $arguments);
		}
	}
?>