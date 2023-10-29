<?php
	use fruithost\Auth;
	use fruithost\Utils;
	use fruithost\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	$result = shell_exec('dpkg-query -W -f=\'${binary:Package};${Version};${binary:Summary};${Maintainer}\n\'');
	$lines	= explode(PHP_EOL, $result);
	// @ToDo group by names (for sample php-*)
	?>
	<table class="table table-borderless table-striped table-hover">
		<thead>
			<tr>
				<th scope="col"><?php I18N::__('Package'); ?></th>
				<th scope="col"><?php I18N::__('Version'); ?></th>
			</tr>
		</thead>
		<tbody>	
			<?php
				foreach($lines AS $line) {
					if(empty($line)) {
						continue;
					}
					$info = explode(';', $line);
					?>
						<tr>
							<td scope="col">
								<?php print $info[0]; ?>
								<p><small><?php print (isset($info[2]) ? $info[2] : ''); ?></small></p>
							</th>
							<td scope="col" class="text-right"><span class="badge badge-secondary"><?php print (isset($info[1]) ? $info[1] : ''); ?></span></th>
						</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	<?php
	$template->footer();
?>