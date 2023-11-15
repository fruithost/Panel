<?php
    use fruithost\Localization\I18N;
?>
<p></p>
<div class="container">
	<div class="form-group row">
		<label for="id" class="col-sm-2 col-form-label"><?php I18N::__('ID'); ?>:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="id" name="id" value="<?php print $user->getID(); ?>" DISABLED />
		</div>
	</div>
	<div class="form-group row">
		<label for="username" class="col-sm-2 col-form-label"><?php I18N::__('Username'); ?>:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="username" name="username" value="<?php print $user->getUsername(); ?>" DISABLED />
		</div>
	</div>
	<div class="form-group row">
		<label for="email" class="col-sm-2 col-form-label"><?php I18N::__('E-Mail Address'); ?>:</label>
		<div class="col-sm-10">
			<input type="email" class="form-control" id="email" name="email" value="<?php print $user->getMail(); ?>" placeholder="your.mail@example.com" />
		</div>
	</div>
	<hr class="mb-4" />
	<div class="form-group row">
		<label for="name_first" class="col-sm-2 col-form-label"><?php I18N::__('Full Name'); ?>:</label>
		<div class="col-sm-10 container">
			<div class="row">
				<div class="col-6">
					<input type="text" class="form-control" id="name_first" name="name_first" value="<?php print $user->getFirstName(); ?>" />
				</div>
				<div class="col-6">
					<input type="text" class="form-control" id="name_last" name="name_last" value="<?php print $user->getLastName(); ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="phone" class="col-sm-2 col-form-label"><?php I18N::__('Phone Number'); ?>:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="phone" name="phone" value="<?php print $user->getPhoneNumber(); ?>" />
		</div>
	</div>
	<div class="form-group row">
		<label for="address" class="col-sm-2 col-form-label"><?php I18N::__('Postal Address'); ?>:</label>
		<div class="col-sm-10">
			<textarea class="form-control" id="address" name="address"><?php print $user->getAddress(); ?></textarea>
		</div>
	</div>
	<div class="form-group text-end">
		<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
	</div>
</div>