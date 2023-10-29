<?php
	namespace fruithost;
	
	use fruithost\Encryption;
	use fruithost\Auth;
	use fruithost\Database;
	use fruithost\Response;
	use fruithost\ModuleInterface;
	use fruithost\Modal;
	use fruithost\Button;
	
	class CoreAdmin extends ModuleInterface {
		public function init() {
			$this->addModal((new Modal('add_repository', 'Add Repository', dirname(__DIR__) . '/views/admin/repository_create.php'))->addButton([
				(new Button())->setName('cancel')->setLabel('Cancel')->addClass('btn-outline-danger')->setDismissable(),
				(new Button())->setName('create')->setLabel('Create')->addClass('btn-outline-success')
			])->onSave([ $this, 'onCreateRepository' ]));
			
			$this->addModal((new Modal('confirmation', 'Confirmation', NULL))->addButton([
				(new Button())->setName('cancel')->setLabel('No')->addClass('btn-outline-danger')->setDismissable(),
				(new Button())->setName('create')->setLabel('Yes')->addClass('btn-outline-success')
			])->onSave([ $this, 'onConfirmation' ]));
			
			$this->getRouter()->addRoute('/admin', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('admin');
			});
			
			$this->getRouter()->addRoute('^/admin(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function($destination = null, $tab = NULL) {
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
						
						if($days == 0) {
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
						$data['daemon']			= [
							'started'	=> strtotime($this->getCore()->getSettings('DAEMON_TIME_START')),
							'start'		=> date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0))),
							'end'		=> date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0))),
							'ended'		=> strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0)),
							'time'		=> number_format($this->getCore()->getSettings('DAEMON_RUNNING_END', 0) - $this->getCore()->getSettings('DAEMON_RUNNING_START', 0), 4, ',', '.')
						];
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
						$upgradeable		= [];
						$list				= sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
						$module				= NULL;
						$modules			= $this->getModules();
						
						if(file_exists($list)) {
							$upgradeable	= json_decode(file_get_contents($list));
						}
						
						if(isset($_GET['settings'])) {
							if($modules->hasModule($_GET['settings'])) {
								$module = $modules->getModule($_GET['settings']);
								
								if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($module->getInstance(), 'onSettings')) {
									$module->getInstance()->onSettings($_POST);
								}
							}
						} else if(isset($_GET['disable'])) {
							if($modules->hasModule($_GET['disable'], true)) {
								$module = $modules->getModule($_GET['disable'], true);
								
								if(!$module->isEnabled()) {
									$data['error'] = 'The module is already disabled!';
								} else {
									Database::update(DATABASE_PREFIX . 'modules', 'name', [
										'name'			=> $module->getDirectory(),
										'state'			=> 'DISABLED'
									]);
									
									$module->setEnabled(false);
									
									$data['success']	= 'The module was successfully disabled.';
								}
							} else {
								$data['error'] = 'The module not exists!';
							}
						} else if(isset($_GET['enable'])) {
							if($modules->hasModule($_GET['enable'], true)) {
								$module = $modules->getModule($_GET['enable'], true);
								
								if($module->isEnabled()) {
									$data['error'] = 'The module is already enabled!';
								} else {
									Database::update(DATABASE_PREFIX . 'modules', 'name', [
										'name'			=> $module->getDirectory(),
										'state'			=> 'ENABLED'
									]);
									
									$module->setEnabled(true);
									
									$data['success']	= 'The module was successfully enabled.';
								}
							} else {
								$data['error'] = 'The module not exists!';
							}
						} else if(isset($_GET['deinstall'])) {
							if($modules->hasModule($_GET['deinstall'], true)) {
								$module = $modules->getModule($_GET['deinstall'], true);
								
								Database::update(DATABASE_PREFIX . 'modules', 'name', [
									'name'			=> $module->getDirectory(),
									'state'			=> 'DISABLED',
									'time_deleted'	=> date('Y-m-d H:i:s', time()),
								]);
								
								if($module->isEnabled()) {
									$module->setEnabled(false);
								}
								
								$data['success'] = 'The module was successfully deinstalled!';
							} else {
								$data['error'] = 'The module not exists!';
							}
						}
						
						$data['module']			= $module;
						$data['upgradeable']	= $upgradeable;
						$data['repositorys']	= Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
						$data['modules']		= $this->getModules();
					break;
					case 'users':
						
						$data['users'] = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'users`');
					break;
				}

				$this->getTemplate()->display('admin' . (!empty($destination) ? sprintf('/%s', $destination) : ''), $data);
			});
		
			$this->getRouter()->addRoute('^/server(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function($destination = null, $tab = NULL) {
				$data = [
					'tab'	=> $tab
				];
				
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('server' . (!empty($destination) ? sprintf('/%s', $destination) : ''), $data);
			});
		}
		
		public function onConfirmation(array $data = []) : string {
			return 'CONFIRMED';
		}
		
		public function onCreateRepository(array $data = []) : string | bool {
			if(empty($data['repository_url']) || !filter_var($data['repository_url'], FILTER_VALIDATE_URL)) {
				return 'Please enter an valid  repository URL!';
			}
			
			$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys` WHERE `url`=:url', [
				'url'	=> $data['repository_url']
			]);
			
			if(count($repositorys) > 0) {
				return 'Repository already exists!';
			} else {
				Database::insert(DATABASE_PREFIX . 'repositorys', [
					'id'			=> null,
					'url'			=>$data['repository_url'],
					'time_updated'	=> NULL
				]);
			}
			
			return true;
		}
	}
?>