<?php
	$template->header();
	?>
		<form>
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a href="<?php print $this->url('/account'); ?>">Account</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
				</div>
			</div>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Account Details</a></li>
				<li class="nav-item"><a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="true">Change Password</a></li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane show active" id="details" role="tabpanel" aria-labelledby="details-tab">
					<p class="text-secondary p-4">Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.</p>
					
					<div class="container">
						<div class="form-group row">
							<label for="email" class="col-sm-2 col-form-label">E-Mail Adresss:</label>
							<div class="col-sm-10">
								<input type="email" class="form-control" id="email" value="" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="name_first" class="col-sm-2 col-form-label">Full Name:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="name_first" value="" />
							</div>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="name_last" value="" />
							</div>
						</div>
						<div class="form-group row">
							<label for="phone" class="col-sm-2 col-form-label">Phone Number:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="phone" value="" />
							</div>
						</div>
						<div class="form-group row">
							<label for="address" class="col-sm-2 col-form-label">Postal Address:</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="address"></textarea>
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" class="btn btn-outline-success">Save</button>
						</div>
					</div>					
				</div>
				<div class="tab-pane" id="password" role="tabpanel" aria-labelledby="password-tab">
					<p class="text-secondary p-4">Change your current control panel password.</p>
					
					<div class="container">
						<div class="form-group row">
							<label for="password_current" class="col-sm-2 col-form-label">Current Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_current" value="" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="password_new" class="col-sm-2 col-form-label">New Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_new" value="" />
							</div>
						</div>
						<div class="form-group row">
							<label for="password_repeated" class="col-sm-2 col-form-label">Password Confirmation:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_repeated" value="" />
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