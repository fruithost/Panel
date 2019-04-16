<?php
	namespace fruithost;
	
	class Modules {
		private $core		= null;
		private $modules	= [];
		
		public function __construct($core) {
			$this->core = $core;
			$enabled	= [];
			
			foreach(Database::fetch('SELECT `name` FROM `fh_modules` WHERE `state`=\'ENABLED\'') AS $entry) {
				$enabled[] = $entry->name;
			}
			
			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$path = sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				$module = new Module($path);
				$module->setEnabled(in_array(basename($path), $enabled));
				
				$this->addModule(basename($path), $module);
			}
		}
		
		public function getPath() {
			return sprintf('%s%s%s', dirname(PATH), DS, 'modules');
		}
		
		public function addModule($name, $module) {
			if(!$module->isEnabled()) {
				return;
			}
			
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