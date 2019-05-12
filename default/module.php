<?php
	$template->header();
	
	if(method_exists($module->getInstance(), 'frame') && !empty($module->getInstance()->frame())) {
		?>
			<iframe src="<?php print $module->getInstance()->frame(); ?>"></iframe>
		<?php
	} else {
?>
	<form method="post" action="<?php print $this->url(true); ?>">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">
				<a href="<?php print $this->url(sprintf('/module/%s', $module->getInfo()->getName())); ?>"><?php print $module->getInfo()->getName(); ?></a>
				<?php
					if(!empty($submodule)) {
						?>
							<i class="material-icons">arrow_right</i>
							<a href="<?php print $this->url(sprintf('/module/%s/%s', $module->getInfo()->getName(), $submodule)); ?>"><?php print $submodule; ?></a>
						<?php
					}
				?>
			</h1>
			<?php
				$buttons = $this->getCore()->getHooks()->applyFilter('buttons', []);
				
				if(!empty($buttons)) {
					?>
						<div class="btn-toolbar mb-2 mb-md-0">
							<?php
								foreach($buttons AS $button) {
									if(is_array($button) && !is_object($button)) {
										printf('<div class="btn-group mr-2">');
										
										foreach($button AS $entry) {
											if($entry->hasModal()) {
												printf('<button type="button" name="%1$s" data-toggle="modal" data-target="#%4$s" class="btn btn-sm %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false), $entry->getModal());
											} else {
												printf('<button type="submit" name="action" value="%1$s" class="btn btn-sm %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
											}
										}
										
										printf('</div>');
									} else {
										if($button->hasModal()) {
											printf('<button type="button" name="%1$s" data-toggle="modal" data-target="#%4$s" class="btn mr-2 btn-sm %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false), $button->getModal());
										} else {
											printf('<button type="submit" name="action" value="%1$s" class="btn mr-2 btn-sm %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false));
										}
									}
								}
							?>
						</div>
					<?php
				}
			?>
		</div>
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
			
			$module->getInstance()->content($submodule);
		?>
		</form>
		<?php
	}
	
	$template->footer();
?>