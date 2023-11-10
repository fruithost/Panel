<?php
    use fruithost\Localization\I18N;

    $template->header();
    ?>
		<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">
				<a class="active" href="<?php print $this->url(); ?>"><?php I18N::__('Error'); ?></a>
			</h1>
			<div class="btn-toolbar mb-2 mb-md-0">
				<a href="<?php print $this->url(); ?>" class="btn btn-sm btn-outline-primary"><?php I18N::__('Reload'); ?></a>
			</div>
		</header>
		<div class="jumbotron text-center bg-transparent text-muted">
			<i class="material-icons">error</i>
			<h2><?php I18N::__('Module Error'); ?></h2>
			<p class="lead"><?php I18N::__('The specified module could not be loaded.'); ?></p>
		</div>
	<?php
	$template->footer();
?>