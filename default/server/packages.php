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
    if (empty($result)) {
        $lines = [];
    } else {
        $lines = explode(PHP_EOL, $result);
    }
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
                if (empty($lines)) {
                    ?>
                        <tr>
                            <td scope="col" colspan="2"><?php I18N::__('No packages found.'); ?></td>
                        </tr>
                    <?php
                }
				foreach($lines AS $line) {
					if(empty($line)) {
						continue;
					}
					$info = explode(';', $line);
					?>
						<tr>
							<td scope="col">
								<?php print $info[0]; ?>
								<p><small><?php print ($info[2] ?? ''); ?></small></p>
							</td>
							<td scope="col" class="text-right"><span class="badge badge-secondary"><?php print ($info[1] ?? ''); ?></span></td>
						</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	<?php
	$template->footer();
?>