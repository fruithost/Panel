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
					<p class="text-secondary p-4">Global settings for your usability</p>
					
					<div class="container">
						<div class="form-group row">
							<label for="language" class="col-sm-2 col-form-label">Language:</label>
							<div class="col-sm-10">
								<select name="language" class="form-control">
									<option>English</option>
								</select>
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="timeformat" class="col-sm-2 col-form-label">Time Format:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="timeformat" value="d.m.Y - H:i:s" />
							</div>
						</div>
						<div class="form-group row">
							<label for="timezone" class="col-sm-2 col-form-label">Timezone:</label>
							<div class="col-sm-10">
								<select name="timezone" class="form-control">
									<option>Europe / Berlin</option>
								</select>
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" class="btn btn-outline-success">Save</button>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="security" role="tabpanel" aria-labelledby="security-tab">
					<p class="text-secondary p-4">Some security settings for your safety.</p>
					
					<div class="container">
						<div class="form-group row">
							<div class="col-sm-2 text-right">
								<input type="checkbox" name="2fa_enabled" id="2fa_enabled" value="true" />
							</div>
							<label for="2fa_enabled" class="col-sm-10 col-form-label">Enable Two-factor authentication (2FA)</label>
						</div>
						<div class="form-group row">
							<label for="2fa_type" class="col-sm-2 col-form-label">Type:</label>
							<div class="col-sm-10">
								<select name="2fa_type" class="form-control">
									<option DISABLED>Smartphone (disabled)</option>
									<option SELECTED>E-Mail</option>
									<option DISABLED>SMS (disabled)</option>
								</select>
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" class="btn btn-outline-success">Save</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php
	$template->footer();
?>