<?php
	use fruithost\I18N;
	$template->header();
	?>
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">
				<?php I18N::__('Module Error'); ?>
			</h1>
		</div>
		<p><?php I18N::__('Empty module...!'); ?></p>
	<?php
	$template->footer();
?>