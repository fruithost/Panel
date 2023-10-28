<?php
	use fruithost\Auth;
	
	$template->header();
	
	if(!Auth::hasPermission('THEMES::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	require_once(dirname(dirname(__DIR__)) . '/views/admin/themes_empty.php');
	
	$template->footer();
?>