<?php
	namespace fruithost\Modules;
	
	use fruithost\System\Core;
    use fruithost\Network\Router;
    use fruithost\Templating\Template;
    use fruithost\UI\Button;
    use fruithost\UI\Modal;

    class ModuleInterface {
		private ?Core $core			= null;
		private ?Module $instance	= null;
		
		public function __construct(Core $core, $instance) {
			$this->core		= $core;
			$this->instance	= $instance;
			
			$this->init();
		}
		
		protected function init() : void {
			/* Override Me */
		}
		
		public function preLoad() : void {
			/* Override Me */
		}
		
		public function load() : void {
			/* Override Me */
		}

		public function onPOST(mixed $data = null) : void {
			/* Override Me */
		}

        public function onUpdate($data = []) : ?string {
            /* Override Me */

            return null;
        }

        public function onSave($data = []) : ?string {
            /* Override Me */

            return null;
        }

		public function frame() : bool {
			/* Override Me */

            return false;
		}
		
		public function getRouter() : ?Router {
			return $this->core->getRouter();
		}
		
		public function getModules() : ?Modules {
			return $this->core->getModules();
		}
		
		public function getTemplate() : ?Template {
			return $this->core->getTemplate();
		}
		
		public function getCore() : ?Core {
			return $this->core;
		}
		
		public function getInstance() : ?Module {
			return $this->instance;
		}
		
		public function setSettings(string $name, mixed $value = null) : void {
			$this->instance->setSettings($name, $value);
		}
		
		public function getSettings(string $name, mixed $default = null) : mixed {
			return $this->instance->getSettings($name, $default);			
		}
		
		public function addButton(Button | array $button) : void {
			$this->addFilter('buttons', function($buttons) use($button) {
				$buttons[] = $button;
				return $buttons;
			}, 10);
		}
		
		public function addModal(Modal $modal) : void {
			$this->addFilter('modals', function($modals) use($modal) {
				$modals[] = $modal;
				return $modals;
			}, 10);
		}
		
		public function assign(string $name, mixed $value) : void {
			$this->getCore()->getTemplate()->assign($name, $value);
		}
		
		public function url(string $path = '') : string {
			return $this->getCore()->getTemplate()->url($path);
		}
		
		/* Filter */
		public function addFilter(string $name, \Closure | array  $method, int $priority = 50) : bool {
			return $this->core->getHooks()->addFilter($name, $method, $priority);
		}
		
		public function removeFilter(string $name, \Closure | array $method, int $priority = 50) : bool {
			return $this->core->getHooks()->removeFilter($name, $method, $priority);
		}
		
		public function hasFilter(string $name, mixed $method = false) : bool {
			return $this->core->getHooks()->hasFilter($name, $method);
		}
		
		public function applyFilter(string $name, mixed $arguments) : mixed {
			return $this->core->getHooks()->applyFilter($name, $arguments);
		}
		
		/* Actions */
		public function addAction(string $name, \Closure | array  $method, int $priority = 50) : bool {
			return $this->core->getHooks()->addAction($name, $method, $priority);
		}
		
		public function removeAction(string $name, \Closure | array $method, int $priority = 50) : bool {
			return $this->core->getHooks()->removeAction($name, $method, $priority);
		}
		
		public function hasAction(string $name, mixed $method = false) : bool {
			return $this->core->getHooks()->hasAction($name, $method);
		}
		
		public function runAction(string $name, mixed $arguments = null) : bool {
			return $this->core->getHooks()->runAction($name, $arguments);
		}
	}
?>