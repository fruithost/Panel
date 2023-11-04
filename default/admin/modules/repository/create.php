<?php
	use fruithost\Auth;
	use fruithost\I18N;
?>
<div class="container">
	<div class="form-group row">
		<label for="repository_url" class="col-12 col-form-label col-form-label-sm"><?php I18N::__('Repository URL'); ?>:</label>
		<div class="col-12">
			<input type="text" class="form-control" name="repository_url" id="repository_url" aria-label="<?php I18N::__('Repository URL'); ?>" placeholder="https://github.com/<user>/<repository>/" />
		</div>
	</div>
</div>