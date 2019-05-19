<?php
	use fruithost\Auth;
	use Gauge\Gauge;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	
	?>
	<div class="container mt-5 mb-5">
		<div class="row">
			<div class="col">
				<img src="<?php print (new Gauge())->render('Memory', $memory['free'], 0, $memory['total'])->base64(); ?>" />
			</div>
			<div class="col">
				<img src="<?php print (new Gauge())->render('CPU Load', $memory['free'], 0, $memory['total'])->base64(); ?>" />
			</div>
			<div class="col">
				<img src="<?php print (new Gauge())->render('Swap', $memory['free_swap'], 0, $memory['total_swap'])->base64(); ?>" />
			</div>
			<div class="col">
				<img src="<?php print (new Gauge())->render('Cache', $memory['cache'], $memory['free'], $memory['total'])->base64(); ?>" />
			</div>
			<div class="col">
				<img src="<?php print (new Gauge())->render('Buffer', $memory['buffer'], 0, $memory['total'])->base64(); ?>" />
			</div>
		</div>
	</div>
	<div class="container mt-5">
		<div class="row">
			<div class="col-6">
				<h4>System Properties</h4>
				<table class="table">
					<tr>
						<th>Hostname</th>
						<td><?php print $hostname; ?></td>
					</tr>
					<tr>
						<th>Time</th>
						<td><?php print $time_system; ?></td>
					</tr>
					<tr>
						<th>System</th>
						<td><?php print $os; ?></td>
					</tr>
					<tr>
						<th>Kernel</th>
						<td><?php print $kernel; ?></td>
					</tr>
					<tr>
						<th>Uptime</th>
						<td><?php print $uptime; ?></td>
					</tr>
				</table>
				
				<h4>Daemon</h4>
				<table class="table">
					<tr>
						<th>Last Start</th>
						<td><?php print $daemon['start']; ?></td>
					</tr>
					<tr>
						<th>Last End</th>
						<td><?php print $daemon['end']; ?></td>
					</tr>
					<tr>
						<th>Last Time</th>
						<td><?php print $daemon['time']; ?> Seconds</td>
					</tr>
				</table>
			</div>
			<div class="col-6">
				<h4>Mounted Drives</h4>
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
							<small class="text-muted">Type: <?php print $disk['type']; ?> | FileSystem: <?php print $disk['filesystem']; ?> | Size: <?php print $disk['avail']; ?> / <?php print $disk['size']; ?></small>
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