<?php
	namespace fruithost\Modules;
	
	class ModuleInfo {
		private ?string $name			= null;
		private ?string $version		= null;
		private ?string $category		= null;
		private ?string $icon			= null;
		private int $order				= 999;
		private ?string  $description	= null;
		private ?ModuleAuthor $author	= null;
		private ?string $repository		= null;
		private ?object $depencies		= null;
		private bool $is_valid			= false;
		
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
			
			$data = json_decode($content, false);
			
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
			
			if(!empty($data->depencies)) {
				$this->depencies = $data->depencies;
			}
			
			$this->is_valid = true;
		}
		
		public function isValid() : bool {
			return $this->is_valid;
		}
		
		public function getName() : ?string {
			return $this->name;
		}
		
		public function getVersion() : ?string {
			return $this->version;
		}
		
		public function getCategory() : ?string {
			return $this->category;
		}
		
		public function getOrder() : int {
			return $this->order;
		}
		
		public function getIcon(bool $raw = false) : ?string {
			if($raw) {
				return $this->icon;
			}
			
			/* When the Icon is an URL or an embedded Image (Base64) */
			if(preg_match('/^(http|https|data):/', $this->icon)) {
				return sprintf('<img alt="Icon" class="module-icon" src="%s" />', $this->icon);
			}
			
			// @ToDo check if its an local file
			
			return sprintf('<i class="bi bi-%s"></i>', $this->icon);
		}
		
		public function getDescription() : string {
			return $this->description;
		}
		
		public function getAuthor() : ?ModuleAuthor {
			return $this->author;
		}
		
		public function getRepository() : ?string {
			return $this->repository;
		}
		
		public function getDepencies() : object {
			if($this->depencies === null) {
				return (object) [];
			}
			
			return $this->depencies;
		}
	}
?>