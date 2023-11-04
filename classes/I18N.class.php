<?php
	namespace fruithost;
	use \fruithost\Request;
	use \fruithost\Auth;
	use \fruithost\Database;
	use \Sepia\PoParser\SourceHandler\FileSystem;
	use \Sepia\PoParser\Parser;

	class I18N {
		protected static $translation	= null;
		protected static $languages		= [
			'en_US' => 'English'
		];
		
		public function __construct() {
			self::loadLanguages();
			self::load(self::set());
		}
		
		public static function reload() {
			self::load(self::set());			
		}
		
		protected static function getSettings(string $name, mixed $default = NULL) : mixed {
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
		
		protected static function set() : string {
			$language = Auth::getSettings('LANGUAGE', NULL, self::getSettings('LANGUAGE', 'en_US'));
			
			if(!Auth::isLoggedIn() && Request::has('lang')) {
				$language = Request::get('lang');
			}
			
			return $language;
		}
		
		protected static function load(string $language) {
			$file				= sprintf('%slanguages/%s.po', PATH, $language);
			
			if(file_exists($file)) {
				$language			= new Parser(new FileSystem($file));
				self::$translation	= $language->parse();
			} else {
				self::$translation	= null;
			}
		}
		
		protected static function loadLanguages() {
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
		
		public static function __($string) {
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