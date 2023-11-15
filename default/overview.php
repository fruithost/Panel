<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;

    $template->header();
	
	if(!empty($admin) && $admin) {
		if(!Auth::hasPermission('*')) {
			?>
				<div class="alert alert-danger mt-4" role="alert">
					<strong><?php I18N::__('Access denied!'); ?></strong>
					<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
				</div>
			<?php
			$template->footer();
			exit();
		}
	} else if(empty($admin)) {
		?>
			<div class="alert alert-secondary alert-dismissible fade show mt-4 welcome" role="alert">
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php I18N::__('Close'); ?>"></button>
				<h4 class="alert-heading"><?php printf(I18N::get('Welcome to %s!'), $project_name); ?></h4>
				<p><?php I18N::__('We\'ve assembled some links to get you started'); ?>:</p>
				<div class="row">
					<div class="col-sm-4 text-center">
						<h6 class="text-left"><?php I18N::__('Get Started'); ?></h6>
						<a href="<?php print $template->url('/module/domains'); ?>" class="btn btn-primary btn-lg ml-4 mr-4 mt-2"><?php I18N::__('Create a Domain'); ?></a>
						<p><?php I18N::__('or'); ?> <a href="<?php print $template->url('/module/subdomains'); ?>"><?php I18N::__('create a new subdomain'); ?></a></p>
					</div>
					<div class="col-sm-4">
						<h6><?php I18N::__('Next Steps'); ?></h6>
						<ul class="list-unstyled">
							<li><?php Icon::show('ftp'); ?> <a href="<?php print $template->url('/module/ftp'); ?>"><?php I18N::__('Create an FTP Account'); ?></a></li>
							<li><?php Icon::show('database'); ?> <a href="<?php print $template->url('/module/database'); ?>"><?php I18N::__('Create a new Database'); ?></a></li>
							<li><?php Icon::show('mailbox'); ?> <a href="<?php print $template->url('/module/mailserver'); ?>"><?php I18N::__('Create a new Mailbox'); ?></a></li>
						</ul>
					</div>
					<div class="col-sm-4">
						<h6><?php I18N::__('More Actions'); ?></h6>
						<ul class="list-unstyled">
							<li><?php Icon::show('account'); ?> <a href="<?php print $template->url('/settings'); ?>"><?php I18N::__('Modify your Account settings'); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		<?php
	}
	
	if(Auth::getSettings('2FA_ENABLED', NULL, 'false') === 'true' && !filter_var(Auth::getMail(), FILTER_VALIDATE_EMAIL)) {
		?>
			<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
				<?php I18N::__('Two-Factor authentication (2FA) is <strong>temporarily disabled</strong> because you did not provide a <strong>valid E-Mail address</strong>.'); ?>
				<br /><?php printf(I18N::get('Check your E-Mail address in the <a href="%s">account settings</a>.'), $template->url('/account')); ?>
			</div>
		<?php
	}
	?>
		<ul class="list-unstyled row sortable">
			<?php
				if(!$navigation->isEmpty()) {
					foreach($navigation->getEntries() AS $category) {
						if($category->isEmpty()) {
							continue;
						}
						
						$visible = false;
						
						foreach($category->getEntries() AS $entry) {
							if($visible) {
								break;
							}
							
							if($entry->active) {
								$visible = true;
							}
						}
						?>
							<li class="col-md-6">
								<div class="card m-2">
									<div class="card-header container-fluid">
										<div class="row">
											<div class="col-10 pt-1">
												<?php print $category->getLabel(); ?>
											</div>
											<div class="col-2 text-end actions mr-0 pr-0">
												<?php
													Icon::show('arrow-down', [
														'classes' 		=> [ 'text-muted ' ],
														'attributes'	=> [
															'data-bs-toggle'	=> 'collapse',
															'data-bs-target'	=> sprintf('#overview_%s', $category->getID()),
															'aria-expanded'		=> false,
															'aria-controls'		=> sprintf('#overview_%s', $category->getID())
														]
													]);
												?>
											</div>
										</div>
									</div>
									<div class="card-body collapse show" id="overview_<?php print $category->getID(); ?>">
										<ul class="nav nav-pills">
											<?php
												foreach($category->getEntries() AS $entry) {
													?>
														<li class="text-center nav-item" style="order: <?php print $entry->order; ?>;">
															<a class="nav-link" href="<?php print $template->url($entry->url); ?>">
																<div class="icon"><?php print $entry->icon; ?></div>
																<div class="label"><?php print $entry->name; ?></div>
															</a>
														</li>
													<?php
												}
											?>
										</ul>
									</div>
								</div>
							</li>
						<?php
					}
				}
			?>
		</ul>
	<?php
	$template->footer();
?>