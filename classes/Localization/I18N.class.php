<?php
    namespace fruithost\Localization;
    use fruithost\Accounting\Auth;
    use fruithost\Network\Request;
    use fruithost\Storage\Database;
    use Sepia\PoParser\Parser;
    use Sepia\PoParser\SourceHandler\FileSystem;
    use Sepia\PoParser\Catalog\CatalogArray;

    class I18N {
		protected static ?CatalogArray $translation	= null;
		protected static array $languages		= [
			'en_US' => 'English'
		];
		
		public function __construct() {
			self::loadLanguages();
			self::load(self::set());
		}
		
		public static function addPath($path) : void {
			$file					= sprintf('%s%s.po', $path, self::set());
			
			if(file_exists($file)) {
				$language			= new Parser(new FileSystem($file));
				self::$translation	= $language->parse(self::$translation);
			}
		}
		
		public static function reload() : void {
			self::load(self::set());			
		}
		
		/* @ToDo Remove, its core-method, but must be added for the Daemon. Check if we can remove these now */
		protected static function getSettings(string $name, mixed $default = null) : mixed {
			$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> $name
			]);
			
			if(!empty($result)) {
				// Is Boolean: False
				if(in_array(strtolower($result->value), [
					'off', 'false', 'no'
				])) {
					return false;
				// Is Boolean: True
				} else if(in_array(strtolower($result->value), [
					'on', 'true', 'yes'
				])) {
					return true;
				} else if(!empty($result->value)) {
					return $result->value;
				}
			}
			
			return $default;
		}
		
		public static function set() : string {
			$language = Auth::getSettings('LANGUAGE', null, self::getSettings('LANGUAGE', 'en_US'));
			
			if(!Auth::isLoggedIn() && Request::has('lang')) {
				$language = Request::get('lang');
			}
			
			return $language;
		}
		
		protected static function load(string $language) : void {
			$file				= sprintf('%slanguages/%s.po', PATH, $language);
			
			if(file_exists($file)) {
				$language			= new Parser(new FileSystem($file));
				self::$translation	= $language->parse();
			} else {
				self::$translation	= null;
			}
		}
		
		protected static function loadLanguages() : void {
			foreach(new \DirectoryIterator(sprintf('%slanguages/', PATH)) AS $info) {
				if($info->isDot()) {
					continue;
				}
				
				if(preg_match('/(.*)\.po$/Uis', $info->getFileName(), $matches)) {
					$language		= new Parser(new FileSystem($info->getPathName()));
					$parsed			= $language->parse();
					$header			= $parsed->getHeader();
					
					foreach($header->asArray() AS $line) {
						if(preg_match('/Language: (.*)$/Uis', $line, $names)) {
							self::$languages[$matches[1]] = $names[1];
							break;
						}
					}
				}
			}
		}
		
		public static function getLanguages() : array {
			return self::$languages;
		}
		
		public static function __($string) : void {
			print self::get($string);
		}
		
		public static function get($string) : string {
			if(self::$translation == null) {
				return $string;
			}
			
			$table = self::$translation->getEntry($string);
			
			if($table == null) {
				return $string;
			}
			
			if(empty($table->getMsgStr())) {
				return $string;
			}
			
			return $table->getMsgStr();
		}
	}
	
	new I18N();
?>