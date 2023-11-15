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
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
						<li class="breadcrumb-item<?php print (empty($submodule) ? ' active" aria-current="page' : ''); ?>">
							<a class="active" href="<?php print $this->url('/admin/users'); ?>"><?php I18N::__('Users'); ?></a>
						</li>
						<?php
							if(is_numeric($tab) && $tab > 0) {
								?>
									<li class="breadcrumb-item active" aria-current="page">
										<a href="<?php print $this->url('/admin/users/' . $user->getID()); ?>"><?php printf('%s %s', I18N::get('edit'), $user->getUsername()); ?></a>
									</li>
								<?php
							} else if($tab == 'create') {
								?>
									<li class="breadcrumb-item active" aria-current="page">
										<a href="<?php print $this->url('/admin/users/create'); ?>"><?php I18N::__('create User'); ?></a>
									</li>
								<?php
							}
						?>
					</ol>
				</nav>
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
									<a href="<?php print $this->url('/admin/users/create'); ?>" class="btn btn-sm btn-outline-primary"><?php I18N::__('Add new'); ?></a>
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
			(() => {
				'use strict'
				
				window.addEventListener('DOMContentLoaded', () => {
					function change(event) {
						let name		= event.target.name;
						let matches		= /(?<type>[a-zA-Z0-9_-]+)\[(?<name>([a-zA-Z0-9_-]+))\]/gm.exec(name);
						let elements	= {
							generate:			document.querySelector('input[name="password_recovery[generate]"]'),
							recover:			document.querySelector('input[name="password_recovery[recover]"]'),
							password_new:		document.querySelector('input[name="password_new"]'),
							password_repeated:	document.querySelector('input[name="password_repeated"]')
						};

						if(matches != null && typeof(matches.groups) !== 'undefined' && matches.groups.type == 'password_recovery') {
							elements.password_new.value			= '';
							elements.password_repeated.value	= '';
							
							switch(matches.groups.name) {
								case 'recover':
									if(elements.generate.checked) {
										elements.generate.checked = false;
									}
								break;
								case 'generate':
									if(elements.recover.checked) {
										elements.recover.checked = false;
									}
								break;
							}
						}
					}
					
					document.querySelectorAll('input[name^="password_recovery"]').forEach(element => {
						element.addEventListener('change', change);
					});
					
					function click(event) {
						event.target.parent().parent().parent().querySelector('input[type="checkbox"]').checked = true;
					}
					
					document.querySelectorAll('button[name="action"].deletes').forEach(element => {
						element.addEventListener('click', click);
					});
				})
			})();
		</script>
	<?php
	
	$template->footer();
?>