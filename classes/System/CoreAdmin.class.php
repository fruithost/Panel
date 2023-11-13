<?php
    namespace fruithost\System;

    use fruithost\Accounting\Auth;
    use fruithost\Modules\ModuleInterface;
    use fruithost\UI\Button;
    use fruithost\UI\Modal;
    use fruithost\Network\Response;
    use fruithost\Localization\I18N;

    class CoreAdmin extends ModuleInterface {
		public function init() : void {
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
				
				$this->getTemplate()->display('overview', [
					'admin' => true
				]);
			});
			
			$this->getRouter()->addRoute('^/admin(?:(?:/([a-zA-Z0-9\-_]+)?)(?:/([a-zA-Z0-9\-_]+)(?:/([a-zA-Z0-9\-_]+))?)?)?$', function(?string $destination = null, ?string $tab = null, ?string $action = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				if($this->getTemplate()->display((!empty($destination) ? sprintf('/admin/%s', $destination) : 'overview'), [
					'tab'		=> $tab,
					'action'	=> $action,
					'admin'		=> true
				]) == false) {
					$this->getTemplate()->display('error/404');
				}
			});
		
			$this->getRouter()->addRoute('^/server(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function(?string $destination = null, ?string $tab = null) {
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				if($this->getTemplate()->display('server' . (!empty($destination) ? sprintf('/%s', $destination) : ''), [
					'tab'	=> $tab
				]) == false) {
					$this->getTemplate()->display('error/404');
				}
			});
		}
	}
?>