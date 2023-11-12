<?php
    use fruithost\Localization\I18N;
?>
<p></p>
<div class="container">
	<div class="row">
		<div class="col-3 border-end">
			<ul class="nav flex-column nav-tabs" role="tablist" aria-orientation="vertical">
				<li class="nav-item"><button type="button" class="nav-link active" id="security-tab" role="tab" data-bs-toggle="tab" data-bs-target="#security"><?php I18N::__('Security settings'); ?></button></li>
				<li class="nav-item"><button type="button" class="nav-link" id="manual-tab" role="tab" data-bs-toggle="tab" data-bs-target="#manual"><?php I18N::__('Change password manually'); ?></button></li>
				<li class="nav-item"><button type="button" class="nav-link" id="recover-tab" role="tab" data-bs-toggle="tab" data-bs-target="#recover"><?php I18N::__('Initiate password reset'); ?></button></li>
				<li class="nav-item"><button type="button" class="nav-link" id="generate-tab" role="tab" data-bs-toggle="tab" data-bs-target="#generate"><?php I18N::__('Generate new password'); ?></button></li>
			</ul>
		</div>
		<div class="col-9 p-4">
			<div class="tab-content">
				<!-- Security Settings -->
				<div class="tab-pane active" id="security" role="tabpanel" aria-labelledby="security-tab">
					<div class="form-group row">
						<div class="col-sm-2 text-right"></div>
						<div class="col-sm-10">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="2fa_enabled" id="2fa_enabled" value="true"<?php print ($user->getSettings('2FA_ENABLED', NULL, 'false') === 'true' ? ' CHECKED' : ''); ?> />
								<label class="form-check-label" for="2fa_enabled">
									<?php I18N::__('Enable Two-factor authentication (2FA)'); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-2 text-right"></div>
						<div class="col-sm-10">
							<?php
								if($user->getSettings('2FA_ENABLED', NULL, 'false') === 'true' && !filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
									?>
										<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
											<?php I18N::__('Two-Factor authentication (2FA) is <strong>temporarily disabled</strong> because you did not provide a <strong>valid E-Mail address</strong>.'); ?>
										</div>
									<?php
								} else if(!filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
									?>
										<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
											<?php I18N::__('Two-Factor authentication (2FA) will be <strong>temporarily disabled</strong> because you did not provide a <strong>valid E-Mail address</strong>.'); ?>
										</div>
									<?php
								}
							?>
						</div>
					</div>
					<div class="form-group row">
						<label for="2fa_type" class="col-sm-2 col-form-label"><?php I18N::__('Type'); ?>:</label>
						<div class="col-sm-10">
							<select name="2fa_type" id="2fa_type" class="form-select">
								<option value="app" DISABLED><?php I18N::__('Smartphone'); ?> (<?php I18N::__('disabled'); ?>)</option>
								<option value="mail" SELECTED><?php I18N::__('E-Mail'); ?></option>
								<option value="sms" DISABLED><?php I18N::__('SMS'); ?> (<?php I18N::__('disabled'); ?>)</option>
							</select>
						</div>
					</div>
				</div>
				
				<!-- Manual password change -->
				<div class="tab-pane" id="manual" role="tabpanel" aria-labelledby="manual-tab">
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
				</div>
				
				<!-- Password recovery -->
				<div class="tab-pane" id="recover" role="tabpanel" aria-labelledby="recover-tab">
					<p><?php I18N::__('Initiates the password recovery process.'); ?><br /><?php I18N::__('The user receives an E-Mail with instructions on how to reset the password.'); ?></p>
					<?php
						if(!filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
							?>
								<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
									<?php I18N::__('The user will probably not receive an E-Mail because the provided <strong>E-Mail address is invalid</strong>.'); ?>
								</div>
							<?php
						}
					?>
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" role="switch" name="password_recovery[recover]" id="confirm_recover" />
						<label class="form-check-label" for="confirm_recover"><?php I18N::__('Activate the reset using password recovery'); ?></label>
					</div>
				</div>
				
				<!-- Password generation -->
				<div class="tab-pane" id="generate" role="tabpanel" aria-labelledby="generate-tab">
					<p><?php I18N::__('The system automatically generates a strong password and sends it to the user by E-Mail.'); ?><br /><?php I18N::__('Please note that the password can only be sent if the user has entered a valid E-Mail address.'); ?></p>
					<?php
						if(!filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
							?>
								<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
									<?php I18N::__('The user will probably not receive an E-Mail because the provided <strong>E-Mail address is invalid</strong>.'); ?>
								</div>
							<?php
						}
					?>
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" role="switch" name="password_recovery[generate]" id="confirm_generate" />
						<label class="form-check-label" for="confirm_generate"><?php I18N::__('Activate the reset using password generation'); ?></label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group text-end">
		<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
	</div>
</div>