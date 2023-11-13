<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

    $template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
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
		<div class="terminal p-0 m-0 h-100">
			<div class="output"></div>
			<input name="command" placeholder="<?php I18N::__('Command...'); ?>" />
		</div>
		<input type="hidden" name="destination" value="<?php print $this->url('/server/console'); ?>" />
	<?php
	$template->footer();
?>