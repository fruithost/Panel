<?php
	namespace fruithost;
	
	use fruithost\Encryption;
	use fruithost\Auth;
	use fruithost\Database;
	use fruithost\Response;
	use fruithost\ModuleInterface;
	
	class CoreAdmin extends ModuleInterface {
		public function init() {
			$this->getCore()->getHooks()->runAction('core_admin_pre_init');
			
			$this->getRouter()->addRoute('/admin', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('admin');
			});
			
			$this->getRouter()->addRoute('^/admin(?:(?:/([a-zA-Z0-9\-_]+)?)(?:/([a-zA-Z0-9\-_]+)(?:/([a-zA-Z0-9\-_]+))?)?)?$', function($destination = null, $tab = NULL, $action = NULL) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('admin' . (!empty($destination) ? sprintf('/%s', $destination) : ''), [
					'tab'		=> $tab,
					'action'	=> $action
				]);
			});
		
			$this->getRouter()->addRoute('^/server(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function($destination = null, $tab = NULL) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('server' . (!empty($destination) ? sprintf('/%s', $destination) : ''), [
					'tab'	=> $tab
				]);
			});
		}
	}
?>