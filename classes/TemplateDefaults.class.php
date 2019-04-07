<?php
	namespace fruithost;
	
	class TemplateDefaults {
		public function head_robots() {
			printf('<meta name="robots" content="%s" />', $this->getCore()->getHooks()->applyFilter('meta_robots', 'noindex,follow'));
		}
		
		public function head_scripts() {
			$loaded = [];
			
			foreach($this->getFiles()->getHeaderStylesheets() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (strpos($entry->file, '?') === false ? '?t=' . time() . '&' : '&t=' . time() . '&'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getHeaderJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
		
		public function foot_scripts() {
			$loaded = [];
			
			foreach($this->getFiles()->getFooterStylesheets() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getFooterJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
	}
?>