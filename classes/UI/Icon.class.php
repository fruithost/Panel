<?php
	namespace fruithost\UI;
	
	use fruithost\System\Core;

    class Icon {
		private static ?Core $core			= null;
		private static ?object $definition	= null;
		
		public static function init(Core $core) {
			self::$core = $core;
			self::load();
		}
		
		public static function reload() : void {
			self::load();			
		}
		
		public static function load() : void {
			$path		= sprintf('%1$s%2$sthemes%2$s%3$s%2$sicons.json', dirname(PATH), DS, self::$core->getTemplate()->getTheme());
			$content	= null;
			
			if(file_exists($path) && is_readable($path)) {
				$content = file_get_contents($path);
			} else {
				$path = sprintf('%1$s%2$sdefault%2$sicons.json', PATH, DS);
				
				if(file_exists($path) && is_readable($path)) {
					$content = file_get_contents($path);
				}
			}
			
			if(empty($content)) {
				throw new \Exception('Can\'t load icon definitions: ' . $path);
			}
			
			$json = json_decode($content, false);
		
			if(json_last_error() !== JSON_ERROR_NONE) {
				throw new \Exception('Can\'t parse icon JSON definitions: ' . $path);
			}
			
			self::$definition = $json;
		}
		
		public static function render(string $icon, ?array $options = []) : string {
			$json		= self::$definition;
			$icons		= $json->definitions;
			$template 	= $json->template;
			$classes	= empty($template->classes) ? [] : array_merge([], $template->classes);
			$attributes	= empty($template->attributes) ? [] : array_merge([], $template->attributes);
			$attr		= [];
			
			if(!empty($options['classes'])) {
				$classes	= array_merge($classes, $options['classes']);
			}
			
			if(!empty($options['attributes'])) {
				$attributes	= array_merge($attributes, $options['attributes']);
			}
			
			foreach($attributes AS $name => $value) {
				if(is_bool($value)) {
					$value = $value ? 'true' : 'false';
				}
				
				$attr[] = sprintf('%s="%s"', $name, $value);
			}
			
			if(!empty($icons->{$icon})) {
				switch($template->content->type) {
					case 'class':
						$classes[] = sprintf($template->content->html, $icons->{$icon});
					break;
					default:
						throw new \Exception('Unknown icon definition: ' . $template->content->type);
					break;
				}
				
				return sprintf(self::$core->getHooks()->applyFilter('icons_html', '<%1$s class="%2$s" %3$s></%1$s>'), $template->element, implode(' ', $classes), implode(' ', $attr));
			}
			
			return sprintf('[Icon %s]', $icon);
		}
		
		public static function get(string $icon) : string {
			$json		= self::$definition;
			$icons		= $json->definitions;
			$template 	= $json->template;
			$class		= [];
			
			if(!empty($icons->{$icon})) {
				switch($template->content->type) {
					case 'class':
						$class = sprintf($template->content->html, $icons->{$icon});
					break;
					default:
						throw new \Exception('Unknown icon definition: ' . $template->content->type);
					break;
				}
				
				return $class;
			}
			
			return sprintf('[Icon %s]', $icon);
		}
		
		public static function show(string $name, ?array $options = []) : void {
			print self::render($name, $options);
		}
	}
?>