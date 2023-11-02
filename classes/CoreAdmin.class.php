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
			$this->addModal((new Modal('add_repository', I18N::get('Add Repository'), 'admin/modules/repository/create'))->addButton([
				(new Button())->setName('cancel')->setLabel(I18N::get('Cancel'))->addClass('btn-outline-danger')->setDismissable(),
				(new Button())->setName('create')->setLabel(I18N::get('Create'))->addClass('btn-outline-success')
			])->onSave([ $this, 'onCreateRepository' ]));
			
			$this->addModal((new Modal('confirmation', I18N::get('Confirmation'), NULL))->addButton([
				(new Button())->setName('cancel')->setLabel(I18N::get('No'))->addClass('btn-outline-danger')->setDismissable(),
				(new Button())->setName('create')->setLabel(I18N::get('Yes'))->addClass('btn-outline-success')
			])->onSave([ $this, 'onConfirmation' ]));
			
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
		
		public function onConfirmation(array $data = []) : string {
			// @ToDo Check permissions?
			return 'CONFIRMED';
		}
		
		public function onCreateRepository(array $data = []) : string | bool {
			if(empty($data['repository_url']) || !filter_var($data['repository_url'], FILTER_VALIDATE_URL)) {
				return I18N::get('Please enter an valid  repository URL!');
			}
			
			$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys` WHERE `url`=:url', [
				'url'	=> $data['repository_url']
			]);
			
			if(count($repositorys) > 0) {
				return I18N::get('Repository already exists!');
			} else {
				Database::insert(DATABASE_PREFIX . 'repositorys', [
					'id'			=> null,
					'url'			=> $data['repository_url'],
					'time_updated'	=> NULL
				]);
			}
			
			return true;
		}
	}
?>