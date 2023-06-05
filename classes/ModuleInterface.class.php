<?php
	namespace fruithost;
	
	class ModuleInterface {
		private $core		= null;
		private $instance	= null;
		
		public function __construct($core, $instance) {
			$this->core		= $core;
			$this->instance	= $instance;
			
			$this->init();
		}
		
		protected function init() {
			/* Override Me */
		}
		
		public function load() {
			/* Override Me */
		}
		
		public function getRouter() {
			return $this->core->getRouter();
		}
		
		public function getModules() {
			return $this->core->getModules();
		}
		
		public function getTemplate() {
			return $this->core->getTemplate();
		}
		
		public function getCore() {
			return $this->core;
		}
		
		public function getInstance() {
			return $this->instance;
		}
		
		public function setSettings($name, $value = NULL) {
			$this->instance->setSettings($name, $value);
		}
		
		public function getSettings($name, $default = NULL) {
			return $this->instance->getSettings($name, $default);			
		}
		
		public function addButton($button, $logged_in = false) {
			$this->addFilter('buttons', function($buttons) use($button) {
				$buttons[] = $button;
				return $buttons;
			}, 10, $logged_in);
		}
		
		public function addModal($modal, $logged_in = false) {
			$this->addFilter('modals', function($modals) use($modal) {
				$modals[] = $modal;
				return $modals;
			}, 10, $logged_in);
		}
		
		public function assign($name, $value) {
			$this->getCore()->getTemplate()->assign($name, $value);
		}
		
		public function url($path = '') {
			return $this->getCore()->getTemplate()->url($path);
		}
		
		/* Filter */
		public function addFilter($name, $method, $priority = 10, $logged_in = false) {
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