<?php
	namespace fruithost;
	
	class ModuleInfo {
		private $name			= null;
		private $version		= null;
		private $category		= null;
		private $icon			= null;
		private $order			= null;
		private $description	= null;
		private $author			= null;
		private $repository		= null;
		private $is_valid		= false;
		
		public function __construct(string $path) {
			$file = sprintf('%s%smodule.package', $path, DS);
			
			if(!file_exists($file)) {
				// throw new \Exception('[Module] module.package not exists: ' . $path);
				// @ToDo Exception
				return;
			}
			
			$content = file_get_contents($file);
			
			if(empty($file)) {
				// throw new \Exception('[Module] module.package is empty: ' . $path);
				// @ToDo Exception
				return;
			}
			
			$data = json_decode($content);
			
			if(json_last_error() !== JSON_ERROR_NONE) {
				// throw new \Exception('[Module] module.package is broken: ' . $path);
				// @ToDo Exception
				return;
			}
			
			if(!empty($data->name)) {
				$this->name = $data->name;
			}
			
			if(!empty($data->version)) {
				$this->version = $data->version;
			}
			
			// @ToDo check Enum
			if(!empty($data->category)) {
				$this->category = $data->category;
			}
			
			if(!empty($data->icon)) {
				$this->icon = $data->icon;
			}
			
			if(!empty($data->order)) {
				$this->order = $data->order;
			}
			
			if(!empty($data->description)) {
				$this->description = $data->description;
			}
			
			if(!empty($data->author)) {
				$this->author = new ModuleAuthor($data->author);
			}
			
			if(!empty($data->repository)) {
				$this->repository = $data->repository;
			}
			
			$this->is_valid = true;
		}
		
		public function isValid() : bool {
			return $this->is_valid;
		}
		
		public function getName() : string {
			return $this->name;
		}
		
		public function getVersion() : string {
			return $this->version;
		}
		
		public function getCategory() : string {
			return $this->category;
		}
		
		public function getOrder() : int {
			return $this->order;
		}
		
		public function getIcon(bool $raw = false) : string {
			if($raw) {
				return $this->icon;
			}
			
			// @ToDo If starts with http/https or slash or base64
			
			return sprintf('<i class="material-icons">%s</i>', $this->icon);
		}
		
		public function getDescription() : string {
			return $this->description;
		}
		
		public function getAuthor() : ModuleAuthor {
			return $this->author;
		}
		
		public function getRepository() : string {
			return $this->repository;
		}
	}
?>