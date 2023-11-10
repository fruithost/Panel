<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

    $template->header();
	
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
	?>
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