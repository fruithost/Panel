<?php
	namespace fruithost;
	
	class Modal {
		private $name		= null;
		private $title		= null;
		private $content	= null;
		private $buttons	= [];
		private $callbacks	= [];
		private $variables	= [];
		
		public function __construct(string $name, string $title, $content, $variables = []) {
			$this->name			= $name;
			$this->title		= $title;
			$this->variables	= $variables;
			
			if(is_string($content)) {
				$this->content	= $content;
			}
		}
		
		public function getCallback($name) {
			if(!isset($this->callbacks[$name])) {
				return null;
			}
			
			return $this->callbacks[$name];
		}
		
		public function onSave($method) : Modal {
			$this->callbacks['save'] = $method;
			
			return $this;
		}
		
		public function getName() : string {
			return $this->name;
		}
		
		public function getTitle() : string {
			return $this->title;
		}
		
		public function addButton(array $button) : Modal {
			$this->buttons[] = $button;
			return $this;
		}
		
		public function getButtons() : array {
			return $this->buttons;
		}
		
		public function getContent(Template $template) {
			foreach($this->variables AS $name => $value) {
				${$name} = $value;
			}
			
			// @ToDo check if is template file
			if(!empty($this->content)) {
				$template->display($this->content, $this->variables, false, false);
				return;
			}
			
			return $this->content;
		}
	}
?>