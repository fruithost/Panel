<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\UI;
	
	use fruithost\Templating\Template;

    class Modal {
		private ?string $name		= null;
		private ?string $title		= null;
		private bool $show			= false;
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
		
		public function isShowing() : bool {
			return $this->show;
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
		
		public function show($variables = []) {
			$this->variables	= array_merge($this->variables, $variables);
			$this->show			= true;
		}
	}
?>