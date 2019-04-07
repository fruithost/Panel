<?php
	namespace fruithost;
	
	class TemplateNavigation {
		private $core		= null;
		private $entries	= [];
		
		public function __construct($core) {
			$this->core = $core;
		}
		
		public function addCategory($name, $label) {
			$category = new TemplateNavigationCategory($this, $name, $label);
			$this->entries[$category->getID()] = $category;
		}
		
		public function getCore() {
			return $this->core;
		}
		
		public function isEmpty() {
			return (count($this->entries) === 0);
		}
		
		public function getEntries() {
			return $this->entries;
		}
	}
?>