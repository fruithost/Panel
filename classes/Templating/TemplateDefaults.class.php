<?php
	namespace fruithost\Templating;
	
	class TemplateDefaults {
		public function head_robots() : void {
			printf('<meta name="robots" content="%s" />', $this->getCore()->getHooks()->applyFilter('meta_robots', 'noindex,follow', 10, false));
		}
		
		public function favicon() : void {
			$base64 = 'data:image/png;base64,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';
			$entrys = [
				'32'		=> sprintf('<link rel="icon" href="%s" sizes="32x32" />', $base64),
				'192'		=> sprintf('<link rel="icon" href="%s" sizes="192x192" />', $base64),
				'apple'		=> sprintf('<link rel="apple-touch-icon-precomposed" href="%s" />', $base64),
				'microsoft'	=> sprintf('<meta name="msapplication-TileImage" content="%s" />', $base64)
			];
			
			print implode('', $this->getCore()->getHooks()->applyFilter('favicons', $entrys));
		}
		
		public function theme_color() : void {
			printf('<meta name="theme-color" content="%s" />', $this->getCore()->getHooks()->applyFilter('theme_color', '#007BFF', 10, false));
		}
		
		public function head_scripts() : void {
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
				
				if(!in_array($name, $loaded, true)) {					
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (defined('DEBUG') && DEBUG ? (strpos($entry->file, '?') === false ? '?t=' . time() . '&' : '&t=' . time() . '&') : '?'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getHeaderJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!in_array($needed, $loaded, true)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded, true)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (defined('DEBUG') && DEBUG ? (strpos($entry->file, '?') === false ? '?t=' . time() . '&' : '&t=' . time() . '&') : '?'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
		
		public function foot_modals() : void {
            $template	= $this;
            $modals		= $this->getCore()->getHooks()->applyFilter('modals', [], 10, false);
            $path		= sprintf('%1$s%2$sthemes%2$s%4$s%2$s%3$s.php', dirname(PATH), DS, 'modal', $template->getTheme());

            foreach($template->getAssigns() AS $name => $value) {
                ${$name} = $value;
            }

            if(file_exists($path)) {
                require_once($path);
            } else {
                $path = sprintf('%1$s%2$sdefault%2$s%3$s.php', PATH, DS, 'modal');

                if(file_exists($path)) {
                    @require_once($path);
                }
            }
		}
		
		public function foot_scripts() : void {
			$loaded = [];
			
			foreach($this->getFiles()->getFooterStylesheets() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!in_array($needed, $loaded, true)) {
							$continue = false;
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded, true)) {
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (defined('DEBUG') && DEBUG ? (strpos($entry->file, '?') === false ? '?' : '&') : '?'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getFooterJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!in_array($needed, $loaded, true)) {
							$continue = false;
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded, true)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (defined('DEBUG') && DEBUG ? (strpos($entry->file, '?') === false ? '?' : '&') : '?'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
	}
?>