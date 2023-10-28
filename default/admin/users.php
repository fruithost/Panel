<?php
	use fruithost\Auth;
	
	$template->header();
	
	if(!Auth::hasPermission('USERS::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
		<form method="post" action="<?php print $this->url('/admin/users' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/admin/users'); ?>">Users</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">				
					<?php
						switch($tab) {
							case 'user':
								?>
									<div class="btn-group mr-2">
										<button type="submit" name="action" value="update" class="btn btn-sm btn-outline-success">Update</button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Delete</button>
									</div>
								<?php
							break;
							default:
								?>
									<div class="btn-group mr-2">
										<button type="button" name="add_user" data-toggle="modal" data-target="#add_user" class="btn btn-sm btn-outline-primary">Add new</button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Delete</button>
									</div>
								<?php
							break;
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
						switch($tab) {
							default:
								if(count($users) === 0) {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/users_empty.php');
								} else {
									require_once(dirname(dirname(__DIR__)) . '/views/admin/users_list.php');
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
	
	$template->footer();
?>