<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */
	namespace fruithost\Services;
	
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	use fruithost\Security\Encryption;
	use fruithost\System\Utils;
	
	class PHP {
		private ?string $path    = null;
		private ?string $socket  = null;
		private ?string $content = null;
		private array   $data    = [];
		private bool    $error   = false;
		
		public function __construct() {
			$ini     = null;
			$version = sprintf('%d.%d', PHP_MAJOR_VERSION, PHP_MINOR_VERSION);
			try {
				# Ubuntu, Debian			
				if(file_exists(sprintf('/etc/php/%s/fpm/pool.d/www.conf', $version))) {
					$ini = parse_ini_file(sprintf('/etc/php/%s/fpm/pool.d/www.conf', $version), true);
					# CentOS, RHEL, Fedora
				} else if(file_exists('/etc/php-fpm.d/www.conf')) {
					$ini = parse_ini_file('/etc/php-fpm.d/www.conf', true);
				}
			} catch(\Exception $e) {
			}
			if(!empty($ini)) {
				if(isset($ini['www'])) {
					if(isset($ini['www']['listen'])) {
						$this->socket = $ini['www']['listen'];
					}
				}
			}
			if(!empty(shell_exec('which cgi-fcgi')) && $this->socket == null) {
				$this->error = true;
			}
		}
		
		public function setPath(string $path) : void {
			$this->path = $path;
		}
		
		public function getHeader() : ?string {
			return explode("\r\n\r\n", $this->content)[0];
		}
		
		public function getBody() : ?string {
			if(preg_match("/\r\n\r\n/Uis", $this->content)) {
				return explode("\r\n\r\n", $this->content)[1];
			} else if(preg_match("/(cgi\-fcgi|command not found)/Uis", $this->content)) {
				throw new \Exception('Can\'t access cgi-fcgi.');
			} else {
				return $this->content;
			}
		}
		
		public function getContent() : ?string {
			return $this->content;
		}
		
		public function isAvailable() : bool {
			return !empty(shell_exec('which cgi-fcgi'));
		}
		
		public function hasErrors() : bool {
			return $this->error;
		}
		
		public function getInfo() : array {
			$file = sprintf('.fruithost.%s.php', Utils::randomString(5));
			$hash = Encryption::encrypt(Utils::randomString(28), ENCRYPTION_SALT);
			file_put_contents(sprintf('%s%s', $this->path, $file), sprintf('<?php $name = \'FRUITHOST\'; $hash = \'%s\'; if((isset($_SERVER[$name]) && $_SERVER[$name] == $hash) || (isset($_GET[$name]) && $_GET[$name] == $hash)) { phpinfo(); } ?>', $hash));
			$this->execute($file, [ 'FRUITHOST' => $hash, 'USERNAME' => Auth::getUsername() ]);
			@unlink(sprintf('%s%s', $this->path, $file));
			$this->parse();
			
			return $this->data;
		}
		
		public function execute(string $file, array $arguments = [], ?string $password = null) : void {
			if(!is_writable($this->path)) {
				$this->error = true;
				
				return;
			}
			$args = '';
			foreach(array_merge([ 'SCRIPTS_DIR' => $this->path, 'HOME' => $this->path, 'PWD' => $this->path, 'USER' => 'www-data', 'DOCUMENT_ROOT' => $this->path, 'SCRIPT_FILENAME' => sprintf('%s%s', $this->path, $file), 'REQUEST_METHOD' => 'GET' ], $arguments) as $name => $value) {
				$args .= sprintf('%s=%s \\%s', $name, $value, PHP_EOL);
			}
			if(!array_key_exists('FRUITHOST', $arguments)) {
				$arguments['FRUITHOST'] = Encryption::encrypt(Utils::randomString(28), ENCRYPTION_SALT);
			}
			// Check if cgi-fcgi available
			if(!empty(shell_exec('which cgi-fcgi'))) {
				$command = sprintf('%scgi-fcgi -bind -connect "%s" 2>&1', $args, $this->socket);
				if($password != null) {
					$command = sprintf('bash -lc \'echo %s | /usr/bin/sudo -S %s\'', $password, $command);
				}
				$result = shell_exec($command);
				if($result === null) {
					$this->error = true;
					
					return;
				}
				$this->content = $result;
				// If cgi-fcgi not available, use a polyfill with CURL
			} else {
				// @ToDo Check security(!)
				$additional = '';
				if(array_key_exists('MODULE', $arguments)) {
					$additional = sprintf('require_once(\'%s\'); ', $arguments['MODULE']);
				}
				$temp    = sprintf('.fruithost.check.%s.php', Utils::randomString(5));
				$token   = Encryption::encrypt(Utils::randomString(28), ENCRYPTION_SALT);
				$content = sprintf('<'.'?php define(\'DAEMON\', true); $wrapper_name = \'TOKEN\'; $wrapper_hash = \'%1$s\'; if((isset($_SERVER[$wrapper_name]) && $_SERVER[$wrapper_name] == $wrapper_hash) || (isset($_GET[$wrapper_name]) && $_GET[$wrapper_name] == $wrapper_hash)) { require_once(\'%2$s\'); %3$s } ?>', $token, sprintf('%s%s', $this->path, $file), $additional);
				$ch      = curl_init();
				file_put_contents(sprintf('%s%s', PATH, $temp), $content);
				// @ToDo Domain = $_SERVER['SERVER_NAME']d
				curl_setopt($ch, CURLOPT_URL, sprintf('https://%s/%s?TOKEN=%s&FRUITHOST=%s&USERNAME=%s', $_SERVER['SERVER_NAME'], $temp, $token, $arguments['FRUITHOST'], Auth::getUsername()));
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Cookie: '.$_SERVER['HTTP_COOKIE'] ]);
				$result = curl_exec($ch);
				curl_close($ch);
				$this->content = $result;
				@unlink(sprintf('%s%s', PATH, $temp));
			}
		}
		
		public function parse() : void {
			if($this->error) {
				return;
			}
			$entitiesToUtf8 = function($input) {
				// http://php.net/manual/en/function.html-entity-decode.php#104617
				return preg_replace_callback("/(&#[0-9]+;)/", function($m) {
					return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
				}, $input);
			};
			$plainText      = function($input) use ($entitiesToUtf8) {
				return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
			};
			$titlePlainText = function($input) use ($plainText) {
				return '# '.$plainText($input);
			};
			if(!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', $this->content, $matches)) {
				return;
			}
			preg_match_all('/src="data:image\/png;base64,([^"]+)" alt="PHP logo"/', $this->content, $logo);
			$php_logo = (!empty($logo[1][0]) ? $logo[1][0] : null);
			preg_match_all('/src="data:image\/png;base64,([^"]+)" alt="Zend logo"/', $this->content, $logo);
			$zend_logo = (!empty($logo[1][0]) ? $logo[1][0] : null);
			$phpinfo   = [ 'info' => [] ];
			$input     = $matches[1];
			$matches   = [];
			if(preg_match_all('#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.'(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $input, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$fn = !str_contains($match[0], '<th') ? $plainText : $titlePlainText;
					if($match[1] !== '') {
						$phpinfo[$match[1]] = [];
					} else if(isset($match[3])) {
						$keys1                                = array_keys($phpinfo);
						$phpinfo[end($keys1)][$fn($match[2])] = $this->exploding($fn($match[2]), isset($match[4]) ? [ $fn($match[3]), $fn($match[4]) ] : $fn($match[3]));
					} else {
						$keys1                  = array_keys($phpinfo);
						$phpinfo[end($keys1)][] = $this->exploding($keys1, $fn($match[2]));
					}
				}
			}
			$this->data         = $phpinfo;
			$this->data['logo'] = [ 'PHP' => $php_logo, 'Zend' => $zend_logo ];
		}
		
		public function exploding(string | array $name, mixed $data) : array | string {
			if($name == 'Configuration File (php.ini) Path') {
				return $this->highlight($this->path);
			}
			if($name == 'Loaded Configuration File') {
				return [ $data, sprintf('%sphp.ini', $this->path) ];
			}
			if(in_array($name, [ 'Additional .ini files parsed', 'Registered PHP Streams', 'Registered Stream Socket Transports', 'Registered Stream Filters', 'disable_functions' ])) {
				if(is_array($data)) {
					foreach($data as $key => $value) {
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
				foreach($data as $name => $value) {
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
				case 'no value':
					return sprintf('<i class="text-black-50">%s</i>', I18N::get($data));
			}
			if(preg_match('/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?/', $data)) {
				return sprintf('<i style="color: %1$s;">%1$s</i>', $data);
			}
			if(is_numeric($data)) {
				return sprintf('<i class="text-warning">%s</i>', $data);
			}
			// $data[0] gives hier Errors!
			if(substr($data, 0, 1) === '/' || str_starts_with($data, '.:/')) {
				return sprintf('<span class="badge badge-secondary">%s</span>', $data);
			}
			
			return $data;
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