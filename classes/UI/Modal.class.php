<?php
	namespace fruithost\UI;
	
	use fruithost\Template;
	
	class Modal {
		private ?string $name		= null;
		private ?string $title		= null;
		private mixed $content		= null;
		private array $buttons		= [];
		private array $callbacks	= [];
		private array $variables	= [];
		
		public function __construct(string $name, string $title, mixed $content = null, $variables = []) {
			$this->name			= $name;
			$this->title		= $title;
			$this->variables	= $variables;
			
			if(is_string($content)) {
				$this->content	= $content;
			}
		}
		
		public function getCallback(string $name) : mixed  {
			if(!isset($this->callbacks[$name])) {
				return null;
			}
			
			return $this->callbacks[$name];
		}
		
		public function onSave(mixed $method) : Modal {
			$this->callbacks['save'] = $method;
			
			return $this;
		}
		
		public function getName() : ?string {
			return $this->name;
		}
		
		public function getTitle() : ?string {
			return $this->title;
		}
		
		public function addButton(array $button) : Modal {
			$this->buttons[] = $button;
			return $this;
		}
		
		public function getButtons() : array {
			return $this->buttons;
		}
		
		public function getContent(Template $template) : mixed {
			foreach($this->variables AS $name => $value) {
				${$name} = $value;
			}
			
			// @ToDo check if is template file
			if(!empty($this->content)) {
				$template->display($this->content, $this->variables, false, false);
				return null;
			}
			
			return $this->content;
		}
	}
?>