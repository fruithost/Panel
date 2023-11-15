<?php
    use fruithost\Localization\I18N;

    $template->header();

    if(isset($error)) {
        ?>
            <div class="alert alert-danger mt-4" role="alert"><?php (is_array($error) ? var_dump($error) : print $error); ?></div>
        <?php
    } else if(isset($success)) {
        ?>
            <div class="alert alert-success mt-4" role="alert"><?php (is_array($success) ? var_dump($success) : print $success); ?></div>
        <?php
    }
?>
	<div class="container">
		<div class="form-group row">
			<label for="username" class="col-sm-2 col-form-label"><?php I18N::__('Username'); ?>:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="username" name="username" value="<?php print (!empty($username) ? $username : ''); ?>" />
			</div>
		</div>
		<div class="form-group row">
			<label for="email" class="col-sm-2 col-form-label"><?php I18N::__('E-Mail Address'); ?>:</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="email" name="email" value="<?php print (!empty($email) ? $email : ''); ?>" placeholder="your.mail@example.com" />
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
			<button type="submit" name="action" value="create" class="btn btn-outline-primary"><?php I18N::__('Create'); ?></button>
		</div>
	</div>
	<?php
	$template->footer();
?>