<?php
    use fruithost\Localization\I18N;
    use fruithost\UI\Icon;

    $template->header();
	?>
	<main class="form-signin w-100 m-auto text-center">
		<form method="post" action="<?php print $template->url(true); ?>">
			<img class="mb-4" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE4AAABjCAYAAAFCnwZjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozMjkwNjdGQTdEMzMxMUU4QkQ0QkUzMTYwNTRFNTQzOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozMjkwNjdGQjdEMzMxMUU4QkQ0QkUzMTYwNTRFNTQzOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjMyOTA2N0Y4N0QzMzExRThCRDRCRTMxNjA1NEU1NDM4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjMyOTA2N0Y5N0QzMzExRThCRDRCRTMxNjA1NEU1NDM4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+wx6ypAAAEYNJREFUeNpi/P//PwMI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" alt="" />
			<h1 class="h3 mb-3 fw-normal"><?php I18N::__('Reset your Password'); ?></h1>
	
			<?php
				if(isset($error)) {
					?>
						<div class="alert alert-danger" role="alert"><?php print $error; ?></div>
					<?php
				} else if(isset($success)) {
					?>
						<div class="alert alert-success" role="alert"><?php print $success; ?></div>
					<?php
				}
				
				if(!isset($success)) {
					if(isset($changeable) && $changeable) {
						?>
							<div class="form-floating">
								<input type="password" class="form-control" id="password_new" name="password_new" placeholder="<?php I18N::__('New Password'); ?>" required autofocus />
								<label for="password_new"><?php I18N::__('New Password'); ?></label>
							</div>
							<div class="form-floating">
								<input type="password" class="form-control" id="password_repeated" name="password_repeated" placeholder="<?php I18N::__('Password Confirmation'); ?>" required />
								<label for="password_repeated"><?php I18N::__('Password Confirmation'); ?></label>
							</div>
						<?php
					} else {
						?>
							<div class="form-floating">
								<input type="email" class="form-control" id="email" name="email" placeholder="<?php I18N::__('E-Mail Address'); ?>" required autofocus />
								<label for="email"><?php I18N::__('E-Mail Address'); ?></label>
							</div>
						<?php
					}
				}
			?>
						
			<div class="mb-3 mt-3 text-start">
				<a class="icon-link icon-link-hover" style="--bs-link-hover-color-rgb: 25, 135, 84;" href="<?php print $template->url('/login'); ?>">
					<i class="bi-arrow-left"></i>
					<?php I18N::__('Back to the login'); ?>
				</a>
			</div>
			
			<?php
				if(!isset($success)) {
					?>
						<div class="btn-group btn-group-lg" style="width: 100%;">
							<button class="btn btn-primary col-9" name="action" value="lost-password" type="submit">
								<?php I18N::__('Reset Password'); ?>
							</button>
							<button type="button" class="col-3 btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
								<?php Icon::show('language'); ?>
								<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu">
								<?php
									foreach(I18N::getLanguages() AS $code => $language) {
										printf('<li><a class="dropdown-item" href="%s">%s</a></li>', $template->url('/login?lang=' . $code), $language);
									}
								?>
							</ul>
						</div>
					<?php
				}
			?>

			<p class="mt-5 mb-3 text-body-secondary">
				<?php
					if($project_copyright) {
						?>
							<p class="mt-5 mb-3 text-muted">Powered by <a href="https://fruithost.de/" target="_blank">fruithost</a></p>
						<?php
					}
				?>
			</p>
		</form>
	</main>
	<?php
	$template->footer();
?>