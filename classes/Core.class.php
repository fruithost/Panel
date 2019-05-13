<?php
	namespace fruithost;
	
	class Core {
		private $modules	= null;
		private $hooks		= null;
		private $router		= null;
		private $template	= null;
		
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
		
		private function init() {
			Request::init();
			
			$this->hooks	= new Hooks();
			$this->modules	= new Modules($this);
			$this->template	= new Template($this);
			$this->router	= new Router($this);
			
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
					$module->getInstance()->onPOST($_POST);
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
			
			
			$this->router->addRoute('/admin', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->template->display('admin');
			});
			
			$this->router->addRoute('^/admin(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function($destination = null, $tab = NULL) {
				$data = [
					'tab'	=> $tab
				];
				
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				switch($destination) {
					case 'server':
						$uname			= explode(' ', shell_exec('uname -a'));

						$uptime_array	= explode(" ", exec("cat /proc/uptime"));
						$seconds		= round($uptime_array[0], 0);
						$minutes		= $seconds / 60;
						$hours			= $minutes / 60;
						$days			= floor($hours / 24);
						$hours			= sprintf('%02d', floor($hours - ($days * 24)));
						$minutes		= sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
						
						if ($days == 0) {
							$uptime = $hours . ":" .  $minutes . " (hh:mm)";
						} elseif($days == 1) {
							$uptime = $days . " day, " .  $hours . ":" .  $minutes . " (hh:mm)";
						} else {
							$uptime = $days . " days, " .  $hours . ":" .  $minutes . " (hh:mm)";
						}

						$memory		= [];
						$meminfo	= file('/proc/meminfo', \FILE_SKIP_EMPTY_LINES);
						
						foreach($meminfo AS $line) {
							preg_match('/(?<name>[a-zA-Z]+):([\s]+)(?<value>[0-9]+)\s(?<size>[a-zA-Z]+)$/Uis', $line, $matches);
							
							if(!empty($matches['name'])) {
								switch(trim($matches['name'])) {
									case 'MemTotal':	$memory['total'] = $matches['value'];		break;
									case 'MemFree':		$memory['free'] = $matches['value'];		break;
									case 'SwapTotal':	$memory['total_swap'] = $matches['value'];	break;
									case 'SwapFree':	$memory['free_swap'] = $matches['value'];	break;
									case 'Buffers':		$memory['buffer'] = $matches['value'];		break;
									case 'Cached':		$memory['cache'] = $matches['value'];		break;
									default: break;
								}
							}
						}

						$disks		= [];
						$output		= shell_exec('df -T -h');
						$search		= [ 'G', 'M' ];
						$replace	= [ ' GB', ' MB' ];
						
						foreach(explode(PHP_EOL, $output) AS $index => $line) {
							if($index === 0 || empty(trim($line))) {
								continue;
							}
							
							preg_match('/^(?P<filesystem>[\da-zA-Z\/]+)\s+(?P<type>[\da-zA-Z\/]+)\s+(?P<size>[0-9A-Z\.]+)\s+(?P<used>[0-9A-Z\.]+)\s+(?P<avail>[0-9A-Z\.]+)\s+(?P<percent>\d+%)\s+(?P<mount>[\da-zA-Z\/]+)$/', $line, $matches);
							
							$disks[] = [
								'filesystem'	=> (isset($matches['filesystem']) ? $matches['filesystem'] : NULL),
								'type'			=> (isset($matches['type']) ? $matches['type'] : NULL),
								'size'			=> (isset($matches['size']) ? str_replace($search, $replace, $matches['size']) : NULL),
								'used'			=> (isset($matches['used']) ? str_replace($search, $replace, $matches['used']) : NULL),
								'avail'			=> (isset($matches['avail']) ? str_replace($search, $replace, $matches['avail']) : NULL),
								'percent'		=> (isset($matches['percent']) ? $matches['percent'] : NULL),
								'mount'			=> (isset($matches['mount']) ? $matches['mount'] : NULL)
							];
						}
						
						$data['hostname']		= exec('hostname -f');
						$data['time_system']	= exec('date +\'%d %b %Y %T %Z\'');
						$data['time_php']		= date('d M Y H:i:s T');
						$data['os']				= $uname[0];
						$data['kernel']			= $uname[2];
						$data['uptime']			= $uptime;
						$data['memory']			= $memory;
						$data['disks']			= $disks;
					break;
					case 'logs':
						$logfiles		= [];
						$position		= 0;
						$size			= 0;
						$logfile		= NULL;
						$directories	= [
							'/var/fruithost/logs/',
							'/var/log/'
						];
						
						if(isset($_GET['file']) && !empty($_GET['file'])) {
							$logfile = Encryption::decrypt($_GET['file'], sprintf('LOGFILE::%s', Auth::getID()));
							
							if(!file_exists($logfile)) {
								$logfile = NULL;
							}
							
							if(!is_readable($logfile)) {
								$logfile = NULL;
							}
							
							if(!empty($logfile)) {
								$logfile = explode(PHP_EOL, shell_exec(sprintf('cat %s 2>&1', $logfile)));
							}
						}
						
						do {
							$size		= count($directories);
							$directory	= $directories[$position++];
							
							foreach(new \DirectoryIterator($directory) AS $info) {
								if($info->isDot()) {
									continue;
								}
								
								if($info->isDir()) {
									if($info->isReadable()) {
										$directories[]	= $info->getPathName();
										$size			= count($directories);
									}
									continue;
								}
								
								if(preg_match('/(\.(gz|\d)$|\-bin\.)/', $info->getPathName())) {
									continue;
								}
								
								$path = str_replace($info->getFileName(), '', $info->getPathName());
	
								if(empty($logfiles[$path])) {
									$logfiles[$path] = [];
								}
							
								$logfiles[$path][] = $info->getFileName();
							}
						} while($size > $position);
						
						$data['logfile']	= $logfile;
						$data['logfiles']	= $logfiles;
					break;
					case 'modules':
						$data['repositorys']	= Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
						$data['modules']		= $this->modules;
					break;
				}

				$this->template->display('admin' . (!empty($destination) ? sprintf('/%s', $destination) : ''), $data);
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
							$result		= call_user_func_array($callback, [ $_POST ]);
							
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