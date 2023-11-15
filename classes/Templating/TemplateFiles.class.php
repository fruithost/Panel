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
		
		private array $footer	= [
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
            $sorted = [];

            while($count = count($input)) {
                foreach($input AS $name => $script) {
                    if(isset($script->depencies)) {
                        foreach($script->depencies as $index => $depency) {
                            if(isset($sorted[$depency])) {
                                unset($script->depencies[$index]);
                            }
                        }

                        if(!count($script->depencies)) {
                            unset($script->depencies);
                        }
                    }
                }

                foreach($input AS $name => $script) {
                    if(!isset($script->depencies)) {
                        $sorted[$script->name] = $script;
                        unset($input[$name]);
                    }
                }

                /*if(count($input) == $count) {
                    die("Unresolvable dependency");
                }*/
            }

            return $sorted;
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