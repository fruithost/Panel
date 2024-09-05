<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */
	namespace fruithost\System;
	
	use fruithost\Accounting\Auth;
	use fruithost\Accounting\Session;
	use fruithost\Installer\Installer;
	use fruithost\Installer\Repository;
	use fruithost\Localization\I18N;
	use fruithost\Modules\Modules;
	use fruithost\Network\Request;
	use fruithost\Network\Response;
	use fruithost\Network\Router;
	use fruithost\Security\Encryption;
	use fruithost\Storage\Database;
	use fruithost\Storage\PropertiesCache;
	use fruithost\Templating\Template;
	use fruithost\UI\Icon;
	use Parsedown\Parsedown;
	
	class Core extends Loader {
		private ?Modules         $modules          = null;
		private ?Hooks           $hooks            = null;
		private ?Router          $router           = null;
		private ?Template        $template         = null;
		private ?CoreAdmin       $admin            = null;
		private ?PropertiesCache $cache            = null;
		private bool             $modules_disabled = false;
		
		public function __construct() {
			parent::__construct();
			$this->init();
		}
		
		public function init() : void {
			Request::init();
			$this->cache    = new PropertiesCache();
			$this->hooks    = new Hooks();
			$this->modules  = new Modules($this, $this->modules_disabled);
			$this->template = new Template($this);
			$this->router   = new Router($this);
			$this->admin    = new CoreAdmin($this, null);
			Icon::init($this);
			Update::bind($this)::check();
			$this->getHooks()->runAction('core_pre_init');
			$this->initRoutes();
			$this->getHooks()->runAction('core_init');
			$this->router->run();
		}
		
		public function getHooks() : ?Hooks {
			return $this->hooks;
		}
		
		protected function initRoutes() : void {
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
			$this->router->addRoute('^/settings(?:/([a-zA-Z0-9\-_]+))?$', function(?string $tab = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				$this->template->display('settings', [
					'tab'       => $tab,
					'languages' => I18N::getLanguages(),
					'timezones' => json_decode(file_get_contents(dirname(PATH).'/config/timezones.json'))
				]);
			});
			$this->router->addRoute('^/account(?:/([a-zA-Z0-9\-_]+))?$', function(?string $tab = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				$data = Database::single('SELECT * FROM `'.DATABASE_PREFIX.'users_data` WHERE `user_id`=:user_id LIMIT 1', [
					'user_id' => Auth::getID()
				]);
				if($data !== false) {
					foreach($data as $index => $entry) {
						if(in_array($index, [
							'id',
							'user_id'
						])) {
							continue;
						}
						$data->{$index} = Encryption::decrypt($entry, ENCRYPTION_SALT);
					}
				}
				$this->template->display('account', [
					'tab'  => $tab,
					'data' => $data
				]);
			});
			$this->router->addRoute('^/app/([a-zA-Z0-9\-_]+)/(.*)$', function(?string $module = null, ?string $file = null) {
				if(empty($module)) {
					$this->template->display('error/module', array_merge($this->template->getAssigns(), [
						'module' => $module
					]));
					
					return;
				}
				$module = $this->getModules()->getModule($module);
				if(empty($module)) {
					header('HTTP/1.1 403 Forbidden');
					require_once(dirname(PATH).'/placeholder/errors/403.html');
					exit();
				}
				if(!method_exists($module->getInstance(), 'frame')) {
					header('HTTP/1.1 403 Forbidden');
					require_once(dirname(PATH).'/placeholder/errors/403.html');
					exit();
				}
				if(!file_exists(sprintf('%s/www/', $module->getPath()))) {
					header('HTTP/1.1 403 Forbidden');
					require_once(dirname(PATH).'/placeholder/errors/403.html');
					exit();
				}
				$file = sprintf('%s/www/%s', $module->getPath(), $file);
				if(!file_exists($file)) {
					header('HTTP/1.1 403 Forbidden');
					require_once(dirname(PATH).'/placeholder/errors/403.html');
					exit();
				}
				Response::setContentType(mime_content_type($file));
				Response::header();
				readfile($file);
				exit();
			});
			$this->router->addRoute('^/module(?:(?:/([a-zA-Z0-9\-_]+)?)(?:/([a-zA-Z0-9\-_]+)(?:/([a-zA-Z0-9\-_]+))?)?)?$', function(?string $module = null, ?string $submodule = null, ?string $action = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				if(Session::has('success')) {
					$this->template->assign('success', Session::get('success'));
					Session::remove('success');
				}
				if(Session::has('error')) {
					$this->template->assign('error', Session::get('error'));
					Session::remove('error');
				}
				$errors = [];
				foreach(($this->getModules()->getList()) as $m) {
					try {
						if($m != null && $m->getInstance() != null && method_exists($m->getInstance(), 'preLoad')) {
							$m->getInstance()->preLoad();
						}
					} catch(\Exception $e) {
						$errors[] = (object) [
							'name'    => $m->getInfo()->getName(),
							'message' => $e->getMessage(),
							'stack'   => $e->getTrace()
						];
					}
				}
				if(count($errors) > 0) {
					$message = I18N::get('Following Modules have some Errors:');
					$message .= '<br />';
					foreach($errors as $entry) {
						$message .= $entry->name;
					}
					$this->template->assign('error', $message);
				}
				if(empty($module)) {
					$this->template->display('error/module', array_merge($this->template->getAssigns(), [
						'module'    => $module,
						'submodule' => $submodule,
						'action'    => $action
					]));
					
					return;
				}
				$module = $this->getModules()->getModule($module);
				if(empty($module)) {
					$this->template->display('error/module', array_merge($this->template->getAssigns(), [
						'module'    => $module,
						'submodule' => $submodule,
						'action'    => $action
					]));
					
					return;
				}
				$permissions = $module->getInfo()->getPermissions();
				$visible     = true;
				if(!empty($permissions)) {
					$visible = false;
					foreach($permissions as $permission) {
						if(Auth::hasPermission($permission)) {
							$visible = true;
						}
					}
				}
				if(!$visible) {
					$this->template->display('error/permissions', array_merge($this->template->getAssigns(), [
						'module'    => $module,
						'submodule' => $submodule,
						'action'    => $action
					]));
					
					return;
				}
				if(method_exists($module->getInstance(), 'load')) {
					$module->getInstance()->load($submodule, $action);
				}
				if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($module->getInstance(), 'onPOST')) {
					$module->getInstance()->onPOST(array_merge([
						'module'    => $module,
						'submodule' => $submodule,
						'action'    => $action
					], $_POST));
				}
				if(!method_exists($module->getInstance(), 'content') && !method_exists($module->getInstance(), 'frame')) {
					$this->template->display('error/module_empty', array_merge($this->template->getAssigns(), [
						'module'    => $module,
						'submodule' => $submodule,
						'action'    => $action
					]));
					
					return;
				}
				$this->template->display('module', array_merge($this->template->getAssigns(), [
					'module'    => $module,
					'submodule' => $submodule,
					'action'    => $action
				]));
			});
			$this->router->addRoute('^/lost-password(?:/([a-zA-Z0-9\-_]+))?$', function(?string $token = null) {
				if(Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				$this->template->display('lost-password', [
					'token' => $token
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
					require_once(dirname(PATH).'/placeholder/errors/403.html');
					exit();
				}
				if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
					header('HTTP/1.1 405 Method Not Allowed');
					require_once(dirname(PATH).'/placeholder/errors/405.html');
					exit();
				}
				$this->getHooks()->runAction('ajax');
				$this->router->run(true);
				// Fire modals
				if(!empty($_POST['modal'])) {
					$modals = $this->getHooks()->applyFilter('modals', []);
					if(count($modals) == 0) {
						print 'No registred modals.';
					} else {
						foreach($modals as $modal) {
							if($modal->getName() === $_POST['modal']) {
								$callback = $modal->getCallback('save');
								$data     = [];
								foreach($_POST as $name => $value) {
									if(is_array($value)) {
										$data[trim($name)] = [];
										foreach($value as $key => $entry) {
											$data[trim($name)][trim($key)] = trim($entry);
										}
									} else {
										$data[trim($name)] = trim($value);
									}
								}
								$result = call_user_func_array($callback, [ $data ]);
								if(is_bool($result)) {
									print json_encode($result);
									
									return;
								}
								print $result;
							}
						}
					}
					/* Module Info */
				} else if(!empty($_POST['module'])) {
					if(!Auth::hasPermission('MODULES::INSTALL')) {
						print 'NO_PERMISSIONS';
						
						return;
					}
					$repositorys = Installer::getRepositorys();
					if(count($repositorys) === 0) {
						print 'NO_REPOSITORYS';
						
						return;
					}
					$found = null;
					$repo  = null;
					foreach($repositorys as $repository) {
						$content = Installer::getFile($repository, 'modules.list');
						if(!($content == Repository::FORBIDDEN || empty($content))) {
							$modules = explode(PHP_EOL, $content);
							foreach($modules as $name) {
								if(empty($name)) {
									continue;
								}
								$name = trim($name);
								if($name == $_POST['module']) {
									$found = $name;
									$repo  = $repository;
								}
							}
						}
					}
					if(empty($found)) {
						print 'MODULE_NOT_FOUND';
						
						return;
					}
					$info = Installer::getFile($repo, sprintf('%s/module.package?time=%d', $found, time()));
					if(empty($info)) {
						print 'MODULE_EMPTY';
						
						return;
					}
					$info           = json_decode($info);
					$screenshots    = [];
					$info->icon_raw = $info->icon;
					/* Icon */
					if(preg_match('/^(http|https|data):/', $info->icon)) {
						$info->icon = sprintf('<img alt="" class="module-icon" src="%s" />', $info->icon);
					} else if(str_starts_with($info->icon, '/')) {
						$info->icon = sprintf('<img alt="" class="module-icon" src="/app/%s" />', $info->icon);
					} else {
						$info->icon = sprintf('<i class="bi bi-%s"></i>', $info->icon);
					}
					/* Readme */
					$readme = Installer::getFile($repo, sprintf('%s/README.md?time=%d', $found, time()));
					if(empty($readme)) {
						$readme = '';
					}
					/* Screenshots */
					if(isset($info->screenshots)) {
						// Fix Github Repos
						if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $repo->url, $matches)) {
							$repo->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
						}
						foreach($info->screenshots as $screenshot) {
							$screenshots[] = sprintf('%s/%s/%s?raw=true', $repo->url, $found, $screenshot->file);
						}
					}
					/* Changelog */
					$changelog = Installer::getFile($repo, sprintf('%s/CHANGELOG.md?time=%d', $found, time()));
					if(empty($changelog)) {
						$changelog = '';
					}
					/* Features */
					// @ToDo Analyze module files for these informations...
					$features = [
						'hooks'       => [],
						'filters'     => [],
						'actions'     => [],
						'extended'    => false,
						'settings'    => false,
						'daemon'      => false,
						'install'     => false,
						'deinstall'   => false,
						'check'       => false,
						'permissions' => []
					];
					print json_encode([
						'repository'  => $repo,
						'info'        => $info,
						'screenshots' => $screenshots,
						'installed'   => $this->getModules()->hasModule($found, true),
						'readme'      => (new Parsedown())->text($readme),
						'changelog'   => (new Parsedown())->text($changelog),
						'features'    => $features
					]);
				}
			});
		}
		
		public function getModules() : ?Modules {
			return $this->modules;
		}
		
		public function getAdminCore() : ?CoreAdmin {
			return $this->admin;
		}
		
		public function disableModules() {
			$this->modules_disabled = true;
		}
		
		public function enableModules() {
			$this->modules_disabled = false;
		}
		
		public function getTemplate() : ?Template {
			return $this->template;
		}
		
		public function getRouter() : ?Router {
			return $this->router;
		}
		
		public function hasSettings(string $name) : bool {
			return $this->cache->exists('settings', $name);
		}
		
		public function removeSettings(string $name) : void {
			$this->cache->remove('settings', $name);
		}
		
		public function getSettings(?string $name = null, mixed $default = null) : mixed {
			return $this->cache->get('settings', $name, $default);
		}
		
		public function setSettings(string $name, mixed $value = null) : void {
			$this->cache->set('settings', $name, $value);
		}
	}
	
	?>