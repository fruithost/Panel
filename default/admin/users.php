<?php
    use fruithost\Accounting\Auth;
    use fruithost\Accounting\Session;
    use fruithost\Localization\I18N;

    $template->header();
	
	if(!Auth::hasPermission('USERS::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
		<form method="post" action="<?php print $this->url('/admin/users' . (empty($tab) ? '' : sprintf('/%s', $tab)) . (empty($action) ? '' : sprintf('/%s', $action))); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/admin/users'); ?>"><?php I18N::__('Users'); ?></a>
					<?php
						if(is_numeric($tab) && $tab > 0) {
							?>
								<i class="material-icons">arrow_right</i>
								<a class="active" href="<?php print $this->url('/admin/users/' . $user->getID()); ?>"><?php printf('%s %s', I18N::get('edit'), $user->getUsername()); ?></a>
							<?php
						} else if($tab == 'create') {
							?>
								<i class="material-icons">arrow_right</i>
								<a class="active" href="<?php print $this->url('/admin/users/create'); ?>"><?php I18N::__('create User'); ?></a>
							<?php
						}
					?>
					
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">				
					<?php
						if(is_numeric($tab) && $tab > 0) {
							?>
								<div class="btn-group mr-2">
									<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Save'); ?></button>
									
									<?php
										if($user->getID() != 1) {
											?>
												<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
											<?php
										}
									?>
								</div>
							<?php
							
						} else if($tab == 'create') {
							?>
								<div class="btn-group mr-2">
									<button type="submit" name="action" value="create" class="btn btn-sm btn-outline-primary"><?php I18N::__('Create'); ?></button>
								</div>
							<?php
						} else {
							?>
								<div class="btn-group mr-2">
									<a href="<?php print $this->url('/admin/users/create'); ?>" class="btn btn-sm btn-outline-primary text-primary"><?php I18N::__('Add new'); ?></a>
									<button type="submit" name="action" value="deletes" class="btn btn-sm btn-outline-danger" data-confirm="<?php I18N::__('Do you really wan\'t to delete all selected users?'); ?>"><?php I18N::__('Delete'); ?></button>
								</div>
							<?php
						}
					?>
				</div>
			</header>
			<?php
				if(Session::has('success')) {
					?>
						<div class="alert alert-success mt-4" role="alert"><?php print Session::get('success'); ?></div>
					<?php
					Session::remove('success');
				} else if(!is_numeric($tab) && $tab !== 'create') {
					if(isset($error)) {
						?>
							<div class="alert alert-danger mt-4" role="alert"><?php (is_array($error) ? var_dump($error) : print $error); ?></div>
						<?php
					}
					
					if(isset($success)) {
						?>
							<div class="alert alert-success mt-4" role="alert"><?php (is_array($success) ? var_dump($success) : print $success); ?></div>
						<?php
					}
				}
			?>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane show active" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					<?php
						if(is_numeric($tab) && $tab > 0) {
							$template->display('admin/users/edit', [
								'user'	=> $user
							]);
						} else if($tab == 'create') {
							$template->display('admin/users/create');
						} else if(count($users) === 0) {
							$template->display('admin/users/empty');
						} else {
							$template->display('admin/users/list');
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
						$('input[name^="password_recovery"]').on('change', function(event) {
							let name		= $(event.delegateTarget).attr('name');
							let matches		= /(?<type>[a-zA-Z0-9_-]+)\[(?<name>([a-zA-Z0-9_-]+))\]/gm.exec(name);
							let elements	= {
								generate:			$('input[name="password_recovery[generate]"]'),
								recover:			$('input[name="password_recovery[recover]"]'),
								password_new:		$('input[name="password_new"]'),
								password_repeated:	$('input[name="password_repeated"]')
							};

							if(matches != null && typeof(matches.groups) !== 'undefined' && matches.groups.type == 'password_recovery') {
								elements.password_new.val('');
								elements.password_repeated.val('');
								
								switch(matches.groups.name) {
									case 'recover':
										if(elements.generate.is(':checked')) {
											elements.generate.prop('checked', false);
										}
									break;
									case 'generate':
										if(elements.recover.is(':checked')) {
											elements.recover.prop('checked', false);
										}
									break;
								}
							}
						});
						
						$('button[name="action"].deletes').on('click', function(event) {
							$(event.target).parent().parent().parent().find('input[type="checkbox"]').prop('checked', true);
						});
					}(jQuery));
				}
			}, 500);
		</script>
	<?php
	
	$template->footer();
?>