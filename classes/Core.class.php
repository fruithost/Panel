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
			$this->hooks	= new Hooks();
			$this->template	= new Template($this);
			$this->modules	= new Modules($this);
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
			
			$this->router->addRoute('/settings', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}

				$this->template->display('settings');
			});
			
			$this->router->addRoute('/account', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}

				$this->template->display('account');
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
				
				if(!method_exists($module->getInstance(), 'content')) {
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
			
			$this->router->addRoute('^/lost-password(?:/(.*))?$', function($token = null) {
				if(Auth::isLoggedIn()) {
					Response::redirect('/');
				}

				$this->template->display('lost-password', [
					'token'	=> $token
				]);
			});
			
			$this->router->addRoute('/login', function() {
				if(Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->template->display('login');
			});

			$this->router->run();
		}
	}
?>