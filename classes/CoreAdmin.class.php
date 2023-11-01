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
			$this->addModal((new Modal('add_repository', I18N::get('Add Repository'), dirname(__DIR__) . '/views/admin/repository_create.php'))->addButton([
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
			
			$this->getRouter()->addRoute('^/admin(?:/([a-zA-Z0-9\-_]+))(?:/([a-zA-Z0-9\-_]+))?$', function($destination = null, $tab = NULL) {
				$data = [
					'tab'	=> $tab
				];
				
				if(!Auth::isLoggedIn()) {
					Response::redirect('/');
				}
				
				switch($destination) {
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
									$data['error'] = I18N::get('The module is already disabled!');
								} else {
									Database::update(DATABASE_PREFIX . 'modules', 'name', [
										'name'			=> $module->getDirectory(),
										'state'			=> 'DISABLED'
									]);
									
									$module->setEnabled(false);
									
									$data['success']	= I18N::get('The module was successfully disabled.');
								}
							} else {
								$data['error'] = I18N::get('The module not exists!');
							}
						} else if(isset($_GET['enable'])) {
							if($modules->hasModule($_GET['enable'], true)) {
								$module = $modules->getModule($_GET['enable'], true);
								
								if($module->isEnabled()) {
									$data['error'] = I18N::get('The module is already enabled!');
								} else {
									Database::update(DATABASE_PREFIX . 'modules', 'name', [
										'name'			=> $module->getDirectory(),
										'state'			=> 'ENABLED'
									]);
									
									$module->setEnabled(true);
									
									$data['success']	= I18N::get('The module was successfully enabled.');
								}
							} else {
								$data['error'] = I18N::get('The module not exists!');
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
								
								$data['success'] = I18N::get('The module was successfully deinstalled!');
							} else {
								$data['error'] = I18N::get('The module not exists!');
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