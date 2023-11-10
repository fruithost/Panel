<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

    $template->header();
	?>
		<div class="alert alert-secondary alert-dismissible fade show mt-4 welcome" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="alert-heading"><?php sprintf(I18N::get('Welcome to %s!'), $project_name); ?></h4>
			<p><?php I18N::__('We\'ve assembled some links to get you started'); ?>:</p>
			<div class="row">
				<div class="col-sm-4 text-center">
					<h6 class="text-left"><?php I18N::__('Get Started'); ?></h6>
					<a href="<?php print $template->url('/module/domains'); ?>" class="btn btn-primary btn-lg ml-4 mr-4 mt-2"><?php I18N::__('Create a Domain'); ?></a>
					<p><?php I18N::__(''); ?>or <a href="<?php print $template->url('/module/subdomains'); ?>"><?php I18N::__('create a new subdomain'); ?></a></p>
				</div>
				<div class="col-sm-4">
					<h6><?php I18N::__('Next Steps'); ?></h6>
					<ul class="list-unstyled">
						<li><i class="material-icons">folder</i> <a href="<?php print $template->url('/module/ftp'); ?>"><?php I18N::__('Create an FTP Account'); ?></a></li>
						<li><i class="material-icons">lock</i> <a href="<?php print $template->url('/module/database'); ?>"><?php I18N::__('Create a new Database'); ?></a></li>
						<li><i class="material-icons">mail_outline</i> <a href="<?php print $template->url('/module/mailserver'); ?>"><?php I18N::__('Create a new Mailbox'); ?></a></li>
					</ul>
				</div>
				<div class="col-sm-4">
					<h6><?php I18N::__('More Actions'); ?></h6>
					<ul class="list-unstyled">
						<li><i class="material-icons">settings</i> <a href="<?php print $template->url('/settings'); ?>"><?php I18N::__('Modify your Account settings'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
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
									<div class="card-header container">
										<div class="row">
											<div class="col pt-1">
												<?php print $category->getLabel(); ?>
											</div>
											<div class="col text-right actions mr-0 pr-0">
												<i class="material-icons" data-toggle="collapse" data-target="#overview_<?php print $category->getID(); ?>" aria-expanded="false" aria-controls="overview_<?php print $category->getID(); ?>">arrow_drop_up</i>
											</div>
										</div>
									</div>
									<div class="card-body collapse show" id="overview_<?php print $category->getID(); ?>">
										<ul class="list-unstyled list-group list-group-horizontal list-group-flush">
											<?php
												foreach($category->getEntries() AS $entry) {
													?>
														<li class="text-center" style="order: <?php print $entry->order; ?>;">
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