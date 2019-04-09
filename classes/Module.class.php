<?php
	namespace fruithost;
	
	class Module {
		private $path		= null;
		private $info		= null;
		private $instance	= null;
		
		public function __construct($path) {
			$this->path = $path;
			$this->info = new ModuleInfo($path);
		}
		
		public function isValid() {
			return (!empty($this->info) && !$this->info->isValid());
		}
		
		public function getInfo() {
			return $this->info;
		}
		
		public function getInstance() {
			return $this->instance;
		}
		
		public function init($core) {
			$script = sprintf('%s%smodule.php', $this->path, DS);
			
			if(!file_exists($script)) {
				//throw new \Exception('[Module] had no class: ' . $script);
				return;
			}
			
			$old = get_declared_classes();
			require_once($script);
			$new = get_declared_classes();
			
			foreach(array_diff($new, $old) AS $class) {
				if(is_subclass_of($class, 'fruithost\\ModuleInterface', true)) {
					$reflect			= new \ReflectionClass($class);
					$instance			= $reflect->newInstanceArgs([ $core ]);
					$this->instance		= $instance;
				}
			}
			
			if(!empty($this->info->getCategory())) {
				$core->getHooks()->addFilter($this->info->getCategory(), function($entries) use ($core) {
					$entries[] = (object) [
						'name'		=> $this->info->getName(),
						'icon'		=> $this->info->getIcon(),
						'order'		=> $this->info->getOrder(),
						'url'		=> sprintf('/module/%s', basename($this->path)),
						'active'	=> $core->getRouter()->is(sprintf('/module/%s', basename($this->path)))
					];
					
					return $entries;
				});
			}
			
			return $this;
		}
	}
?>