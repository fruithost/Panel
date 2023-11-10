<?php
	namespace fruithost\Templating;
	
	class TemplateFiles {
		const HEADER		= 1;
		const FOOTER		= 2;
		const STYLESHEET	= 3;
		const JAVASCRIPT	= 4;
		
		private array $header 	= [
			'javascripts'	=> [],
			'stylesheets'	=> []
		];
		
		private array $footer		= [
			'javascripts'	=> [],
			'stylesheets'	=> []
		];
		
		public function addJavaScript(string $name, string $file, string $version, ?array $depencies = null, int $position = TemplateFiles::HEADER) : void {
			$this->add($name, $file, $version, $depencies, $position, TemplateFiles::JAVASCRIPT);
		}
		
		public function addStyleSheet(string $name, string $file, string $version, ?array $depencies = null, int $position = TemplateFiles::HEADER) : void {
			$this->add($name, $file, $version, $depencies, $position, TemplateFiles::STYLESHEET);
		}
		
		private function add(string $name, string $file, string $version, ?array $depencies, int $position = TemplateFiles::HEADER, ?int $type = null) : void {
			if($type === null) {
				return;
			}
			
			$destination = null;
			
			switch($type) {
				case TemplateFiles::JAVASCRIPT:
					$destination = 'javascripts';
				break;
				case TemplateFiles::STYLESHEET:
					$destination = 'stylesheets';
				break;
			}
			
			if($destination === null) {
				return;
			}
			
			switch($position) {
				case TemplateFiles::HEADER:
					$this->header[$destination][$name] = (object) [
						'name'		=> $name,
						'file'		=> $file,
						'version'	=> $version,
						'depencies'	=> $depencies
					];
				break;
				case TemplateFiles::FOOTER:
					$this->footer[$destination][$name] = (object) [
						'name'		=> $name,
						'file'		=> $file,
						'version'	=> $version,
						'depencies'	=> $depencies
					];
				break;
			}
		}
		
		private function sortDepencies(array $input) : array {
			$cloned		= array_replace([], $input);
			$depencies	= [];
			$output		= [];
			
			do {
				$added = false;

				foreach($cloned AS $name => $entry) {
					$depencies = empty($entry->depencies) ? [] : $entry->depencies;
					
					if(count(array_diff($depencies, $depencies)) === 0) {
						$depencies[]	= $name;
						$added			= true;
						unset($cloned[$name]);
						break;
					}
				}
			} while($added);

			/*if(count($cloned)) {
				trigger_error("unable to resolve a dependency",E_USER_ERROR);
			}*/

			foreach($depencies AS $name) {
				$output[$name] = $input[$name];
			}
			
			return $output;
		}
		
		public function getHeaderStylesheets(bool $depency_order = true) : array {
			if($depency_order) {
				return $this->sortDepencies($this->header['stylesheets']);
			}
			
			return $this->header['stylesheets'];
		}
		
		public function getFooterStylesheets(bool $depency_order = true) : array {
			if($depency_order) {
				return $this->sortDepencies($this->footer['stylesheets']);
			}
			
			return $this->footer['stylesheets'];
		}
		
		public function getHeaderJavascripts(bool $depency_order = true) : array {
			if($depency_order) {
				return $this->sortDepencies($this->header['javascripts']);
			}
			
			return $this->header['javascripts'];
		}
		
		public function getFooterJavascripts(bool $depency_order = true) : array {
			if($depency_order) {
				return $this->sortDepencies($this->footer['javascripts']);
			}
			
			return $this->footer['javascripts'];
		}
		
		public function getFooter() : array {
			return $this->footer;
		}
	}
?>