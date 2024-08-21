<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Modules;
	
	use fruithost\System\Core;
    use fruithost\Storage\Database;
    use fruithost\Localization\I18N;
	use PHPMailer\Exception;

	class Modules {
		private ?Core $core		= null;
		private array $modules	= [];
		private bool $disabled	= false;
		private array $errors	= [];
		
		public function __construct(Core $core, bool $disabled = false) {
			$this->core = $core;
			$this->disabled = $disabled;
			$enabled	= [];
			$versions	= [];

			if($this->disabled) {
				return;
			}
			
			foreach(Database::fetch('SELECT `name` FROM `' . DATABASE_PREFIX . 'modules` WHERE `state`=\'ENABLED\' AND `time_deleted` IS NULL') AS $entry) {
				$enabled[]				= $entry->name;
				try {
					$versions[$entry->name]	= $this->getVersion($entry->name);
				} catch(Exception $e) {
					#print_r($e->getMessage());
				}
			}

			foreach(new \DirectoryIterator($this->getPath()) AS $info) {
				if($info->isDot() || $info->getFilename()[0] == '.' || !$info->isDir() || in_array($info->getFilename(), [
					'modules.packages',
					'modules.list',
					'LICENSE',
					'README.md'
				])) {
					continue;
				}

				$path	= sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename());
				$module	= new Module($path);

				if(!$module->getInfo()->isValid()) {
					return;
				}

				foreach($module->getInfo()->getDepencies() AS $name => $version) {
					if(!in_array($name, $enabled, true) || version_compare($versions[$name], $version, '>=') === false) {
						$module->setLocked(true);
					}
<<<<<<< HEAD

					$module->setEnabled(in_array(basename($path), $enabled, true));
					$this->addModule(basename($path), $module);
				} catch(\Exception $e) {
					// @ToDo Catch Module-Errors!
					print_r($e->getMessage());
=======
>>>>>>> Adding error handling for modules.
				}

				$module->setEnabled(in_array(basename($path), $enabled, true));
				$this->addModule(basename($path), $module);
			}
		}

		public function enable() : void {
			$this->disabled = false;
		}

		public function disable() : void {
			$this->disabled = true;
		}

		public function isDisabled() : bool {
			return 	$this->disabled;
		}
		
		public function getVersion($name) : ?string {
			$path = sprintf('%s%s%s', $this->getPath(), DS, $name);
			$file = sprintf('%s%smodule.package', $path, DS);

			if(!file_exists($file)) {
				throw new Exception('module.package for Module "' . $name . '" not exists!');
				return null;
			}

			$content = file_get_contents($file);
			
			if(empty($content)) {
				throw new Exception('Empty module.package for Module "' . $name . '"!');
				return null;
			}
			
			$data = json_decode($content, false);
			
			if(json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception('JSON-Format Error module.package for Module "' . $name . '"!');
				return null;
			}
			
			
			if(!empty($data->version)) {
				return $data->version;
			}

			throw new Exception('Empty Module-Version "' . $name . '"!');
			
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

				if(!$module->getInfo()->isValid()) {
					continue;
				}
				
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
		
		public function addModule(string $name, Module $module) : void {
			if(!$module->isEnabled()) {
				return;
			}
			
			if(file_exists(sprintf('%s/languages/', $module->getPath()))) {
				I18N::addPath(sprintf('%s/languages/', $module->getPath()));
			}
			
			try {
				$this->modules[$name] = $module->init($this->core);
			} catch(\Exception $e) {
<<<<<<< HEAD
				// @ToDo Catch Module-Errors!
				print_r($e->getMessage());
				exit();
=======
				$module->setLocked(true);
				$this->modules[$name]	= $module;
				$this->errors[]			= new ModuleError($name, $e);
>>>>>>> Adding error handling for modules.
			}
		}
		
		public function getErrors() : array {
			return $this->errors;
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