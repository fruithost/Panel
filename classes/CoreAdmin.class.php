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
			
			$this->addModal((new Modal('confirmation', I18N::get('Confirmation'), null))->addButton([
				(new Button())->setName('cancel')->setLabel(I18N::get('No'))->addClass('btn-outline-danger')->setDismissable(),
				(new Button())->setName('create')->setLabel(I18N::get('Yes'))->addClass('btn-outline-success')
			])->onSave(function(array $data = []) : ?string {
				// @ToDo Check permissions?
				return 'CONFIRMED';
			}));
			
			$this->getRouter()->addRoute('/admin', function() {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('admin');
			});
			
			$this->getRouter()->addRoute('^/admin(?:(?:/([a-zA-Z0-9\-_]+)?)(?:/([a-zA-Z0-9\-_]+)(?:/([a-zA-Z0-9\-_]+))?)?)?$', function(?string $destination = null, ?string $tab = null, ?string $action = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				$this->getTemplate()->display('admin' . (!empty($destination) ? sprintf('/%s', $destination) : ''), [
					'tab'		=> $tab,
					'action'	=> $action
				]);
			});
		
			$this->getRouter()->addRoute('^/server(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function(?string $destination = null, ?string $tab = null) {
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