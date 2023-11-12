<?php
    use fruithost\Localization\I18N;
?>
<p></p>
<div class="container">
	<div class="form-group row">
		<label for="language" class="col-sm-2 col-form-label"><?php I18N::__('Language'); ?>:</label>
		<div class="col-sm-10">
			<select name="language" id="language" class="form-select">
			<?php
				foreach(I18N::getLanguages() AS $code => $language) {
					printf('<option value="%1$s"%2$s>%3$s</option>', $code, ($user->getSettings('LANGUAGE', null, $template->getCore()->getSettings('LANGUAGE', 'en_US')) === $code ? ' SELECTED' : ''), $language);
				}
			?>
			</select>
		</div>
	</div>
	<hr class="mb-4" />
	<div class="form-group row">
		<label for="time_format" class="col-sm-2 col-form-label"><?php I18N::__('Time Format'); ?>:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="time_format" name="time_format" value="<?php print $user->getSettings('TIME_FORMAT', null, $template->getCore()->getSettings('TIME_FORMAT', 'd.m.Y - H:i:s')); ?>" />
		</div>
	</div>
	<div class="form-group row">
		<label for="time_zone" class="col-sm-2 col-form-label"><?php I18N::__('Timezone'); ?>:</label>
		<div class="col-sm-10">
			<select name="time_zone" id="time_zone" class="form-select">
				<?php
					foreach($timezones AS $category) {
						printf('<optgroup label="%s">', $category->group);
						
						foreach($category->zones AS $zone) {
							printf('<option value="%1$s"%2$s>%3$s</option>', $zone->value, ($user->getSettings('TIME_ZONE', null, $template->getCore()->getSettings('TIME_ZONE', date_default_timezone_get())) === $zone->value ? ' SELECTED' : ''), $zone->name);
						}
						
						print('</optgroup>');
					}
				?>
			</select>
		</div>
	</div>
	<?php
		$template->getCore()->getHooks()->runAction('ACCOUNT_SETTINGS_GLOBAL', [
			'user' => $user
		]);
	?>
	<div class="form-group text-end">
		<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
	</div>
</div>