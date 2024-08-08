<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\UI;
	
	class Button {
		private ?string $name		= null;
		private ?string $label		= null;
		private array $classes		= [];
		private ?string $modal		= null;
		private bool $dismissable	= false;
		
		public function getName() : ?string {
			return $this->name;
		}
		
		public function setName(string $name) : Button {
			$this->name = $name;
			return $this;
		}
		
		public function isDismissable() : bool {
			return $this->dismissable;
		}
		
		public function setDismissable() : Button {
			$this->dismissable = true;
			return $this;
		}
		
		public function hasModal() : bool {
			return !empty($this->modal);
		}
		
		public function getModal() : ?string {
			return $this->modal;
		}
		
		public function setModal(string $modal) : Button {
			$this->modal = $modal;
			return $this;
		}
		
		public function getLabel() : ?string {
			return $this->label;
		}
		
		public function setLabel(string $label) : Button {
			$this->label = $label;
			return $this;
		}
		
		public function getClasses(bool $raw = true) : string | array {
			if($raw) {
				return $this->classes;
			}
			
			return implode(' ', $this->classes);
		}
		
		public function addClass(string $class) : Button {
			$this->classes[] = $class;
			return $this;
		}
	}
?>