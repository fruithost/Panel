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
		
		public function load() {
			/* Override Me */
		}
		
		public function getCore() {
			return $this->core;
		}
		
		public function addButton($button) {
			$this->addFilter('buttons', function($buttons) use($button) {
				$buttons[] = $button;
				return $buttons;
			});
		}
		
		public function addModal($modal) {
			$this->addFilter('modals', function($modals) use($modal) {
				$modals[] = $modal;
				return $modals;
			});
		}
		
		public function assign($name, $value) {
			$this->getCore()->getTemplate()->assign($name, $value);
		}
		
		public function url($path) {
			$this->getCore()->getTemplate()->url($path);
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