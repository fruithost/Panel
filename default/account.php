<?php
	use \fruithost\Auth;
	$template->header();
	?>
		<form method="post" action="<?php print $this->url('/account' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/account'); ?>">Account</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary">Save</button>
				</div>
			</header>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item"><a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="details-tab" href="<?php print $this->url('/account'); ?>" role="tab">Account Details</a></li>
				<li class="nav-item"><a class="nav-link<?php print (!empty($tab) && $tab === 'password' ? ' active' : ''); ?>" id="password-tab" href="<?php print $this->url('/account/password'); ?>" role="tab">Change Password</a></li>
			</ul>
			<?php
				if(isset($error)) {
					?>
						<div class="alert alert-danger mt-4" role="alert"><?php print $error; ?></div>
					<?php
				} else if(isset($success)) {
					?>
						<div class="alert alert-success mt-4" role="alert"><?php print $success; ?></div>
					<?php
				}
			?>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane<?php print (empty($tab) ? ' show active' : ''); ?>" id="details" role="tabpanel" aria-labelledby="details-tab">
					<p class="text-secondary p-4">Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.</p>
					
					<div class="container">
						<div class="form-group row">
							<label for="email" class="col-sm-2 col-form-label">E-Mail Adresss:</label>
							<div class="col-sm-10">
								<input type="email" class="form-control" id="email" name="email" value="<?php print Auth::getMail(); ?>" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="name_first" class="col-sm-2 col-form-label">Full Name:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="name_first" name="name_first" value="<?php print (!empty($data) && !empty($data->name_first) ? $data->name_first : ''); ?>" />
							</div>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="name_last" name="name_last" value="<?php print (!empty($data) && !empty($data->name_last) ? $data->name_last : ''); ?>" />
							</div>
						</div>
						<div class="form-group row">
							<label for="phone" class="col-sm-2 col-form-label">Phone Number:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="phone" name="phone" value="<?php print (!empty($data) && !empty($data->phone_number) ? $data->phone_number : ''); ?>" />
							</div>
						</div>
						<div class="form-group row">
							<label for="address" class="col-sm-2 col-form-label">Postal Address:</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="address" name="address"><?php print (!empty($data) && !empty($data->address) ? $data->address : ''); ?></textarea>
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" name="action" value="save" class="btn btn-outline-success">Save</button>
						</div>
					</div>					
				</div>
				<div class="tab-pane<?php print (!empty($tab) && $tab === 'password' ? ' show active' : ''); ?>" id="password" role="tabpanel" aria-labelledby="password-tab">
					<p class="text-secondary p-4">Change your current control panel password.</p>
					
					<div class="container">
						<div class="form-group row">
							<label for="password_current" class="col-sm-2 col-form-label">Current Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_current" name="password_current" value="" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="password_new" class="col-sm-2 col-form-label">New Password:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_new" name="password_new" value="" />
							</div>
						</div>
						<div class="form-group row">
							<label for="password_repeated" class="col-sm-2 col-form-label">Password Confirmation:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_repeated" name="password_repeated" value="" />
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" name="action" value="save" class="btn btn-outline-success">Save</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php
	$template->footer();
?>