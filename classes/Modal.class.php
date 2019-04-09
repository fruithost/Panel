<?php
	namespace fruithost;
	
	class Modal {
		private $name = null;
		private $title = null;
		private $content = null;
		private $buttons = [];
		
		public function __construct($name, $title, $content) {
			$this->name		= $name;
			$this->title	= $title;
			
			if(is_string($content)) {
				$this->content	= $content;
			}
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getTitle() {
			return $this->title;
		}
		
		public function addButton($button) {
			$this->buttons[] = $button;
			return $this;
		}
		
		public function getButtons() {
			return $this->buttons;
		}
		
		public function getContent($template) {
			if(preg_match('/\.php$/', $this->content)) {
				require_once($this->content);
				return;
			}
			
			return $this->content;
		}
	}
?>