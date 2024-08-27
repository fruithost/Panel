<?php
	
	use fruithost\Localization\I18N;
	use fruithost\Storage\Database;
	use fruithost\UI\Button;
	use fruithost\UI\Modal;
	
	function updateRepoSecrets()
	{
		$repos = Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'repositorys`');
		
		foreach($repos as $entry) {
			if(str_starts_with($entry->url, 'git:')) {
				$parts = explode(' ', $entry->url);
				$user = null;
				$repo = null;
				$token = null;
				$branch = null;
				
				foreach($parts as $part) {
					if(str_starts_with($part, 'user:')) {
						$user = str_replace('user:', '', $part);
					} else if(str_starts_with($part, 'repo:')) {
						$repo = str_replace('repo:', '', $part);
					} else if(str_starts_with($part, 'branch:')) {
						$branch = str_replace('branch:', '', $part);
					} else if(str_starts_with($part, 'token:')) {
						$token = str_replace('token:', '', $part);
					}
				}
				
				$str = [];
				
				foreach([
					        'user' => $user,
					        'repo' => $repo,
					        'branch' => $branch,
					        'token' => '[*** PROTECTED ***]',
				        ] as $name => $value) {
					if(!empty($value)) {
						$str[] = sprintf('%s:%s', $name, $value);
					}
				}
				
				$entry->url = sprintf('git: %s', implode(' ', $str));
				$entry->token = $token;
			}
		}
		
		return $repos;
	}
	
	$template->getAdminCore()->addModal((new Modal('add_repository', I18N::get('Add Repository'), 'admin/modules/repository/create'))->addButton([
		(new Button())->setName('cancel')->setLabel(I18N::get('Cancel'))->addClass('btn-outline-danger')->setDismissable(),
		(new Button())->setName('create')->setLabel(I18N::get('Create'))->addClass('btn-outline-success')
	])->onSave(function(array $data = []) : string | bool {
		if(empty($data['repository_url']) || (!filter_var($data['repository_url'], FILTER_VALIDATE_URL) && !preg_match('/^git:/', $data['repository_url']))) {
			return I18N::get('Please enter an valid  repository URL!');
		}
		
		$repositorys = Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'repositorys` WHERE `url`=:url', [
			'url' => $data['repository_url']
		]);
		
		if(count($repositorys) > 0) {
			return I18N::get('Repository already exists!');
		} else {
			Database::insert(DATABASE_PREFIX.'repositorys', [
				'id' => null,
				'url' => $data['repository_url'],
				'time_updated' => null
			]);
		}
		
		return true;
	}));
	
	$upgradeable = [];
	$list = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
	$module = null;
	$modules = $template->getCore()->getModules();
	
	if(file_exists($list)) {
		$upgradeable = json_decode(file_get_contents($list));
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
				Database::update(DATABASE_PREFIX.'modules', 'name', [
					'name' => $module->getDirectory(),
					'state' => 'DISABLED'
				]);
				
				$module->setEnabled(false);
				
				$template->assign('success', I18N::get('The module was successfully disabled.'));
			}
		} else {
			$template->assign('error', I18N::get('The module not exists!'));
		}
	} else if(isset($_GET['enable'])) {
		if($modules->hasModule($_GET['enable'], true)) {
			$module = $modules->getModule($_GET['enable'], true);
			
			if($module->isEnabled()) {
				$template->assign('error', I18N::get('The module is already enabled!'));
			} else {
				Database::update(DATABASE_PREFIX.'modules', 'name', [
					'name' => $module->getDirectory(),
					'state' => 'ENABLED'
				]);
				
				$module->setEnabled(true);
				
				$template->assign('success', I18N::get('The module was successfully enabled.'));
			}
		} else {
			$template->assign('error', I18N::get('The module not exists!'));
		}
	} else if(isset($_GET['deinstall'])) {
		if($modules->hasModule($_GET['deinstall'], true)) {
			$module = $modules->getModule($_GET['deinstall'], true);
			
			Database::update(DATABASE_PREFIX.'modules', 'name', [
				'name' => $module->getDirectory(),
				'state' => 'DISABLED',
				'time_deleted' => date('Y-m-d H:i:s', time()),
			]);
			
			if($module->isEnabled()) {
				$module->setEnabled(false);
			}
			
			$template->assign('success', I18N::get('The module was successfully deinstalled!'));
		} else {
			$template->assign('error', I18N::get('The module not exists!'));
		}
	}
	
	
	$template->assign('module', $module);
	$template->assign('upgradeable', $upgradeable);
	$template->assign('repositorys', updateRepoSecrets());
	$template->assign('modules', $template->getCore()->getModules());
	$template->assign('errors', $template->getCore()->getModules()->getErrors());
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'update':
				$repositorys = [];
				
				if(isset($_POST['repository'])) {
					foreach($_POST['repository'] as $repository) {
						$repositorys[] = Database::single('SELECT * FROM `'.DATABASE_PREFIX.'repositorys` WHERE `id`=:id', [
							'id' => $repository
						]);
					}
				} else {
					$repositorys = Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'repositorys`');
				}
				
				if(count($repositorys) === 0) {
					#print "EMPTY";
				} else {
					$count = 0;
					$updateable = [];
					$packages = [];
					$conflicts = [];
					$installed = $this->getCore()->getModules()->getList();
					$headers = [];
					$options = [];
					
					foreach($repositorys as $entry) {
						// Load GitHub by RAW
						if(preg_match('/github\.com\/([^\/]+)\/([^\/]+)$/Uis', rtrim($entry->url, '/'), $matches)) {
							$user = rtrim($matches[1], '/');
							$repo = rtrim($matches[2], '/');
							$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $user, $repo);
							
							// Load by Git-Variables
						} else if(str_starts_with($entry->url, 'git:')) {
							$parts = explode(' ', $entry->url);
							$user = null;
							$repo = null;
							$token = null;
							$branch = null;
							
							foreach($parts as $part) {
								if(str_starts_with($part, 'user:')) {
									$user = str_replace('user:', '', $part);
								} else if(str_starts_with($part, 'repo:')) {
									$repo = str_replace('repo:', '', $part);
								} else if(str_starts_with($part, 'branch:')) {
									$branch = str_replace('branch:', '', $part);
								} else if(str_starts_with($part, 'token:')) {
									$token = str_replace('token:', '', $part);
								}
							}
							
							if(empty($branch)) {
								$branch = 'master';
							}
							
							$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/%s', $user, $repo, $branch);
							
							if(!empty($token)) {
								$headers['Authorization'] = sprintf('token %s', $token);
								$headers['Accept'] = 'application/vnd.github.raw+json';
								$headers['User-Agent'] = sprintf('%s@%s (PHP v1.0.0)', $user, $repo);
								$headers['X-GitHub-Api-Version'] = '2022-11-28';
								$entry->url = sprintf('https://api.github.com/repos/%s/%s/contents', $user, $repo);
							}
						}
						
						if(!empty($headers)) {
							$h = '';
							
							foreach($headers as $name => $value) {
								$h .= sprintf('%s: %s%s', $name, $value, "\r\n");
							}
							
							$options = [
								"http" => [
									"header" => $h
								]
							];
						}
						
						$context = stream_context_create($options);
						$request = sprintf('%s/modules.list', $entry->url);
						$list = @file_get_contents($request, false, $context);
						
						if(count(preg_grep('/Forbidden/', $http_response_header)) == 1) {
							$updated = '9999-12-31 00:00:00';
						} else if(empty($list)) {
							$updated = '0000-00-00 00:00:00';
						} else {
							$modules = explode(PHP_EOL, $list);
							$updated = date('Y-m-d H:i:s', time());
							$loaded = 0;
							
							foreach($modules as $name) {
								if(empty($name)) {
									continue;
								}
								
								$name = trim($name);
								$info = file_get_contents(sprintf('%s/%s/module.package', $entry->url, $name), false, $context);
								
								if(!isset($conflicts[$name])) {
									$conflicts[$name] = [];
								}
								
								$conflicts[$name][] = $entry;
								
								++$loaded;
								
								if(isset($packages[$name])) {
									//continue;
								}
								
								$packages[$name] = $info;
								
								if(empty($info)) {
									continue;
								}
								
								if(!isset($installed[$name])) {
									continue;
								}
								
								$check = $installed[$name];
								$remote = json_decode($info);
								
								if(!empty($check) && !empty($remote) && isset($remote->version) && version_compare($remote->version, $check->getInfo()->getVersion(), '>')) {
									$updateable[$name] = $remote;
								}
							}
						}
						
						Database::update(DATABASE_PREFIX.'repositorys', 'id', [
							'id' => $entry->id,
							'time_updated' => $updated
						]);
						
						++$count;
					}
				}
				
				$temp = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
				if(is_writeable($temp)) {
					file_put_contents($temp, json_encode($updateable));
				}
				
				$this->assign('success', sprintf(I18N::get('<strong>%d Repository%s</strong> was updated. Found %d related Update%s!'), $count, ($count === 1 ? '' : 's'), count($updateable), (count($updateable) === 1 ? '' : 's')));
				$this->assign('repositorys', updateRepoSecrets());
				break;
			case 'delete':
				$messages = [];
				
				if(isset($_POST['repository'])) {
					foreach($_POST['repository'] as $repository) {
						Database::delete(DATABASE_PREFIX.'repositorys', [
							'id' => $repository
						]);
					}
					
					$messages[] = sprintf(I18N::get('<strong>%d Repositorys</strong> was successfully removed'), count($_POST['repository']));
				}
				
				if(count($messages) > 0) {
					$this->assign('success', implode(' and ', $messages));
				} else {
					$this->assign('error', I18N::get('Please select some entries you want to delete.'));
				}
				
				$this->assign('repositorys', updateRepoSecrets());
				break;
		}
	}
?>