<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\System\Utils;

    $template->header();
	
	if(!Auth::hasPermission('SERVER::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	print "<pre>";
	print_r(`lshw -C network -json`);
	print_r(json_decode(`ip --json address show`));
	print_r(json_decode(`ip --json link show`));
	print_r(json_decode(`ip --json route show`));
	
	$this->footer();
?>