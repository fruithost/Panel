<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\System\Utils;

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
	
	$ip_address = $_SERVER['SERVER_ADDR'];
	
	?>
	<div class="container-fluid mt-5">
		<div class="row">
			<div class="col-2 col-sm-6 col-md-2">
				<div class="card">
					<div class="card-header d-flex flex-row p-0">
						<span class="align-self-start flex-shrink-1 material-icons bg-dark rounded text-light p-2 m-2" style="font-size: 30px">router</span>
						<span class="align-self-start flex-fill p-0 ml-2 mt-1">
							<small>IP Address</small>
							<h4 class="p-0 m-o"><?php print $ip_address; ?></h4>
						</span>
					</div>
					<div class="card-body p-1 text-center">
						<a href="">Network settings</a>
					</div>
				</div>
			</div>
			<div class="col-4 col-sm-6 col-md-3 row">
				<div class="col-6 col-sm-12">
					<div class="card">
						<div class="card-header d-flex flex-row p-0">
							<span class="align-self-start flex-shrink-1 material-icons bg-dark rounded text-light p-2 m-2" style="font-size: 30px">watch_later</span>
							<span class="align-self-start flex-fill p-0 ml-2 mt-1">
								<small>Uptime</small>
								<h6 class="p-0 m-o">00:00</h6>
							</span>
						</div>
						<div class="card-body p-1 text-center">
							<a href="">Restart</a>
						</div>
					</div>
				</div>
				<div class="col-6 col-sm-12">
					<div class="card">
						<div class="card-header d-flex flex-row p-0">
							<span class="align-self-start flex-shrink-1 material-icons bg-dark rounded text-light p-2 m-2" style="font-size: 30px">watch_later</span>
							<span class="align-self-start flex-fill p-0 ml-2 mt-1">
								<small>Uptime</small>
								<h4 class="p-0 m-o">xx</h4>
							</span>
						</div>
						<div class="card-body p-1 text-center">
							<a href="">Network settings</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-5 col-sm-12">
				<div class="card">
					<div class="card-header d-flex flex-row">
						Hi
					</div>
					<div class="card-body">
						CONTENT
					</div>
				</div>
			</div>
		</div>
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