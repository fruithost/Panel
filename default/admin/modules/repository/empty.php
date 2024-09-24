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
<div class="jumbotron text-center bg-transparent text-muted">
	<?php Icon::show('smiley-bad'); ?>
	<h2><?php I18N::__('No Repositorys available!'); ?></h2>
	<p class="lead"><?php I18N::__('Please adding some Repositorys to keep updates.'); ?></p>
	<button type="button" name="add_repository" data-toggle="modal" data-target="#add_repository" class="btn btn-lg btn-primary mt-4"><?php I18N::__('Add Repository'); ?></button>
</div>