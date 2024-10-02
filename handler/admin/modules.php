<?php
	/**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	use fruithost\Accounting\Auth;
	use fruithost\Installer\Installer;
	use fruithost\Installer\Repository;
	use fruithost\Localization\I18N;
	use fruithost\Services\PHP;
	use fruithost\Storage\Database;
	use fruithost\UI\Button;
	use fruithost\UI\Modal;
	
	/* Add Repository */
	$template->getAdminCore()->addModal((new Modal('add_repository', I18N::get('Add Repository'), 'admin/modules/repository/create'))->addButton([
		(new Button())->setName('cancel')->setLabel(I18N::get('Cancel'))->addClass('btn-outline-danger')->setDismissable(),
		(new Button())->setName('create')->setLabel(I18N::get('Create'))->addClass('btn-outline-success')
	])->onSave(function(array $data = []) : string | bool {
		if(empty($data['repository_url']) || (!filter_var($data['repository_url'], FILTER_VALIDATE_URL) && !preg_match('/^git:/', $data['repository_url']))) {
			return I18N::get('Please enter an valid  repository URL!');
		}
		$repositorys = Installer::getRepositorys($data['repository_url']);
		if(count($repositorys) > 0) {
			return I18N::get('Repository already exists!');
		} else {
			Installer::createRepository($data['repository_url']);
		}
		
		return true;
	}));

	/* Module Informations */
	$template->getAdminCore()->addModal((new Modal('module_info', '', 'admin/modules/info'))->addButton([
		(new Button())->setName('cancel')->setLabel(I18N::get('Close'))->addClass('btn-outline-secondary')->setDismissable()
	]));

	$upgradeable = [];
	$list        = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
	$module      = null;
	$modules     = $template->getCore()->getModules();
	if(file_exists($list)) {
		$upgradeable = json_decode(file_get_contents($list));
	}

	/* Module :: Settings */
	if(isset($_GET['settings'])) {
		if(Auth::hasPermission('MODULES::HANDLE')) {
			if($modules->hasModule($_GET['settings'])) {
				$module = $modules->getModule($_GET['settings']);
				if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($module->getInstance(), 'onSettings')) {
					$module->getInstance()->onSettings($_POST);
				}
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}

	/* Module :: Disable */
	} else if(isset($_GET['disable'])) {
		if(Auth::hasPermission('MODULES::HANDLE')) {
			if($modules->hasModule($_GET['disable'], true)) {
				$module = $modules->getModule($_GET['disable'], true);
				if(!$module->isEnabled()) {
					$data['error'] = I18N::get('The module is already disabled!');
				} else {
					Database::update(DATABASE_PREFIX.'modules', 'name', [
						'name'  => $module->getDirectory(),
						'state' => 'DISABLED'
					]);
					$module->setEnabled(false);
					$template->assign('success', I18N::get('The module was successfully disabled.'));
				}
			} else {
				$template->assign('error', I18N::get('The module not exists!'));
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}

	/* Mdoule :: Enable */
	} else if(isset($_GET['enable'])) {
		if(Auth::hasPermission('MODULES::HANDLE')) {
			if($modules->hasModule($_GET['enable'], true)) {
				$module = $modules->getModule($_GET['enable'], true);
				if($module->isEnabled()) {
					$template->assign('error', I18N::get('The module is already enabled!'));
				} else {
					Database::update(DATABASE_PREFIX.'modules', 'name', [
						'name'  => $module->getDirectory(),
						'state' => 'ENABLED'
					]);
					$module->setEnabled(true);
					$template->assign('success', I18N::get('The module was successfully enabled.'));
				}
			} else {
				$template->assign('error', I18N::get('The module not exists!'));
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}

	/* Module :: Install */
	} else if(isset($_GET['install'])) {
		if(Auth::hasPermission('MODULES::INSTALL')) {
			if($modules->hasModule($_GET['install'], true)) {
				$template->assign('error', I18N::get('The module is already installed!'));
			} else {
				$repositorys = Installer::getRepositorys();
				if(count($repositorys) === 0) {
					$template->assign('error', I18N::get('Repository Error: No repositorys found!'));
				} else {
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
								if($name == $_GET['install']) {
									$found = $name;
									$repo  = $repository;
								}
							}
						}
					}
					if(empty($found)) {
						$template->assign('error', I18N::get('The module was not found!'));
					} else {
						$zip = Installer::getFile($repo, sprintf('modules.packages/%s.zip?time=%d', $found, time()));
						if(empty($zip)) {
							$template->assign('error', I18N::get('The selected module has no installation package!'));
						} else {
							// Save the Package
							$temp_path = sprintf('%s%s%s%s', dirname(PATH), DS, 'temp', DS);
							// Check if Temp-Dir exists
							if(!file_exists($temp_path)) {
								@mkdir($temp_path);
							}
							// Check if Temp-Dir is accessible
							if(!is_writeable($temp_path)) {
								$template->assign('error', sprintf(I18N::get('The Path %s cant be written!'), $temp_path));
							}
							$zip_path = sprintf('%sinstall_'.$found.'.package', $temp_path);
							// Write Package File
							if(file_put_contents($zip_path, $zip)) {
								$zip = new \ZipArchive;
								// Check ZIP-Package
								if($zip->open($zip_path) !== true) {
									$template->assign('error', I18N::get('The Module-Package is broken!'));
								} else if(!is_writable(sprintf('%s%s%s%s', dirname(PATH), DS, 'modules', DS))) {
									$template->assign('error', sprintf(I18N::get('The Path %s is not writeable!'), sprintf('%s%s%s%s', dirname(PATH), DS, 'modules', DS)));
										
								} else {
									// Extract ZIP-Package
									if(!$zip->extractTo(sprintf('%s%s%s%s', dirname(PATH), DS, 'modules', DS))) {
										$template->assign('error', sprintf(I18N::get('Can\'t extract Module-Package: %s'), $zip->getStatusString()));
									}
									$zip->close();
									$finished    = false;
									$module_path = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'modules', DS, $found);
									// If the module has an installer,..
									if(file_exists(sprintf('%s/setup/install.php', $module_path))) {
										$root   = false;
										$string = '#!fruithost:permission:root';
										try {
											$handler = fopen(sprintf('%s/setup/install.php', $module_path), 'r');
											$root    = (fread($handler, strlen($string)) === $string);
											fclose($handler);
											if($root) {
												require_once(sprintf('%s/setup/install.php', $module_path));
											} else {
												$php = new PHP();
												$php->setPath(PATH);
												$php->execute('classes/System/Loader.class.php', [
													'DAEMON'      => true,
													'REQUEST_URI' => '/',
													'MODULE'      => sprintf('%s/setup/install.php', $module_path)
												]);
												
												if(preg_match('/(File Not Found|404 Not Found|500 Internal Server Error)/', $php->getHeader())) {
													$template->assign('error', sprintf(I18N::get('Installscript not found: %s/setup/install.php'), $module_path));
													// Register Module
												} else {
													$finished = true;
												}
											}
										} catch(\Exception $e) {
											$template->assign('error', $e->getMessage());
											$template->assign('error', $e->getTraceAsString());
										}
									} else {
										$finished = true;
									}
									if($finished) {
										// @ToDo Check if module exists in DB: insert or update
										Database::insert(DATABASE_PREFIX.'modules', [
											'id'           => null,
											'name'         => $found,
											'state'        => 'DISABLED',
											'time_enabled' => null,
											'time_updated' => null,
											'time_deleted' => null
										]);
										$template->assign('success', sprintf(I18N::get('The Module %s was successfully installed.'), $found));
									}
								}
							} else {
								$template->assign('error', sprintf(I18N::get('Can\'t write Module-Package to Path %s!'), $temp_path));
							}
						}
					}
				}
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}

	/* Module :: Deinstall */
	} else if(isset($_GET['deinstall'])) {
		if(Auth::hasPermission('MODULES::DEINSTALL')) {
			if($modules->hasModule($_GET['deinstall'], true)) {
				$module = $modules->getModule($_GET['deinstall'], true);
				// @ToDo Delete the entry!
				Database::update(DATABASE_PREFIX.'modules', 'name', [
					'name'         => $module->getDirectory(),
					'state'        => 'DISABLED',
					'time_deleted' => date('Y-m-d H:i:s', time()),
				]);
				if($module->isEnabled()) {
					$module->setEnabled(false);
				}
				$template->assign('success', I18N::get('The module was successfully deinstalled!'));
			} else {
				$template->assign('error', I18N::get('The module not exists!'));
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}

	/* Module :: Reinstall */
	} else if(isset($_GET['reinstall'])) {
		if($modules->hasModule($_GET['reinstall'], true)) {
            $module		= $modules->getModule($_GET['reinstall'], true);
            $install	= sprintf('%s%s/setup/install.php', $module->getPath(), DS);

            if(file_exists($install)) {
                try {
                    $php = new PHP();
                    $php->setPath(PATH);
                    $php->execute('/classes/System/Loader.class.php', [
                        'DAEMON'			=> true,
                        'REQUEST_URI'		=> '/',
                        'MODULE'			=> $install
                    ]);

                    if(preg_match('/(File Not Found|404 Not Found|500 Internal Server Error)/', $php->getHeader())) {
                        throw new \Exception('Installscript not found: ' . $install . PHP_EOL . $php->getHeader());
                    }

                    if(!empty($php->getBody())) {
                        $template->assign('error', I18N::get('The module has an problem:') . '<br />' . $php->getBody());
                    }
                } catch(\Exception $e) {
                    $template->assign('error', I18N::get('The module has an problem:') . '<br />' . $e->getMessage());
                }
            }

            $template->assign('success', I18N::get('The module was successfully reinstalled!'));
        } else {
            $template->assign('error', I18N::get('The module not exists!'));
        }

	/* Module :: Check */
	} else if(isset($_GET['check'])) {
		if(Auth::hasPermission('MODULES::HANDLE')) {
			if($modules->hasModule($_GET['check'], true)) {
				$module = $modules->getModule($_GET['check'], true);
				if($module) {
					// @ToDo export to template file!
					$check  = sprintf('%s%ssetup/check.php', $module->getPath(), DS);
					$fix_it = sprintf('<div class="d-grid gap-2 d-md-flex justify-content-md-end">
								<a class="btn btn-text btn-sm px-0">'.I18N::get('Try to fix the issue with').'</a>
								<a href="%s" class="btn btn-warning btn-sm">'.I18N::get('Reinstall').'</a>
								<a class="btn btn-text btn-sm px-0">'.I18N::get('the module.').'</a>
							</div>', $this->url('/admin/modules/?reinstall='.$module->getDirectory()));
					if(file_exists($check)) {
						try {
							$php = new PHP();
							$php->setPath(PATH);
							$php->execute('/classes/System/Loader.class.php', [
								'DAEMON'      => true,
								'REQUEST_URI' => '/',
								'MODULE'      => $check
							]);
							if(preg_match('/(File Not Found|404 Not Found|500 Internal Server Error)/', $php->getHeader())) {
								throw new \Exception('Installscript not found: '.$check.PHP_EOL.$php->getHeader());
							}
							if(!empty($php->getBody())) {
								$template->assign('error', I18N::get('The module has an problem:').'<br />'.$php->getBody().$fix_it);
							} else {
								$template->assign('success', sprintf(I18N::get('The module <strong>%s</strong> was successfully checked.'), $module->getInfo()->getName()));
							}
						} catch(\Exception $e) {
							$template->assign('error', I18N::get('The module has an problem:').'<br />'.$e->getMessage());
						}
					} else {
						$template->assign('success', sprintf(I18N::get('The module <strong>%s</strong> has no checks and seems be okay.'), $module->getInfo()->getName()));
					}
				} else {
					$template->assign('error', I18N::get('The module has an instantiation problem!'));
				}
			} else {
				$template->assign('error', I18N::get('The module not exists!'));
			}
		} else {
			$template->assign('error', I18N::get('You have no permissions for this page.'));
		}
	}

	switch($tab) {
		case 'install':
			if(!Auth::hasPermission('MODULES::INSTALL')) {
				$template->assign('error', I18N::get('You have no permissions for this page.'));
			}
		break;
		case 'repositorys':
			if(!Auth::hasPermission('MODULES::REPOSITORY')) {
				$template->assign('error', I18N::get('You have no permissions for this page.'));
			}
		break;
		case 'errors':
			if(!Auth::hasPermission('MODULES::ERRORS')) {
				$template->assign('error', I18N::get('You have no permissions for this page.'));
			}
		break;
	}

	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			/* Repositorys :: Update */
			case 'update':
				if(Auth::hasPermission('MODULES::HANDLE')) {
					$repositorys = [];
					if(isset($_POST['repository'])) {
						$repositorys = Installer::getRepositorysByID($_POST['repository']);
					} else {
						$repositorys = Installer::getRepositorys();
					}
					if(count($repositorys) === 0) {
						#print "EMPTY";
					} else {
						$count      = 0;
						$updateable = [];
						$packages   = [];
						$conflicts  = [];
						$installed  = $this->getCore()->getModules()->getList();
						foreach($repositorys as $entry) {
							$content = Installer::getFile($entry, 'modules.list');
							if($content == Repository::FORBIDDEN) {
								$updated = '9999-12-31 00:00:00';
							} else if(empty($content)) {
								$updated = '0000-00-00 00:00:00';
							} else {
								$modules = explode(PHP_EOL, $content);
								$updated = date('Y-m-d H:i:s', time());
								$loaded  = 0;
								foreach($modules as $name) {
									if(empty($name)) {
										continue;
									}
									$name = trim($name);
									$info = Installer::getFile($entry, sprintf('%s/module.package', $name));
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
									$check  = $installed[$name];
									$remote = json_decode($info);
									if(!empty($check) && !empty($remote) && isset($remote->version) && version_compare($remote->version, $check->getInfo()->getVersion(), '>')) {
										$updateable[$name] = $remote;
									}
								}
							}
							Installer::updateRepository($entry->id, $updated);
							++$count;
						}
					}
					$temp = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
					if(is_writeable($temp)) {
						file_put_contents($temp, json_encode($updateable));
					}
					$this->assign('success', sprintf(I18N::get('<strong>%d Repository%s</strong> was updated. Found %d related Update%s!'), $count, ($count === 1 ? '' : 's'), count($updateable), (count($updateable) === 1 ? '' : 's')));
				} else {
					$template->assign('error', I18N::get('You have no permissions for this page.'));
				}
			break;

			/* Repository :: Delete */
			case 'delete':
				if(Auth::hasPermission('MODULES::DEINSTALL')) {
					$messages = [];
					if(isset($_POST['repository'])) {
						foreach($_POST['repository'] as $repository) {
							Installer::deleteRepository($repository);
						}
						$messages[] = sprintf(I18N::get('<strong>%d Repositorys</strong> was successfully removed'), count($_POST['repository']));
					}
					if(count($messages) > 0) {
						$this->assign('success', implode(' and ', $messages));
					} else {
						$this->assign('error', I18N::get('Please select some entries you want to delete.'));
					}
				} else {
					$template->assign('error', I18N::get('You have no permissions for this page.'));
				}
			break;
		}
	}
	$template->assign('module', $module);
	$template->assign('upgradeable', $upgradeable);
	$template->assign('modules', $template->getCore()->getModules());
	$template->assign('errors', $template->getCore()->getModules()->getErrors());
	$template->assign('repositorys', Installer::getRepositorys());
?>