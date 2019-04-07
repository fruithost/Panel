<?php
	$template->header();
	?>
		<form>
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a href="<?php print $this->url('/settings'); ?>">Settings</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
				</div>
			</div>
			
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="globals-tab" data-toggle="tab" href="#globals" role="tab" aria-controls="globals" aria-selected="true">Global Settings</a></li>
				<li class="nav-item"><a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="true">Security</a></li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane show active" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					Global Settings (language, themes,..)
				</div>
				<div class="tab-pane" id="security" role="tabpanel" aria-labelledby="security-tab">
					Security
				</div>
			</div>
		</form>
	<?php
	$template->footer();
?>