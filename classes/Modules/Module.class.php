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

    class Module {
		private ?string $path				= null;
		private ?ModuleInfo $info			= null;
		private ?ModuleInterface $instance	= null;
		private bool $enabled				= false;
		private bool$locked					= false;
		
		public function __construct(string $path) {
			$this->path = $path;
			$this->info = new ModuleInfo($path);
		}
		
		public function isValid() : bool {
			return (!empty($this->info) && !$this->info->isValid());
		}
		
		public function getInfo() : ?ModuleInfo {
			return $this->info;
		}
		
		public function getInstance() : ?ModuleInterface {
			return $this->instance;
		}
		
		public function getPath() : string {
			return $this->path;
		}
		
		public function getSettings(string $name, mixed $default = null) : mixed {
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
		
		public function setSettings(string $name, mixed $value = null) : void {
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
					'id'			=> null,
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
		
		public function isFrame() : bool {
			if($this->getInstance() == null) {
				return false;
			}
			
			// @ToDo Permissions-Check?
			
			return method_exists($this->getInstance(), 'frame') && !empty($this->getInstance()->frame());
		}
		
		public function isEnabled() : bool {
			return $this->enabled;
		}
		
		public function setEnabled(bool $state) : void  {
			$this->enabled = $state;
		}
		
		public function isLocked() : bool {
			return $this->locked;
		}
		
		public function setLocked(bool $state) : void  {
			$this->locked = $state;
		}
		
		public function init(Core $core) : ?Module {
			$script = sprintf('%s%smodule.php', $this->path, DS);
			
			if(!file_exists($script)) {
				return null;
			}
			
			$old = get_declared_classes();
			require_once($script);
			$new = get_declared_classes();
			
			foreach(array_diff($new, $old) AS $class) {
				if(is_subclass_of($class, 'fruithost\\Modules\\ModuleInterface', true)) {
					$reflect			= new \ReflectionClass($class);
					$instance			= $reflect->newInstanceArgs([ $core, $this ]);
					$this->instance		= $instance;
				}
			}
			
			if(!empty($this->info->getCategory()) && !$this->isLocked()) {
				$core->getHooks()->addFilter($this->info->getCategory(), function($entries) use ($core) {
					$permissions	= $this->info->getPermissions();
					$visible		= true;
					if(!empty($permissions)) {
						$visible	= false;
						
						foreach($permissions AS $permission) {
							if(\fruithost\Accounting\Auth::hasPermission($permission)) {
								$visible	= true;								
							}
						}
					}
					
					if($visible) {
						$entries[] = (object) [
							'name'		=> $this->info->getName(),
							'icon'		=> $this->info->getIcon(),
							'order'		=> $this->info->getOrder(),
							'target'	=> $core->getHooks()->applyFilter('TARGET_' . $this->info->getName(), null),
							'url'		=> $core->getHooks()->applyFilter('URL_' . $this->info->getName(), sprintf('/module/%s', basename($this->path))),
							'active'	=> $core->getRouter()->is(sprintf('/module/%s', basename($this->path))) || $core->getRouter()->startsWith(sprintf('/module/%s/', basename($this->path)))
						];
					}
					
					return $entries;
				});
			}
			
			return $this;
		}
	}
?>