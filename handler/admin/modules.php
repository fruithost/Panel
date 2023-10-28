<?php
	use fruithost\Database;
	
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
				
				$this->assign('success',		sprintf('<strong>%d Repository%s</strong> was updated. Found %d related Update%s!', $count, ($count === 1 ? '' : 's'), count($updateable), (count($updateable) === 1 ? '' : 's')));
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
					
					$messages[] = sprintf('<strong>%d Repositorys</strong> was successfully removed', count($_POST['repository']));
				
					$this->assign('repositorys',	Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`'));
				}
				
				if(count($messages) > 0) {
					$this->assign('success', implode(' and ', $messages));
				} else {
					$this->assign('error', 'Please select some entries you want to delete.');
				}
			break;
		}
	}
?>