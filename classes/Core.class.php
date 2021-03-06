<?php
	namespace fruithost;
	
	class Core {
		private $modules	= null;
		private $hooks		= null;
		private $router		= null;
		private $template	= null;
		private $admin		= null;
		public function __construct() {
			spl_autoload_register([ $this, 'load' ]);
			
			$this->init();
		}
		
		public function load($class) {
			$this->require('.security');
			$this->require('config');
			
			$file			= trim($class, BS);
			$file_array		= explode(BS, $file);
			array_shift($file_array);
			array_unshift($file_array, 'classes');
			
			$path			= sprintf('%s%s.class.php', PATH, implode(DS, $file_array));

			if(!file_exists($path)) {
				// Check it's an Library
				$file_array		= explode(BS, $file);
				array_unshift($file_array, 'libraries');
				$path	= sprintf('%s%s.php', PATH, implode(DS, $file_array));
				
				if(file_exists($path)) {
					require_once($path);
					return;
				}
				// Check it's an Library on an module!
				/*} else if(preg_match('/\/module\//Uis', $_SERVER['REQUEST_URI'])) {
					$file_array		= explode(BS, $file);
					array_unshift($file_array, 'www');
					array_unshift($file_array, str_replace('module', 'modules', $_SERVER['REQUEST_URI']));
					$path	= sprintf('%s%s.php', dirname(PATH), implode(DS, $file_array));
					require_once($path);
					return;
				}*/
				
				print 'Error Loading: ' . $path;
				return;
			}
			
			require_once($path);
		}
		
		private function require($file) {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				print 'ERROR loading: ' . $path;
				return;
			}
			
			require_once($path);
		}
		
		public function getModules() {
			return $this->modules;
		}
		
		public function getHooks() {
			return $this->hooks;
		}
		
		public function getTemplate() {
			return $this->template;
		}
		
		public function getRouter() {
			return $this->router;
		}
		
		public function getSettings($name, $default = NULL) {
			$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> $name
			]);
			
			if(!empty($result)) {
				if(!empty($result->value)) {
					return $result->value;
				}
			}
			
			return $default;
		}
		
		public function setSettings($name, $value = NULL) {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> $name
			])) {
				Database::update(DATABASE_PREFIX . 'settings', [ 'key' ], [
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert(DATABASE_PREFIX . 'settings', [
					'id'			=> NULL,
					'key'			=> $name,
					'value'			=> $value
				]);
			}
		}
		
		private function init() {
			Request::init();
			
			$this->hooks	= new Hooks();
			$this->modules	= new Modules($this);
			$this->template	= new Template($this);
			$this->router	= new Router($this);
			$this->admin	= new CoreAdmin($this, NULL);
			
			$this->router->addRoute('/', function() {
				if(Auth::isLoggedIn()) {
					Response::redirect('/overview');
				}
				
				Response::redirect('/login');
			});
			
			$this->router->addRoute('/logout', function() {
				if(Auth::isLoggedIn()) {
					Auth::logout();
				}

				Response::redirect('/');
			});
			
			$this->router->addRoute('/overview', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}

				$this->template->display('overview');
			});
			
			$this->router->addRoute('^/settings(?:/([a-zA-Z0-9\-_]+))?$', function($tab = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$languages = [
					'en_US' => 'English'
				];
				
				foreach(new \DirectoryIterator(sprintf('%slanguages/', PATH)) AS $info) {
					if($info->isDot()) {
						continue;
					}
					
					if(preg_match('/(.*)\.po$/Uis', $info->getFileName(), $matches)) {
						$language		= new \Sepia\PoParser\Parser(new \Sepia\PoParser\SourceHandler\FileSystem($info->getPathName()));
						$parsed			= $language->parse();
						$header			= $parsed->getHeader();
						
						foreach($header->asArray() AS $line) {
							if(preg_match('/Language: (.*)$/Uis', $line, $names)) {
								$languages[$matches[1]] = $names[1];
								break;
							}
						}
					}
				}
				
				$this->template->display('settings', [
					'tab'		=> $tab,
					'languages'	=> $languages,
					'timezones'	=> json_decode(file_get_contents(dirname(PATH) . '/config/timezones.json'))
				]);
			});
			
			$this->router->addRoute('^/account(?:/([a-zA-Z0-9\-_]+))?$', function($tab = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}

				$data = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id'	=> Auth::getID()
				]);
				
				foreach($data AS $index => $entry) {
					if(in_array($index, [ 'id', 'user_id' ])) {
						continue;
					}
					
					$data->{$index} = Encryption::decrypt($entry, ENCRYPTION_SALT);
				}
				
				$this->template->display('account', [
					'tab'	=> $tab,
					'data'	=> $data
				]);
			});
			
			$this->router->addRoute('^/module(?:/([a-zA-Z0-9\-_]+)(?:/([a-zA-Z0-9\-_]+))?)?$', function($module = null, $submodule = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				if(empty($module)) {
					$this->template->display('error/module', [
						'module'	=> $module,
						'submodule'	=> $submodule
					]);
					return;
				}

				$module = $this->getModules()->getModule($module);
				
				if(empty($module)) {
					$this->template->display('error/module', [
						'module'	=> $module,
						'submodule'	=> $submodule
					]);
					return;
				}
				
				if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($module->getInstance(), 'onPOST')) {
					$data = [];
					
					foreach($_POST AS $name => $value) {
						if(is_array($value)) {
							$data[trim($name)] = [];
							
							foreach($value AS $key => $entry) {
								$data[trim($name)][trim($key)] = trim($entry);
							}
						} else {
							$data[trim($name)] = trim($value);
						}
					}
					
					$module->getInstance()->onPOST($data);
				}
				
				if(method_exists($module->getInstance(), 'load')) {
					$module->getInstance()->load($submodule);
				}
				
				if(!method_exists($module->getInstance(), 'content') && !method_exists($module->getInstance(), 'frame')) {
					$this->template->display('error/module_empty', [
						'module'	=> $module,
						'submodule'	=> $submodule
					]);
					return;
				}
				
				$this->template->display('module', [
					'module'	=> $module,
					'submodule'	=> $submodule
				]);
			});
			
			$this->router->addRoute('/login', function() {
				if(Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->template->display('login');
			});
			
			$this->router->addRoute('/ajax', function() {
				if(!Auth::isLoggedIn()) {
					header('HTTP/1.1 403 Forbidden');
					require_once(dirname(PATH) . '/placeholder/errors/403.php');
					exit();
				}
				
				if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
					header('HTTP/1.1 405 Method Not Allowed');
					require_once(dirname(PATH) . '/placeholder/errors/405.php');
					exit();
				}
				
				// Fire modals
				if(isset($_POST['modal']) && !empty($_POST['modal'])) {
					$modals = $this->getHooks()->applyFilter('modals', []);
			
					foreach($modals AS $modal) {
						if($modal->getName() === $_POST['modal']) {
							$callback	= $modal->getCallback('save');
							$data		= [];
							
							foreach($_POST AS $name => $value) {
								if(is_array($value)) {
									$data[trim($name)] = [];
									
									foreach($value AS $key => $entry) {
										$data[trim($name)][trim($key)] = trim($entry);
									}
								} else {
									$data[trim($name)] = trim($value);
								}
							}
							
							$result		= call_user_func_array($callback, [ $data ]);
							
							if(is_bool($result)) {
								print json_encode($result);
								return;
							}
							
							print $result;
						}
					}
				}
			});

			$this->router->run();
		}
	}
?>