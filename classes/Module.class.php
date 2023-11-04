<?php
	namespace fruithost;
	
	class Module {
		private $path		= null;
		private $info		= null;
		private $instance	= null;
		private $enabled	= false;
		
		public function __construct($path) {
			$this->path = $path;
			$this->info = new ModuleInfo($path);
		}
		
		public function isValid() : bool {
			return (!empty($this->info) && !$this->info->isValid());
		}
		
		public function getInfo() : ModuleInfo {
			return $this->info;
		}
		
		public function getInstance() {
			return $this->instance;
		}
		
		public function getPath() : string {
			return $this->path;
		}
		
		public function getSettings(string $name, mixed $default = NULL) : mixed {
			$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'modules_settings` WHERE `module`=:module AND `key`=:key LIMIT 1', [
				'key'		=> $name,
				'module'	=> $this->getDirectory()
			]);
			
			if(!empty($result)) {
				if(!empty($result->value)) {
					return $result->value;
				}
			}
			
			return $default;
		}
		
		public function setSettings(string $name, mixed $value = NULL) {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'modules_settings` WHERE `module`=:module AND `key`=:key LIMIT 1', [
				'module'	=> $this->getDirectory(),
				'key'		=> $name
			])) {
				Database::update(DATABASE_PREFIX . 'modules_settings', [ 'module', 'key' ], [
					'module'		=> $this->getDirectory(),
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert(DATABASE_PREFIX . 'modules_settings', [
					'id'			=> NULL,
					'module'		=> $this->getDirectory(),
					'key'			=> $name,
					'value'			=> $value
				]);
			}
		}
		
		public function hasSettingsPath() : bool {
			return file_exists($this->getSettingsPath());
		}
		
		public function getSettingsPath() : string {
			return sprintf('%s%sadmin.php', $this->path, DS);
		}
		
		public function getDirectory() : string {
			return basename($this->path);
		}
		
		public function isEnabled() : bool {
			return $this->enabled;
		}
		
		public function setEnabled(bool $state) {
			$this->enabled = $state;
		}
		
		public function init(Core $core) : Module {
			$script = sprintf('%s%smodule.php', $this->path, DS);
			
			if(!file_exists($script)) {
				return;
			}
			
			$old = get_declared_classes();
			require_once($script);
			$new = get_declared_classes();
			
			foreach(array_diff($new, $old) AS $class) {
				if(is_subclass_of($class, 'fruithost\\ModuleInterface', true)) {
					$reflect			= new \ReflectionClass($class);
					$instance			= $reflect->newInstanceArgs([ $core, $this ]);
					$this->instance		= $instance;
				}
			}
			
			if(!empty($this->info->getCategory())) {
				$core->getHooks()->addFilter($this->info->getCategory(), function($entries) use ($core) {
					$entries[] = (object) [
						'name'		=> $this->info->getName(),
						'icon'		=> $this->info->getIcon(),
						'order'		=> $this->info->getOrder(),
						'target'	=> $core->getHooks()->applyFilter('TARGET_' . $this->info->getName(), NULL),
						'url'		=> $core->getHooks()->applyFilter('URL_' . $this->info->getName(), sprintf('/module/%s', basename($this->path))),
						'active'	=> $core->getRouter()->is(sprintf('/module/%s', basename($this->path))) || $core->getRouter()->startsWith(sprintf('/module/%s/', basename($this->path)))
					];
					
					return $entries;
				});
			}
			
			return $this;
		}
	}
?>