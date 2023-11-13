<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
	use fruithost\UI\Icon;

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
	<div class="jumbotron text-center bg-transparent text-muted">
		<?php
			Icon::show('warning', [
				'classes'	=> [ 'text-warning' ]
			]);
		?>
		<h2><?php I18N::__('System-Reboot'); ?></h2>
		<?php
			if(isset($error)) {
				?>
					<p class="lead text-bold text-danger"><?php print $error; ?></p>
				<?php
			} else if(isset($success)) {
				?>
					<p class="lead text-bold text-success"><?php print $success; ?></p>
				<?php
			} else {
				?>
					<p class="lead"><?php I18N::__('Do you really want to reboot the complete system?'); ?></p>
					<form method="post" action="<?php print $this->url('/server/reboot'); ?>">
						<button type="submit" name="action" value="reboot" class="btn btn-sm btn-outline-primary"><?php I18N::__('Reboot now!'); ?></button>
					</form>
				<?php
			}
		?>
	</div>
	<?php
	$template->footer();
?>