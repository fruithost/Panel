<?php
	namespace fruithost;
	
	class Button {
		private $name			= null;
		private $label			= null;
		private $classes		= [];
		private $modal			= null;
		private $dismissable	= false;
		
		public function getName() {
			return $this->name;
		}
		
		public function setName($name) {
			$this->name = $name;
			return $this;
		}
		
		public function isDismissable() {
			return $this->dismissable;
		}
		
		public function setDismissable() {
			$this->dismissable = true;
			return $this;
		}
		
		public function hasModal() {
			return !empty($this->modal);
		}
		
		public function getModal() {
			return $this->modal;
		}
		
		public function setModal($modal) {
			$this->modal = $modal;
			return $this;
		}
		
		public function getLabel() {
			return $this->label;
		}
		
		public function setLabel($label) {
			$this->label = $label;
			return $this;
		}
		
		public function getClasses($raw = true) {
			if($raw) {
				return $this->classes;
			}
			
			return implode(' ', $this->classes);
		}
		
		public function addClass($class) {
			$this->classes[] = $class;
			return $this;
		}
	}
?>