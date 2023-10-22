<?php
	namespace fruithost;
	use \fruithost\Auth;
	
	class I18N {
		public function __construct() {
			$language 	= 'de_DE'; //Auth::getSettings('LANGUAGE', NULL, 'en_US');
			$domain		= 'fruithost';
			
			putenv('LC_ALL=de_DE.UTF-8');
			setlocale(LC_ALL, 'de_DE.UTF-8');
			
			bindtextdomain($domain, PATH . 'languages/.nocache'); 
			bindtextdomain($domain, PATH . 'languages'); 
			bind_textdomain_codeset($domain, 'UTF-8');
			textdomain($domain);
		}
		
		public static function __($string) {
			print gettext($string);
		}
	}
	
	new I18N();
?>