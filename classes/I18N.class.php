<?php
	namespace fruithost;
	use \fruithost\Auth;
	use \Sepia\PoParser\SourceHandler\FileSystem;
	use \Sepia\PoParser\Parser;

	class I18N {
		protected static $translation	= null;
		protected static $languages		= [
			'en_US' => 'English'
		];
		
		public function __construct() {
			self::loadLanguages();
			self::load(Auth::getSettings('LANGUAGE', NULL, 'en_US'));
		}
		
		public static function reload() {
			self::load(Auth::getSettings('LANGUAGE', NULL, 'en_US'));			
		}
		
		protected static function load($language) {
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
		
		public static function getLanguages() {
			return self::$languages;
		}
		
		public static function __($string) {
			print self::get($string);
		}
		
		public static function get($string) {
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