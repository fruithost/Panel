<?php
	namespace fruithost;
	
	class Modules {
		private ?Core $core		= null;
		private array $modules	= [];
		
		public function __construct(Core $core) {
			$this->core = $core;
			$enabled	= [];
			$versions	= [];
			
			foreach(Database::fetch('SELECT `name` FROM `' . DATABASE_PREFIX . 'modules` WHERE `state`=\'ENABLED\' AND `time_deleted` IS NULL') AS $entry) {
				$enabled[]				= $entry->name;
				$versions[$entry->name]	= $this->getVersion($entry->name);
			}

			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$path	= sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				$module	= new Module($path);
				
				foreach($module->getInfo()->getDepencies() AS $name => $version) {
					if(!in_array($name, $enabled, true) || version_compare($versions[$name], $version, '>=') === false) {
						$module->setLocked(true);
					}
				}
				
				$module->setEnabled(in_array(basename($path), $enabled, true));
				$this->addModule(basename($path), $module);
			}
		}
		
		public function getVersion($name) : ?string {
			$path = sprintf('%s%s%s', $this->getPath(), DS, $name);
			$file = sprintf('%s%smodule.package', $path, DS);
			
			if(!file_exists($file)) {
				return null;
			}
			
			$content = file_get_contents($file);
			
			if(empty($file)) {
				return null;
			}
			
			$data = json_decode($content, false);
			
			if(json_last_error() !== JSON_ERROR_NONE) {
				return null;
			}
			
			
			if(!empty($data->version)) {
				return $data->version;
			}
			
			return null;
		}
		
		public function getList() : array {
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
		
		public function getPath() : string {
			return sprintf('%s%s%s', dirname(PATH), DS, 'modules');
		}
		
		public function addModule(string $name, Module $module) {
			if(!$module->isEnabled()) {
				return;
			}
			
			if(file_exists(sprintf('%s/languages/', $module->getPath()))) {
				I18N::addPath(sprintf('%s/languages/', $module->getPath()));
			}
			
			$this->modules[$name] = $module->init($this->core);
		}
		
		public function getModules() : array {
			return $this->modules;
		}
		
		public function hasModule(string $name, bool $all = false) : bool {
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
		
		public function getModule(string $name, bool $all = false) : ?Module {
			if($all) {
				$modules = $this->getList();

                return $modules[$name] ?? null;
            }

            return $this->modules[$name] ?? null;
        }
	}
?>