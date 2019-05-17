<?php
	namespace fruithost;
	
	class Modules {
		private $core		= null;
		private $modules	= [];
		
		public function __construct($core) {
			$this->core = $core;
			$enabled	= [];
			
			foreach(Database::fetch('SELECT `name` FROM `' . DATABASE_PREFIX . 'modules` WHERE `state`=\'ENABLED\' AND `time_deleted` IS NULL') AS $entry) {
				$enabled[] = $entry->name;
			}
			
			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$path	= sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				$module	= new Module($path);
				$module->setEnabled(in_array(basename($path), $enabled));
				$this->addModule(basename($path), $module);
			}
		}
		
		public function getList() {
			$modules = [];
			
			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$path	= sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				$module	= new Module($path);
				$name	= basename($path);
				
				if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'modules` WHERE `name`=:name AND `time_deleted` IS NULL', [
					'name'	=> $name
				])) {
					if($this->hasModule($name)) {
						$modules[$name] = $this->getModule($name);
					} else {
						$modules[$name] = $module;					
					}
				}
			}
			
			return $modules;
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
		
		public function hasModule($name, $all = false) {
			if($all) {
				$modules = $this->getList();
				
				if(!isset($modules[$name])) {
					return false;
				}
				
				return true;
			}
			
			if(!isset($this->modules[$name])) {
				return false;
			}
			
			return true;
		}
		
		public function getModule($name, $all = false) {
			if($all) {
				$modules = $this->getList();
				
				if(!isset($modules[$name])) {
					return null;
				}
				
				return $modules[$name];
			}
			
			if(!isset($this->modules[$name])) {
				return null;
			}
			
			return $this->modules[$name];
		}
	}
?>