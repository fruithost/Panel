<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

    $template->header();
	?>
		<form method="post" action="<?php print $this->url('/account' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
						<li class="breadcrumb-item active" aria-current="page">
							<a href="<?php print $this->url('/account'); ?>"><?php I18N::__('Account'); ?></a>
						</li>
					</ol>
				</nav>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Save'); ?></button>
				</div>
			</header>
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="details-tab" href="<?php print $this->url('/account'); ?>" role="tab"><?php I18N::__('Account Details'); ?></a></li>
				<li class="nav-item"><a class="nav-link<?php print (!empty($tab) && $tab === 'password' ? ' active' : ''); ?>" id="password-tab" href="<?php print $this->url('/account/password'); ?>" role="tab"><?php I18N::__('Change Password'); ?></a></li>
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
			<div class="tab-content">
				<div class="tab-pane<?php print (empty($tab) ? ' show active' : ''); ?>" id="details" role="tabpanel" aria-labelledby="details-tab">
					<p class="text-secondary p-4"><?php I18N::__('Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.'); ?></p>
					
					<div class="container">
						<div class="form-group row">
							<label for="email" class="col-sm-2 col-form-label"><?php I18N::__('E-Mail Address'); ?>:</label>
							<div class="col-sm-10">
								<input type="email" class="form-control" id="email" name="email" value="<?php print Auth::getMail(); ?>" placeholder="your.email@example.com" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="name_first" class="col-sm-2 col-form-label"><?php I18N::__('Full Name'); ?>:</label>
							<div class="col-sm-10 container">
								<div class="row">
									<div class="col-6">
										<input type="text" class="form-control" id="name_first" name="name_first" value="<?php print (!empty($data) && !empty($data->name_first) ? $data->name_first : ''); ?>" />
									</div>
									<div class="col-6">
										<input type="text" class="form-control" id="name_last" name="name_last" value="<?php print (!empty($data) && !empty($data->name_last) ? $data->name_last : ''); ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="phone" class="col-sm-2 col-form-label"><?php I18N::__('Phone Number'); ?>:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="phone" name="phone" value="<?php print (!empty($data) && !empty($data->phone_number) ? $data->phone_number : ''); ?>" />
							</div>
						</div>
						<div class="form-group row">
							<label for="address" class="col-sm-2 col-form-label"><?php I18N::__('Postal Address'); ?>:</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="address" name="address"><?php print (!empty($data) && !empty($data->address) ? $data->address : ''); ?></textarea>
							</div>
						</div>
						<div class="form-group text-end">
							<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
						</div>
					</div>					
				</div>
				<div class="tab-pane<?php print (!empty($tab) && $tab === 'password' ? ' show active' : ''); ?>" id="password" role="tabpanel" aria-labelledby="password-tab">
					<p class="text-secondary p-4"><?php I18N::__('Change your current control panel password.'); ?></p>
					
					<div class="container">
						<div class="form-group row">
							<label for="password_current" class="col-sm-2 col-form-label"><?php I18N::__('Current Password'); ?>:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_current" name="password_current" value="" />
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="password_new" class="col-sm-2 col-form-label"><?php I18N::__('New Password'); ?>:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_new" name="password_new" value="" />
							</div>
						</div>
						<div class="form-group row">
							<label for="password_repeated" class="col-sm-2 col-form-label"><?php I18N::__('Password Confirmation'); ?>:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password_repeated" name="password_repeated" value="" />
							</div>
						</div>
						<div class="form-group text-end">
							<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php
	$template->footer();
?>