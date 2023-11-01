<?php
	use fruithost\Database;
	use fruithost\I18N;
	
	
	$upgradeable		= [];
	$list				= sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
	$module				= NULL;
	$modules			= $template->getCore()->getModules();
	
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
				Database::update(DATABASE_PREFIX . 'modules', 'name', [
					'name'			=> $module->getDirectory(),
					'state'			=> 'ENABLED'
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
			
			Database::update(DATABASE_PREFIX . 'modules', 'name', [
				'name'			=> $module->getDirectory(),
				'state'			=> 'DISABLED',
				'time_deleted'	=> date('Y-m-d H:i:s', time()),
			]);
			
			if($module->isEnabled()) {
				$module->setEnabled(false);
			}
			
			$template->assign('success', I18N::get('The module was successfully deinstalled!'));
		} else {
			$template->assign('error', I18N::get('The module not exists!'));
		}
	}
	
	$template->assign('module',			$module);
	$template->assign('upgradeable',	$upgradeable);
	$template->assign('repositorys',	Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`'));
	$template->assign('modules',		$template->getCore()->getModules());
	
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'update':
				$repositorys	= [];
				
				if(isset($_POST['repository'])) {
					foreach($_POST['repository'] AS $repository) {
						$repositorys[] = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys` WHERE `id`=:id', [
							'id'	=> $repository
						]);
					}
				} else {
					$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
				}
				
				if(count($repositorys) === 0) {
					#print "EMPTY";
				} else {
					$count			= 0;
					$updateable		= [];
					$packages		= [];
					$conflicts		= [];
					$installed		= $this->getCore()->getModules()->getList();
					
					foreach($repositorys AS $entry) {
						// Load GitHub by RAW
						if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $entry->url, $matches)) {
							$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
						}
						
						$list		= @file_get_contents(sprintf('%s/modules.list', $entry->url));
					
						if(empty($list)) {
							continue;
						}
						
						$modules	= explode(PHP_EOL, $list);
						$loaded		= 0;
						
						foreach($modules AS $name) {
							if(empty($name)) {
								continue;
							}
							
							$info = file_get_contents(sprintf('%s/%s/module.package', $entry->url, $name));
						
							if(!isset($conflicts[$name])) {
								$conflicts[$name] = [];
							}
							
							$conflicts[$name][] = $entry;
							
							++$loaded;
							
							if(isset($packages[$name])) {
								continue;
							}
							
							$packages[$name] = $info;
							
							if(empty($info)) {
								continue;
							}
							
							if(!isset($installed[$name])) {
								continue;
							}
							
							$check	= $installed[$name];
							$remote	= json_decode($info);
							
							if(!empty($check) && !empty($remote) && isset($remote->version) && version_compare($remote->version, $check->getInfo()->getVersion(), '>')) {
								$updateable[$name]	= $remote;
							}
						}
						
						Database::update(DATABASE_PREFIX . 'repositorys', 'id', [
							'id'			=> $entry->id,
							'time_updated'	=> date('Y-m-d H:i:s', time())
						]);
						
						++$count;
					}
				}
				
				file_put_contents(sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list'), json_encode($updateable));
				
				$this->assign('success',		sprintf(I18N::get('<strong>%d Repository%s</strong> was updated. Found %d related Update%s!'), $count, ($count === 1 ? '' : 's'), count($updateable), (count($updateable) === 1 ? '' : 's')));
				$this->assign('repositorys',	Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`'));
			break;
			case 'delete':
				$messages	= [];
				
				if(isset($_POST['repository'])) {
					foreach($_POST['repository'] AS $repository) {
						Database::delete(DATABASE_PREFIX . 'repositorys', [
							'id'	=> $repository
						]);
					}
					
					$messages[] = sprintf(I18N::get('<strong>%d Repositorys</strong> was successfully removed'), count($_POST['repository']));
				
					$this->assign('repositorys',	Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`'));
				}
				
				if(count($messages) > 0) {
					$this->assign('success', implode(' and ', $messages));
				} else {
					$this->assign('error', I18N::get('Please select some entries you want to delete.'));
				}
			break;
		}
	}
?>