<?php
	use fruithost\Auth;
	
	$template->header();
	
	if(!Auth::hasPermission('LOGFILES::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	$template->footer();
?>