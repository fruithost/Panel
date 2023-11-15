<?php
    use fruithost\Localization\I18N;
	use fruithost\UI\Icon;
?>
<div class="jumbotron text-center bg-transparent text-muted">
	<?php Icon::show('smiley-bad'); ?>
	<h2><?php I18N::__('No Modules available!'); ?></h2>
	<p class="lead"><?php I18N::__('Please install some Modules.'); ?></p>
</div>