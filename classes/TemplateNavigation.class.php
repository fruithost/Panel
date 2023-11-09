<?php
	namespace fruithost;
	
	class TemplateNavigation {
		private ?Core $core		= null;
		private array $entries	= [];
		
		public function __construct(Core $core) {
			$this->core = $core;
		}
		
		public function addCategory(string $name, string $label) {
			$category							= new TemplateNavigationCategory($this, $name, $label);
			$this->entries[$category->getID()]	= $category;
		}
		
		public function getCore() : Core {
			return $this->core;
		}
		
		public function getCategory(string $name) : TemplateNavigationCategory {
			return $this->entries[$name];
		}
		
		public function isEmpty() : bool {
			return (count($this->entries) === 0);
		}
		
		public function getEntries() : array {
			return $this->entries;
		}
	}
?>