<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    use fruithost\Localization\I18N;
	use fruithost\UI\Icon;
?>
<div class="jumbotron text-center bg-transparent text-muted mt-5">
	<?php Icon::show('smiley-smile'); ?>
	<h2><?php I18N::__('No Errors!'); ?></h2>
	<p class="lead"><?php I18N::__('Everything runs smoothly.'); ?></p>
</div>