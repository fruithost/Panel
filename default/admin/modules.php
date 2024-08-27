<?php
	
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('MODULES::VIEW')) {
		?>
        <div class="alert alert-danger mt-4" role="alert">
            <strong><?php I18N::__('Access denied!'); ?></strong>
            <p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
        </div>
		<?php
		$template->footer();
		exit();
	}
	
	if(isset($_GET['settings'])) {
		?>
        <form method="post"
              action="<?php print $this->url('/admin/modules'.(!empty($tab) ? '/'.$tab : '').'?settings='.$_GET['settings']); ?>">
            <header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb"
                        style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                        <li class="breadcrumb-item">
                            <a href="<?php print $this->url('/admin/modules'); ?>"><?php I18N::__('Modules'); ?></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php print $this->url('/admin/modules'.(!empty($tab) ? '/'.$tab : '').'?settings='.$_GET['settings']); ?>">
								<?php print (empty($module) ? 'Module error' : sprintf('Settings for %s', $module->getInfo()->getName())); ?>
                            </a>
                        </li>
                    </ol>
                </nav>

                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <a class="btn btn-sm btn-outline-danger"
                           href="<?php print $this->url('/admin/modules'.(!empty($tab) ? '/'.$tab : '')); ?>"><?php I18N::__('Cancel'); ?></a>
                        <button type="submit" name="action" value="settings"
                                class="btn btn-sm btn-outline-success"><?php I18N::__('Save'); ?></button>
                    </div>
                </div>
            </header>
			<?php
				if(!$modules->hasModule($_GET['settings'])) {
					?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <strong><?php I18N::__('Module not found!'); ?></strong>
                        <p class="pb-0 mb-0"><?php I18N::__('Unknown module name. Please select an valid Module!'); ?></p>
                    </div>
					<?php
				} else {
					if(!$module->hasSettingsPath()) {
						?>
                        <div class="alert alert-danger mt-4" role="alert">
                            <strong><?php I18N::__('No Settings available!'); ?></strong>
                            <p class="pb-0 mb-0"><?php sprintf(I18N::get('The Module %s has no settings!'), $module->getInfo()->getName()); ?></p>
                        </div>
						<?php
					} else {
						if(isset($error)) {
							?>
                            <div class="container">
                                <div class="alert alert-danger mt-4" role="alert"><?php print $error; ?></div>
                            </div>
							<?php
						} else if(isset($success)) {
							?>
                            <div class="container">
                                <div class="alert alert-success mt-4" role="alert"><?php print $success; ?></div>
                            </div>
							<?php
						}
						
						require_once($module->getSettingsPath());
					}
				}
			?>
        </form>
		<?php
	} else {
		?>
        <form method="post" action="<?php print $this->url('/admin/modules'.(!empty($tab) ? '/'.$tab : '')); ?>">
            <header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb"
                        style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                        <li class="breadcrumb-item active" aria-current="page">
                            <a class="active"
                               href="<?php print $this->url('/admin/modules'); ?>"><?php I18N::__('Modules'); ?></a>
                        </li>
                    </ol>
                </nav>
                <div class="btn-toolbar mb-2 mb-md-0">
					<?php
						switch($tab) {
							case 'install':
								break;
							case 'repositorys':
								?>
                                <div class="btn-group mr-2">
                                    <button type="button" name="add_repository" data-bs-toggle="modal"
                                            data-bs-target="#add_repository"
                                            class="btn btn-sm btn-outline-primary"><?php I18N::__('Add new'); ?></button>
                                    <button type="submit" name="action" value="update"
                                            class="btn btn-sm btn-outline-success"><?php I18N::__('Update'); ?></button>
                                    <button type="submit" name="action" value="delete"
                                            class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
                                </div>
								<?php
								break;
							default:
								?>
                                <a href="<?php print $this->url('/admin/modules/install'); ?>"
                                   class="btn btn-sm btn-outline-primary mx-2"><?php I18N::__('Install'); ?></a>
                                <div class="btn-group mr-2">
                                    <button type="submit" name="action" value="upgrade"
                                            class="btn btn-sm btn-outline-success"><?php I18N::__('Upgrade'); ?></button>
                                    <button type="submit" name="action" value="delete"
                                            class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
                                </div>
								<?php
								break;
						}
					?>
                </div>
            </header>
			<?php
				if($tab == 'install') {
					?>
                    <div class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" role="tab">Popular</a></li>
                        <li class="nav-item"><a class="nav-link" role="tab">Results</a></li>
                        <li class="nav-item ms-auto">
                            <form role="search">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                                           aria-describedby="basic-addon1">
                                    <button class="btn btn-outline-secondary" type="button" id="button-addon2">Search
                                    </button>
                                </div>
                            </form>
                        </li>
                    </div>
					<?php
				} else {
					?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="globals-tab"
                               href="<?php print $this->url('/admin/modules'); ?>" role="tab">
								<?php
									I18N::__('Installed Modules');
									
									$updates = count((array) $upgradeable);
									
									if($updates > 0) {
										printf('<span class="badge rounded-pill text-bg-warning">%d</span>', $updates);
									}
								?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php print (!empty($tab) && $tab === 'repositorys' ? ' active' : ''); ?>"
                               id="security-tab" href="<?php print $this->url('/admin/modules/repositorys'); ?>"
                               role="tab">
								<?php I18N::__('Repositorys'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php print (!empty($tab) && $tab === 'errors' ? ' active' : ''); ?>"
                               id="errors-tab" href="<?php print $this->url('/admin/modules/errors'); ?>" role="tab">
								<?php
									I18N::__('Errors');
									
									$error_count = count((array) $errors);
									
									if($error_count > 0) {
										printf('<span class="badge rounded-pill text-bg-warning">%d</span>', $error_count);
									}
								?>
                            </a>
                        </li>
                    </ul>
					<?php
				}
				
				if(isset($error)) {
					?>
                    <div class="alert alert-danger mt-4" role="alert"><?php print $error; ?></div>
					<?php
				} else if(isset($success)) {
					?>
                    <div class="alert alert-success mt-4" role="alert"><?php print $success; ?></div>
					<?php
				}
			?>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					<?php
						switch($tab) {
							case 'install':
								$template->display('admin/modules/install');
								break;
							case 'repositorys':
								if(count($repositorys) === 0) {
									$template->display('admin/modules/repository/empty');
								} else {
									$template->display('admin/modules/repository/list');
								}
								break;
							case 'errors':
								if(count($errors) === 0) {
									$template->display('admin/modules/errors/empty');
								} else {
									$template->display('admin/modules/errors/list');
								}
								break;
							default:
								if(count($modules->getList()) === 0) {
									$template->display('admin/modules/empty');
								} else {
									$template->display('admin/modules/list');
								}
								break;
						}
					?>
                </div>
            </div>
        </form>
        <script type="text/javascript">
            (() => {
                window.addEventListener('DOMContentLoaded', () => {
                    [].map.call(document.querySelectorAll('button[name="action"].delete'), function (element) {
                        element.addEventListener('click', function (event) {
                            event.target.parentNode.parentNode.querySelector('input[type="checkbox"][name="repository[]"]').checked = true;
                        });
                    });

                    [].map.call(document.querySelectorAll('button[name="action"].update'), function (element) {
                        element.addEventListener('click', function (event) {
                            event.target.parentNode.parentNode.querySelector('input[type="checkbox"][name="repository[]"]').checked = true;
                        });
                    });
                });
            })();
        </script>
		<?php
	}
	
	$template->footer();
?>