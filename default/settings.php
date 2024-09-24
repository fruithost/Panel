<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	
	$template->header();
	?>
		<form method="post" action="<?php print $this->url('/settings' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
						<li class="breadcrumb-item<?php print (empty($submodule) ? ' active" aria-current="page' : ''); ?>">
							<a class="active" href="<?php print $this->url('/settings'); ?>"><?php I18N::__('Settings'); ?></a>
						</li>
						<?php
							if($tab == 'security') {
								?>
									<li class="breadcrumb-item active" aria-current="page">
										<a href="<?php print $this->url('/settings/security'); ?>"><?php I18N::__('Security'); ?></a>
									</li>
								<?php
							} else {
								?>
									<li class="breadcrumb-item active" aria-current="page">
										<a href="<?php print $this->url('/settings'); ?>"><?php I18N::__('Global Settings'); ?></a>
									</li>
								<?php
							}
						?>
					</ol>
				</nav>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Save'); ?></button>
				</div>
			</header>
			
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<?php
					foreach($template->getCore()->getHooks()->applyFilter('SETTINGS_TABS', [
						(object) [
							'id'		=> 'settings',
							'name'		=> I18N::get('Global Settings')
						], (object) [
							'id'		=> 'security',
							'name'		=> I18N::get('Security')
						]
					]) AS $index => $method) {
						$active = false;
						if(empty($tab) && $index == 0) {
							$active = true;
						} else if(!empty($tab) && $tab === $method->id) {
							$active = true;
						}
						
						printf('<li class="nav-item"><a class="nav-link%1$s" id="%2$s-tab" href="%3$s" role="tab">%4$s</a></li>', ($active ? ' active' : ''), $method->id, $this->url(sprintf('/%s%s', ($index == 0 ? '' : 'settings/'), $method->id)), $method->name);
					}
				?>
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
				<div class="tab-pane<?php print (empty($tab) ? ' show active' : ''); ?>" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					<p class="text-secondary p-4"><?php I18N::__('Global settings for your usability'); ?></p>
					
					<div class="container">
						<div class="form-group row">
							<label for="language" class="col-sm-2 col-form-label"><?php I18N::__('Language'); ?>:</label>
							<div class="col-sm-10">
								<select name="language" name="language" class="form-select">
								<?php
									foreach($languages AS $code => $language) {
										printf('<option value="%1$s"%2$s>%3$s</option>', $code, (Auth::getSettings('LANGUAGE', NULL, $template->getCore()->getSettings('LANGUAGE', 'en_US')) === $code ? ' SELECTED' : ''), $language);
									}
								?>
								</select>
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="time_format" class="col-sm-2 col-form-label"><?php I18N::__('Time Format'); ?>:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="time_format" name="time_format" value="<?php print Auth::getSettings('TIME_FORMAT', NULL, $template->getCore()->getSettings('TIME_FORMAT', 'd.m.Y - H:i:s')); ?>" />
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
												printf('<option value="%1$s"%2$s>%3$s</option>', $zone->value, (Auth::getSettings('TIME_ZONE', NULL, $template->getCore()->getSettings('TIME_ZONE', date_default_timezone_get())) === $zone->value ? ' SELECTED' : ''), $zone->name);
											}
											
											print('</optgroup>');
										}
									?>
								</select>
							</div>
						</div>
						<?php
							$template->getCore()->getHooks()->runAction('ACCOUNT_SETTINGS_GLOBAL');
						?>
						<div class="form-group text-end">
							<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
						</div>
					</div>
				</div>
				<div class="tab-pane<?php print (!empty($tab) && $tab === 'security' ? ' show active' : ''); ?>" id="security" role="tabpanel" aria-labelledby="security-tab">
					<p class="text-secondary p-4"><?php I18N::__('Some security settings for your safety.'); ?></p>
					
					<div class="container">
						<div class="form-group row">
							<div class="col-sm-2 text-right"></div>
							<div class="col-sm-10">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="2fa_enabled" name="2fa_enabled" value="true"<?php print (Auth::getSettings('2FA_ENABLED', NULL, 'false') === 'true' ? ' CHECKED' : ''); ?> />
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
									if(Auth::getSettings('2FA_ENABLED', NULL, 'false') === 'true' && !filter_var(Auth::getMail(), FILTER_VALIDATE_EMAIL)) {
										?>
											<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
												<?php I18N::__('Two-Factor authentication (2FA) is <strong>temporarily disabled</strong> because you did not provide a <strong>valid E-Mail address</strong>.'); ?>
												<br /><?php sprintf(I18N::get('Check your E-Mail address in the <a href="%s">account settings</a>.'), $template->url('/account')); ?>
											</div>
										<?php
									} else if(!filter_var(Auth::getMail(), FILTER_VALIDATE_EMAIL)) {
										?>
											<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
												<?php I18N::__('Two-Factor authentication (2FA) will be <strong>temporarily disabled</strong> because you did not provide a <strong>valid E-Mail address</strong>.'); ?>
												<br /><?php sprintf(I18N::get('Check your E-Mail address in the <a href="%s">account settings</a>.'), $template->url('/account')); ?>
											</div>
										<?php
									}
								?>
							</div>
						</div>
						<div class="form-group row">
							<label for="2fa_type" class="col-sm-2 col-form-label"><?php I18N::__('Type'); ?>:</label>
							<div class="col-sm-10">
								<select name="2fa_type" class="form-select">
									<?php
										$selected = Auth::getSettings('2FA_TYPE', NULL, 'mail');
										
										foreach($template->getCore()->getHooks()->applyFilter('2FA_METHODS', [
											(object) [
												'id'		=> 'mail',
												'name'		=> I18N::get('E-Mail'),
												'enabled'	=> true
											]
										]) AS $index => $method) {
											printf('<option value="%1$s"%3$s>%2$s%4$s</option>', $method->id, $method->name, (!$method->enabled ? ' DISABLED' : ($selected == $method->id ? ' SELECTED' : '')), (!$method->enabled ? sprintf(' (%s)', I18N::get('disabled')) : ''));
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group text-end">
							<button type="submit" name="action" value="save" class="btn btn-outline-success"><?php I18N::__('Save'); ?></button>
						</div>
					</div>
				</div>
				<?php
					foreach($template->getCore()->getHooks()->applyFilter('SETTINGS_TABS', [
						(object) [
							'id'			=> 'settings',
							'name'			=> I18N::get('Global Settings'),
							'__build_in'	=> true
						], (object) [
							'id'			=> 'security',
							'name'			=> I18N::get('Security'),
							'__build_in'	=> true
						]
					]) AS $index => $method) {
						if(isset($method->__build_in) && $method->__build_in) {
							continue;
						}
						
						$active = false;
						if(empty($tab) && $index == 0) {
							$active = true;
						} else if(!empty($tab) && $tab === $method->id) {
							$active = true;
						}
						
						?>
							<div class="tab-pane<?php print ($active ? ' show active' : ''); ?>" id="<?php print $method->id; ?>" role="tabpanel" aria-labelledby="<?php print $method->id; ?>-tab">
								<?php require_once($method->content); ?>
							</div>
						<?php
					}
				?>
			</div>
		</form>
	<?php
	$template->footer();
?>