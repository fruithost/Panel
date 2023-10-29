<?php
	use fruithost\Auth;
	use fruithost\I18N;
	
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
		<form method="post" action="<?php print $this->url('/admin/modules' . (!empty($tab) ? '/' . $tab : '') . '?settings=' . $_GET['settings']); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/admin/modules' . (!empty($tab) ? '/' . $tab : '') . '?settings=' . $_GET['settings']); ?>">
						<?php print (empty($module) ? 'Module error' : sprintf('Settings for %s', $module->getInfo()->getName())); ?>
					</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<div class="btn-group mr-2">
						<a name="action" value="cancel" class="btn btn-sm btn-outline-danger" href="<?php print $this->url('/admin/modules' . (!empty($tab) ? '/' . $tab : '')); ?>"><?php I18N::__('Cancel'); ?></a>
						<button type="submit" name="action" value="settings" class="btn btn-sm btn-outline-success"><?php I18N::__('Save'); ?></button>
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
		<form method="post" action="<?php print $this->url('/admin/modules' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/admin/modules'); ?>"><?php I18N::__('Modules'); ?></a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">				
					<?php
						switch($tab) {
							case 'repositorys':
								?>
									<div class="btn-group mr-2">
										<button type="button" name="add_repository" data-toggle="modal" data-target="#add_repository" class="btn btn-sm btn-outline-primary"><?php I18N::__('Add new'); ?></button>
										<button type="submit" name="action" value="update" class="btn btn-sm btn-outline-success"><?php I18N::__('Update'); ?></button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
									</div>
								<?php
							break;
							default:
								?>
									<div class="btn-group mr-2">
										<button type="submit" name="action" value="upgrade" class="btn btn-sm btn-outline-success"><?php I18N::__('Upgrade'); ?></button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
									</div>
								<?php
							break;
						}
					?>
				</div>
			</header>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item"><a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="globals-tab" href="<?php print $this->url('/admin/modules'); ?>" role="tab"><?php
					I18N::__('Installed Modules');
					
					$updates = count((array) $upgradeable);
					
					if($updates > 0) {
						printf('<span class="badge badge-pill badge-danger ml-1">%d</span>', $updates);
					}
				?></a></li>
				<li class="nav-item"><a class="nav-link<?php print (!empty($tab) && $tab === 'repositorys' ? ' active' : ''); ?>" id="security-tab" href="<?php print $this->url('/admin/modules/repositorys'); ?>" role="tab"><?php I18N::__('Repositorys'); ?></a></li>
			</ul>
			<?php
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
							case 'repositorys':
								if(count($repositorys) === 0) {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/repository_empty.php');
								} else {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/repository_list.php');
								}
							break;
							default:
								if(count($modules->getList()) === 0) {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/modules_empty.php');
								} else {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/modules_list.php');
								}
							break;
						}
					?>
				</div>
			</div>
		</form>
		<script type="text/javascript">
			_watcher_modules = setInterval(function() {
				if(typeof(jQuery) !== 'undefined') {
					clearInterval(_watcher_modules);
					
					(function($) {						
						$('button[name="action"].delete, button[name="action"].update').on('click', function(event) {
							$(event.target).parent().parent().find('input[type="checkbox"]').prop('checked', true);
						});
					}(jQuery));
				}
			}, 500);
		</script>
	<?php
	}
	
	$template->footer();
?>