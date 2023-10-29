<?php
	use fruithost\Auth;
	use fruithost\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('THEMES::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	require_once(dirname(dirname(__DIR__)) . '/views/admin/themes_empty.php');
	
	$template->footer();
?>