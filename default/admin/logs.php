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
	?>
		
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">
			<a href="<?php print $this->url('/admin/logs'); ?>">Logfiles</a>
		</h1>
		<div class="btn-toolbar mb-2 mb-md-0">
			<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary">Refresh</button>
		</div>
	</div>
	<div class="d-flex pb-2 mb-3">
		<div class="col-10">
			VIEWER
		</div>
		<div class="col-2">
			<?php
				print_r($logfiles);
			?>
		</div>
	</div>
	<?php
	
	$template->footer();
?>