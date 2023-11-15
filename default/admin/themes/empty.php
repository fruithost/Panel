<?php
    use fruithost\Localization\I18N;
	use fruithost\UI\Icon;
?>
<div class="jumbotron text-center bg-transparent text-muted">
	<?php Icon::show('smiley-bad'); ?>
	<h2><?php I18N::__('No Themes available!'); ?></h2>
	<p class="lead"><?php I18N::__('Please install some Themes.'); ?></p>
</div>