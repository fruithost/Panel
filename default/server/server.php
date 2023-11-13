<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\System\Utils;
    use fruithost\UI\Icon;

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
	<div class="container-fluid mt-5">
		<div class="row">
			<div class="col-xl-3 col-md-6 col-sm-6">
				<div class="card text-break">
					<div class="card-header d-inline-flex flex-row p-0">
						<?php
							Icon::show('ip', [
								'classes' 		=> [ 'align-self-start', 'flex-shrink-1', 'bg-dark', 'rounded', 'text-light', 'p-2', 'm-2' ],
								'attributes'	=> [ 'style' => 'font-size: 30px' ],
								'data-current'	=> 'theme-auto'
							]);
						?>
						<span class="align-self-start flex-fill p-0 ml-2 mt-1">
							<small><?php I18N::__('IP Address'); ?></small>
							<h4 class="p-0 m-o"><?php print $ip_address; ?></h4>
						</span>
					</div>
					<div class="card-body p-1 text-center">
						<a class="icon-link icon-link-hover text-decoration-none" style="--bs-link-hover-color-rgb: 25, 135, 84;" href="<?php print $template->url('/server/network'); ?>">
							<?php
								Icon::show('network', [
									'classes' => [ 'mb-2' ]
								]);
								
								I18N::__('Network settings');
							?>
						</a>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-md-6 col-sm-6">
				<div class="card text-break">
					<div class="card-header d-flex flex-row p-0">
						<?php
							Icon::show('uptime', [
								'classes' 		=> [ 'align-self-start', 'flex-shrink-1', 'bg-dark', 'rounded', 'text-light', 'p-2', 'm-2' ],
								'attributes'	=> [ 'style' => 'font-size: 30px' ]
							]);
						?>
						<span class="align-self-start flex-fill p-0 ml-2 mt-1">
							<small><?php I18N::__('Uptime'); ?></small>
							<h4 class="p-0 m-o"><?php print $uptime; ?></h4>
						</span>
					</div>
					<div class="card-body p-1 text-center">
						<a class="icon-link icon-link-hover text-decoration-none" style="--bs-link-hover-color-rgb: 25, 135, 84;" href="<?php print $template->url('/server/reboot'); ?>">
							<?php
								Icon::show('restart', [
									'classes' => [ 'mb-1' ]
								]);
								
								I18N::__('Restart');
							?>
						</a>
					</div>
				</div>
			</div>
			<div class="col-xl-5 col-md-12 mt-sm-3 mt-xl-0">
				<div class="card">
					<div class="card-header d-flex flex-row">
						<?php I18N::__('System'); ?>
					</div>
					<div class="card-body p-0">
						<table class="table m-0 table-borderless table-striped">
							<tr>
								<td rowspan="4" class="text-center align-middle border-end">
									<img height="50px" class="m-2" src="<?php print $template->url('/assets/systems/debian.svg'); ?>" alt="Debian" />
								</td>
								<td>
									<strong><?php I18N::__('System'); ?></strong>
								</td>
								<td>
									<?php print $os; ?>, <?php print $machine_type; ?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php I18N::__('Kernel'); ?></strong>
								</td>
								<td>
									<?php print $kernel; ?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php I18N::__('System Time'); ?></strong>
								</td>
								<td>
									<?php print $time_system; ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr />
	<div class="container">
		<div class="row">
			<!-- System Properties -->
			<div class="col-xl-6 col-md-12 mt-sm-12 mt-2">
				<div class="card">
					<div class="card-header d-flex flex-row">
						<?php I18N::__('System Properties'); ?>
					</div>
					<div class="card-body p-0">
						<table class="table m-0 table-borderless table-striped">
							<tr>
								<td>
									<strong><?php I18N::__('Hostname'); ?></strong>
								</td>
								<td>
									<?php
										print $hostname;
										
										if($hostname !== $hostname_panel) {
											Icon::show('warning', [
												'classes'		=> [ 'text-warning', 'ml-1' ],
												'attributes'	=> [
													'data-bs-toggle'	=> 'hover',
													'data-bs-content'	=> sprintf(I18N::get('The hostname <strong>%s</strong> does not match the panel domain (<strong>%s</strong>).'), $hostname, $hostname_panel),
													'data-bs-title'		=> sprintf('<small class=\'text-warning\'>%s</small>', I18N::get('Warning!'))
												]
											]);
										}
									?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php I18N::__('System Time'); ?></strong>
								</td>
								<td>
									<?php print $time_system; ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			
			<!-- Daemon -->
			<div class="col-xl-6 col-md-12 mt-sm-12 mt-2">
				<div class="card">
					<div class="card-header d-flex flex-row">
						<?php I18N::__('Daemon'); ?>
					</div>
					<div class="card-body p-0">
						<table class="table m-0 table-borderless table-striped">
							<tr>
								<td>
									<strong><?php I18N::__('Last Start'); ?></strong>
								</td>
								<td>
									<?php print $daemon['start']; ?> (<?php printf(I18N::get('%s ago'), Utils::getTimeDifference($daemon['started'])); ?>)
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php I18N::__('Last End'); ?></strong>
								</td>
								<td>
									<?php print $daemon['end']; ?> (<?php printf(I18N::get('%s ago'), Utils::getTimeDifference($daemon['ended'])); ?>)
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php I18N::__('Last Time'); ?></strong>
								</td>
								<td>
									<?php printf(I18N::get('%d Seconds'), $daemon['time']); ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- Storage -->
			<div class="col-xl-6 col-md-12 mt-sm-12 mt-2">
				<div class="card">
					<div class="card-header d-flex flex-row">
						<?php I18N::__('Mounted Drives'); ?>
					</div>
					<div class="card-body p-3">
						<?php
							foreach($disks['storage'] AS $disk) {
								?>
								<div class="mb-2">
									<div class="d-flex">
										<div class="p-2 flex-shrink-1">
											<?php
												Icon::show('storage', [
													'classes' 		=> [ 'mr' ]
												]);
											?>
										</div>
										<div class="p-2 flex-fill">
											<strong class="ml-2"><?php print $disk['mount']; ?></strong>
										</div>
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
			
			<!-- Memory -->
			<div class="col-xl-6 col-md-12 mt-sm-12 mt-2">
				<div class="card">
					<div class="card-header d-flex flex-row">
						<?php I18N::__('Memory'); ?>
					</div>
					<div class="card-body p-3">
						<?php
							foreach($disks['memory'] AS $disk) {
								?>
								<div class="mb-2">
									<div class="d-flex">
										<div class="p-2 flex-shrink-1">
											<?php
												Icon::show('memory', [
													'classes' 		=> [ 'mr' ]
												]);
											?>
										</div>
										<div class="p-2 flex-fill">
											<strong class="ml-2"><?php print $disk['mount']; ?></strong>
										</div>
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
		</div>
	</div>
	<?php
	$template->footer();
?>