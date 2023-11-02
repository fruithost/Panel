<?php
	use fruithost\Auth;
	use fruithost\I18N;
	
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
		<form method="post" action="<?php print $this->url('/admin/users' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/admin/users'); ?>"><?php I18N::__('Users'); ?></a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">				
					<?php
						if(is_numeric($tab) && $tab > 0) {
							?>
								<div class="btn-group mr-2">
									<button type="button" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Save'); ?></button>
									<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
								</div>
							<?php
							
						} else if($tab == 'create') {
							?>
								<div class="btn-group mr-2">
									<button type="button" name="action" value="create" class="btn btn-sm btn-outline-primary"><?php I18N::__('Create'); ?></button>
									<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
								</div>
							<?php
							
						} else {
							?>
								<div class="btn-group mr-2">
									<a href="<?php print $this->url('/admin/users/create'); ?>" class="btn btn-sm btn-outline-primary"><?php I18N::__('Add new'); ?></a>
									<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
								</div>
							<?php
						}
					?>
				</div>
			</header>
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
						if(is_numeric($tab) && $tab > 0) {
							$template->display('admin/users/edit');
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
						$('button[name="action"].delete, button[name="action"].update').on('click', function(event) {
							$(event.target).parent().parent().find('input[type="checkbox"]').prop('checked', true);
						});
					}(jQuery));
				}
			}, 500);
		</script>
	<?php
	
	$template->footer();
?>