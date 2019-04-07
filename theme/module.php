<?php
	$template->header();
	?>
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">
				<a href="<?php print $this->url(sprintf('/module/%s', $module)); ?>"><?php print $module; ?></a>
				<?php
					if(!empty($submodule)) {
						?>
							<i class="material-icons">arrow_right</i>
							<a href="<?php print $this->url(sprintf('/module/%s/%s', $module, $submodule)); ?>"><?php print $submodule; ?></a>
						<?php
					}
				?>
			</h1>
			<div class="btn-toolbar mb-2 mb-md-0">
				<div class="btn-group mr-2">
					<button type="button" class="btn btn-sm btn-outline-secondary">Create</button>
					<button type="button" class="btn btn-sm btn-outline-secondary">Delete</button>
				</div>
				<button type="button" class="btn btn-sm btn-outline-primary">Save</button>
			</div>
		</div>
		<p>Content...</p>
	<?php
	$template->footer();
?>