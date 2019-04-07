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

				$this->addModule(new Module(sprintf('%s%s%s', $this->getPath(), DS, $info->getFilename())));
			}
		}
		
		public function getPath() {
			return sprintf('%s%s%s', dirname(PATH), DS, 'modules');
		}
		
		public function addModule($module) {
			$this->modules[] = $module->init($this->core);
		}
	}
?>