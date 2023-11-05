<?php
	namespace fruithost;
	
	use fruithost\Encryption;
	use fruithost\Utils;
	
	class PHP {
		private $path		= null;
		private $socket		= '/run/php/php8.2-fpm.sock';
		private $content	= null;
		private $data		= [];
		private $error		= false;
		
		public function setPath(string $path) {
			$this->path = $path;
		}
		
		public function execute() {
			if(!is_writable($this->path)) {
				$this->error = true;
				return;
			}
			
			$args	= '';
			$file	= sprintf('.fruithost.%s.php', Utils::randomString(5));
			$hash	= Encryption::encrypt(Utils::randomString(28), ENCRYPTION_SALT);
			
			file_put_contents(sprintf('%s%s', $this->path, $file), sprintf('<?php if(isset($_SERVER[\'FRUITHOST\']) && $_SERVER[\'FRUITHOST\'] == \'%s\') { phpinfo(); } ?>', $hash));
			
			foreach([
				'FRUITHOST'			=> $hash,
				'SCRIPTS_DIR'		=> $this->path,
				'HOME'				=> $this->path,
				'PWD'				=> $this->path,
				'DOCUMENT_ROOT'		=> $this->path,
				'SCRIPT_FILENAME'	=> sprintf('%s%s', $this->path, $file),
				'REQUEST_METHOD'	=> 'GET'
			] AS $name => $value) {
				$args .= sprintf('%s=%s \\%s', $name, $value, PHP_EOL);
			}
			
			$result 	= shell_exec(sprintf('%scgi-fcgi -bind -connect "%s" 2>&1', $args, $this->socket));
			@unlink(sprintf('%s%s', $this->path, $file));
			
			$this->content = $result;
		}
		
		public function exploding(string | array $name, mixed $data) : array | string {
			if($name == 'Configuration File (php.ini) Path') {
				return $this->highlight($this->path);
			}
			
			if($name == 'Loaded Configuration File') {
				return [ $data, sprintf('%sphp.ini', $this->path) ];
			}
			
			if(in_array($name, [
				'Additional .ini files parsed',
				'Registered PHP Streams',
				'Registered Stream Socket Transports',
				'Registered Stream Filters',
				'disable_functions'
			])) {
				if(is_array($data)) {
					foreach($data AS $key => $value) {
						$data[$key] = explode(',', $value);
					}
				} else {
					return explode(',', $data);
				}
			}
			
			return $this->highlight($data);
		}
		
		public function highlight(array | string $data) : array | string {
			if(is_array($data)) {
				foreach($data AS $name => $value) {
					$data[$name] = $this->highlight($value);
				}
				
				return $data;
			}
			
			switch(strtolower($data)) {
				case 'disabled':
				case 'active':
				case 'enabled':
				case 'off':
				case 'on':
				case 'no':
				case 'yes':
					return sprintf('<strong class="text-info">%s</strong>', I18N::get($data));
				break;
				case 'no value':
					return sprintf('<i class="text-black-50">%s</i>', I18N::get($data));
				break;
			}
			
			if(preg_match('/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?/', $data)) {
				return sprintf('<i style="color: %1$s;">%1$s</i>', $data);				
			}
			
			if(is_numeric($data)) {
				return sprintf('<i class="text-warning">%s</i>', $data);				
			}
			
			if(substr($data, 0, 1) == '/' || substr($data, 0, 3) == '.:/') {
				return sprintf('<span class="badge badge-secondary">%s</span>', $data);
			}
			
			return $data;
		}
		
		public function hasErrors() : bool {
			return $this->error;
		}
		
		public function parse() {
			if($this->error) {
				return;
			}
			
			$entitiesToUtf8 = function($input) {
				// http://php.net/manual/en/function.html-entity-decode.php#104617
				return preg_replace_callback("/(&#[0-9]+;)/", function($m) {
					return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
				}, $input);
			};
			$plainText = function($input) use ($entitiesToUtf8) {
				return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
			};
			
			$titlePlainText = function($input) use ($plainText) {
				return '# '.$plainText($input);
			};
			
			if(!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', $this->content, $matches)) {
				return;
			}
			
			$php_logo	= null;
			$zend_logo	= null;
			preg_match_all('/src="data:image\/png;base64,([^"]+)" alt="PHP logo"/', $this->content, $logo);
			$php_logo	= $logo[1][0];
			preg_match_all('/src="data:image\/png;base64,([^"]+)" alt="Zend logo"/', $this->content, $logo);
			$zend_logo	= $logo[1][0];
			
			$phpinfo 	= [ 'info' => [] ];
			$input		= $matches[1];
			$matches	= [];

			if(preg_match_all(
				'#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.
				'(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
				$input, 
				$matches, 
				PREG_SET_ORDER
			)) {
				foreach($matches as $match) {
					$fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
					
					if(strlen($match[1])) {
						$phpinfo[$match[1]]						= [];
					} else if(isset($match[3])) {
						$keys1 									= array_keys($phpinfo);
						$phpinfo[end($keys1)][$fn($match[2])]	= $this->exploding($fn($match[2]), isset($match[4]) ? [ $fn($match[3]), $fn($match[4]) ] : $fn($match[3]));
					} else {
						$keys1									= array_keys($phpinfo);
						$phpinfo[end($keys1)][]					= $this->exploding($keys1, $fn($match[2]));
					}

				}
			}
			
			$this->data			= $phpinfo;
			$this->data['logo']	= [
				'PHP' 	=> $php_logo,
				'Zend' 	=> $zend_logo
			];
		}
		
		public function getInfo() : array {			
			$this->execute();
			$this->parse();
			
			return $this->data;
		}
	}
	
	/*
		Provide dynamical I18N language strings, do not remove!
		
		I18N::__('disabled')
		I18N::__('active')
		I18N::__('enabled')
		I18N::__('off')
		I18N::__('on')
		I18N::__('no')
		I18N::__('yes')
		I18N::__('no value')
	*/
?>