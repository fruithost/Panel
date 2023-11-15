<?php
    use fruithost\UI\Icon;

	$template->header();
	
	if(method_exists($module->getInstance(), 'frame') && !empty($module->getInstance()->frame())) {
		?>
			<iframe src="<?php print $module->getInstance()->frame(); ?>"></iframe>
		<?php
	} else {
?>
	<form method="post" action="<?php print $this->url(true); ?>">
		<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
					<li class="breadcrumb-item<?php print (empty($submodule) ? ' active" aria-current="page' : ''); ?>">
						<a href="<?php print $this->url(sprintf('/module/%s', $module->getDirectory())); ?>"><?php print $module->getInfo()->getName(); ?></a>
					</li>
					<?php
						if(!empty($submodule)) {
							?>
								<li class="breadcrumb-item active" aria-current="page">
									<a href="<?php print $this->url(sprintf('/module/%s/%s', $module->getDirectory(), $submodule)); ?>"><?php print $this->getCore()->getHooks()->applyFilter('SUBMODULE_NAME', $submodule); ?></a>
								</li>
							<?php
						}
					?>
				</ol>
			</nav>
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
												printf('<button type="button" name="%1$s" data-bs-toggle="modal" data-bs-target="#%4$s" class="btn btn-sm %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false), $entry->getModal());
											} else {
												printf('<button type="submit" name="action" value="%1$s" class="btn btn-sm %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
											}
										}
										
										printf('</div>');
									} else {
										if($button->hasModal()) {
											printf('<button type="button" name="%1$s" data-bs-toggle="modal" data-bs-target="#%4$s" class="btn mr-2 btn-sm %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false), $button->getModal());
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
		</header>
		<?php
			if(isset($error)) {
				?>
					<div class="alert alert-danger mt-4" role="alert"><?php (is_array($error) ? var_dump($error) : print $error) ?></div>
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