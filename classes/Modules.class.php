<?php
	namespace fruithost;
	
	class Modules {
		private $core		= null;
		private $modules	= [];
		
		public function __construct($core) {
			$this->core = $core;
			
			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$path = sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				
				$this->addModule(basename($path), new Module($path));
			}
		}
		
		public function getPath() {
			return sprintf('%s%s%s', dirname(PATH), DS, 'modules');
		}
		
		public function addModule($name, $module) {
			$this->modules[$name] = $module->init($this->core);
		}
		
		public function getModules() {
			return $this->modules;
		}
		
		public function getModule($name) {			
			if(!isset($this->modules[$name])) {
				return null;
			}
			
			return $this->modules[$name];
		}
	}
?>