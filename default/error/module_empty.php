<?php
    use fruithost\Localization\I18N;
    use fruithost\UI\Icon;

    $template->header();
	?>
		<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
					<li class="breadcrumb-item active" aria-current="page">
						<a href="<?php print $this->url(); ?>"><?php I18N::__('Error'); ?></a>
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
			<div class="btn-toolbar mb-2 mb-md-0">
				<a href="<?php print $this->url(); ?>" class="btn btn-sm btn-outline-primary"><?php I18N::__('Reload'); ?></a>
			</div>
		</header>
		<div class="jumbotron text-center bg-transparent text-muted">
			<?php Icon::show('smiley-bad'); ?>
			<h2><?php I18N::__('Module Error'); ?></h2>
			<p class="lead"><?php I18N::__('The specified module could not be loaded.'); ?></p>
		</div>
	<?php
	$template->footer();
?>