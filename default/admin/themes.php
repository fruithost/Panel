<?php
    use fruithost\Accounting\Auth;
    use fruithost\Localization\I18N;

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
	
	$template->display('admin/themes/empty');
	
	$template->footer();
?>