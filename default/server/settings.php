<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

    $template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
		<form method="post" action="<?php print $this->url('/server/settings' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
						<li class="breadcrumb-item active" aria-current="page">
							<a href="<?php print $this->url('/server/settings'); ?>"><?php I18N::__('Settings'); ?></a>
						</li>
					</ol>
				</nav>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Save'); ?></button>
				</div>
			</header>
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
				<div class="tab-pane<?php print (empty($tab) ? ' show active' : ''); ?>" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					<p class="text-secondary p-4"><?php I18N::__('Global settings for the Panel'); ?></p>
					
					<div class="container">
						<div class="form-group row">
							<label for="project_name" class="col-sm-2 col-form-label"><?php I18N::__('Project name'); ?>:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="project_name" name="project_name" value="<?php print $template->getCore()->getSettings('PROJECT_NAME', 'fruithost'); ?>" />
							</div>
						</div>
						<div class="form-group row">
							<label for="project_copyright" class="col-sm-2 col-form-label"><?php I18N::__('Show Copyright'); ?>:</label>
							<div class="col-sm-10">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="project_copyright" value="true" id="project_copyright"<?php print ($template->getCore()->getSettings('PROJECT_COPYRIGHT', true) ? ' CHECKED' : ''); ?>/>
									<label class="form-check-label" for="project_copyright">
										<?php I18N::__('Shows the copyright in the footer'); ?>
									</label>
								</div>
							</div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="language" class="col-sm-2 col-form-label"><?php I18N::__('Default Language'); ?>:</label>
							<div class="col-sm-10">
								<select name="language" class="form-select">
								<?php
									foreach(I18N::getLanguages() AS $code => $language) {
										printf('<option value="%1$s"%2$s>%3$s</option>', $code, ($template->getCore()->getSettings('LANGUAGE', 'en_US') === $code ? ' SELECTED' : ''), $language);
									}
								?>
								</select>
                            </div>
						</div>
						<hr class="mb-4" />
						<div class="form-group row">
							<label for="time_format" class="col-sm-2 col-form-label"><?php I18N::__('Default Time Format'); ?>:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="time_format" name="time_format" value="<?php print $template->getCore()->getSettings('TIME_FORMAT', 'd.m.Y - H:i:s'); ?>" />
							</div>
						</div>
						<div class="form-group row">
							<label for="time_zone" class="col-sm-2 col-form-label"><?php I18N::__('Default Timezone'); ?>:</label>
							<div class="col-sm-10">
								<select name="time_zone" id="time_zone" class="form-select">
									<?php
										foreach(json_decode(file_get_contents(dirname(PATH) . '/config/timezones.json'), false) AS $category) {
											printf('<optgroup label="%s">', $category->group);
											
											foreach($category->zones AS $zone) {
												printf('<option value="%1$s"%2$s>%3$s</option>', $zone->value, ($template->getCore()->getSettings('TIME_ZONE', date_default_timezone_get()) === $zone->value ? ' SELECTED' : ''), $zone->name);
											}
											
											print('</optgroup>');
										}
									?>
								</select>
							</div>
						</div>
						<?php
							$template->getCore()->getHooks()->runAction('SERVER_SETTINGS');
						?>
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