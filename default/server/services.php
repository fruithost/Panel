<?php
	use fruithost\Auth;
	use fruithost\Utils;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	#systemctl status <name>
	$result = json_decode(shell_exec('systemctl list-units --type=service --output=json-pretty'));
	?>
	<table class="table table-borderless table-striped table-hover">
		<thead>
			<tr>
				<th scope="col" colspan="2">Service</th>
				<th scope="col">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($result AS $service) {
					?>
						<tr>
							<td>
								<span class="d-block badge badge-pill module-badge badge-<?php print ($service->sub == 'running' ? 'success' : 'danger');?>" data-toggle="tooltip" title="<?php print ($service->sub == 'running' ? 'Service is running.' : 'Service is stopped.');?>"></span>
							</td>
							<td>
								<?php print $service->unit; ?>
								<p><small><?php print $service->description; ?></small></p>
							</td>
							<td class="text-right">
								<div class="btn-group mr-2">
									<?php
										if($service->sub == 'running') {
											?>
												<button type="submit" name="action" value="restart" class="btn btn-sm btn-outline-warning p-0 m-0"><i class="material-icons">loop</i></button>
												<button type="submit" name="action" value="stop" class="btn btn-sm btn-outline-danger p-0 m-0"><i class="material-icons">stop</i></button>
											<?php
										} else {
											?>
												<button type="submit" name="action" value="start" class="btn btn-sm btn-outline-success p-0 m-0"><i class="material-icons">play_arrow</i></button>
											<?php
										}
									?>
								</div>
								<button type="submit" name="action" value="start" class="btn btn-sm btn-outline-info">Info</button>
							</td>
						</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	<?php
	$template->footer();
?>