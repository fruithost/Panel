<?php
	use fruithost\Auth;
	use fruithost\Utils;
	use fruithost\I18N;
	use Gauge\Gauge;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::VIEW')) {
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
	<div class="container mt-5 mb-5">
		<ul class="list-unstyled row list-group list-group-horizontal list-group-flush text-center">
			<li class="col">
				<img src="<?php print (new Gauge())->render('Memory', $memory['free'], 0, $memory['total'])->base64(); ?>" />
			</li>
			<li class="col">
				<img src="<?php print (new Gauge())->render('CPU Load', $memory['free'], 0, $memory['total'])->base64(); ?>" />
			</li>
			<li class="col">
				<img src="<?php print (new Gauge())->render('Swap', $memory['free_swap'], 0, $memory['total_swap'])->base64(); ?>" />
			</li>
			<li class="col">
				<img src="<?php print (new Gauge())->render('Cache', $memory['cache'], $memory['free'], $memory['total'])->base64(); ?>" />
			</li>
			<li class="col">
				<img src="<?php print (new Gauge())->render('Buffer', $memory['buffer'], 0, $memory['total'])->base64(); ?>" />
			</li>
		</ul>
	</div>
	<div class="container mt-5">
		<div class="row">
			<div class="col-md-6">
				<h4><?php I18N::__('System Properties'); ?></h4>
				<table class="table">
					<tr>
						<th><?php I18N::__('Hostname'); ?></th>
						<td><?php print $hostname; ?></td>
					</tr>
					<tr>
						<th><?php I18N::__('Time'); ?></th>
						<td><?php print $time_system; ?></td>
					</tr>
					<tr>
						<th><?php I18N::__('System'); ?></th>
						<td><?php print $os; ?></td>
					</tr>
					<tr>
						<th><?php I18N::__('Kernel'); ?></th>
						<td><?php print $kernel; ?></td>
					</tr>
					<tr>
						<th><?php I18N::__('Uptime'); ?></th>
						<td><?php print $uptime; ?></td>
					</tr>
				</table>
				
				<h4><?php I18N::__('Daemon'); ?></h4>
				<table class="table">
					<tr>
						<th><?php I18N::__('Last Start'); ?></th>
						<td><?php print $daemon['start']; ?> (<?php printf(I18N::get('%s ago'), Utils::getTimeDifference($daemon['started'])); ?>)</td>
					</tr>
					<tr>
						<th><?php I18N::__('Last End'); ?></th>
						<td><?php print $daemon['end']; ?> (<?php printf(I18N::get('%s ago'), Utils::getTimeDifference($daemon['ended'])); ?>)</td>
					</tr>
					<tr>
						<th><?php I18N::__('Last Time'); ?></th>
						<td><?php printf(I18N::get('%d Seconds'), $daemon['time']); ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<h4><?php I18N::__('Mounted Drives'); ?></h4>
				<?php
					foreach($disks AS $disk) {
						?>
						<div class="mb-2">
							<div class="d-flex">
								<i class="material-icons mr-1"><?php
									switch($disk['type']) {
										case 'devtmpfs':
										case 'tmpfs':
											print 'memory';
										break;
										default:
											print 'storage';
										break;
									}
								?></i>
								<strong><?php print $disk['mount']; ?></strong>
							</div>
							<div class="bg-secondary" style="height: 15px;" data-percentage="<?php printf('%s%s', $disk['percent'], ($disk['used'] === '0' ? '' : sprintf(' (%s)', $disk['used']))); ?>">
								<div class="bg-success" style="height: 100%; width: <?php print $disk['percent']; ?>"></div>
							</div>
							<small class="text-muted"><?php printf(I18N::get('Type: %s | FileSystem: %s | Size: %s / %s'), $disk['type'], $disk['filesystem'], $disk['avail'], $disk['size']); ?></small>
						</div>
						<?php
					}
				?>
			</div>
		</div>
	</div>
	<?php
	$template->footer();
?>