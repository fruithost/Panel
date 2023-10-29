<?php
	use fruithost\Auth;
	use fruithost\Encryption;
	use fruithost\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('LOGFILES::VIEW')) {
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
		
	<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">
			<a class="active" href="<?php print $this->url('/admin/logs'); ?>"><?php I18N::__('Logfiles'); ?></a>
		</h1>
		<div class="btn-toolbar mb-2 mb-md-0">
			<button type="submit" name="action" value="save" class="btn btn-sm btn-outline-primary"><?php I18N::__('Refresh'); ?></button>
		</div>
	</header>
	<div class="d-flex pb-2 mb-3">
		<div class="flex-fill logfile-container w-100 mr-2">
			<?php
				if(empty($logfile)) {
					?>
						<div class="alert alert-info mt-4" role="alert">
							<?php I18N::__('Please select a logfile to view it'); ?>
						</div>
					<?php
				} else {
					foreach($logfile AS $index => $line) {
						$line = preg_replace('/^\[([^\]]+)\]/Uis', '<span class="text-muted">$1</span>', $line);
						$color = 'default';
						
						if(preg_match('/AH00171/', $line)) {
							$color = 'info';
						} else if(preg_match('/AH00163/', $line)) {
							$color = 'warning';
						} else if(preg_match('/AH00094/', $line)) {
							$color = 'success';
						} else if(preg_match('/AH00169/', $line)) {
							$color = 'warning font-weight-bold';
						} else if(preg_match('/((p|P)ermission denied|Stack trace|^PHP (.?)\.|PHP Notice|error)/', $line)) {
							$color = 'danger font-weight-bold';
						}
						
						printf('<div class="logfile log-%1$s" data-number="%3$s">%2$s</div>', $color, $line, $index);
					}
				}
			?>
		</div>
		<div class="flex-fill ml-2">
			<ul class="filetree">
				<?php
					$index = 0;
					foreach($logfiles AS $path => $entry) {
						++$index;
						?>
						<li class="folder">
							<a data-toggle="collapse" href="#folder_<?php print $index; ?>" role="button"><i class="material-icons">folder</i> <?php print $path; ?></a>
							<ul class="files collapse" id="folder_<?php print $index; ?>">
								<?php
									foreach($entry AS $file) {
										printf('<li class="file"><a href="%s"><i class="material-icons">receipt</i> %s</a></li>', $this->url('/admin/logs/?file=' . Encryption::encrypt($path . $file, sprintf('LOGFILE::%s', Auth::getID()))), $file);
									}
								?>
							</ul>
						</li>
						<?php
					}
				?>
			</ul>
		</div>
	</div>
	<?php
	
	$template->footer();
?>