<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;

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

	#systemctl status <name>
	if(defined('DEMO') && DEMO) {
		$result = [];
	} else {
        $json = shell_exec('systemctl list-units --type=service --output=json-pretty'); //TODO convert for other OS envs as well
        if (empty($json)) {
            $result = [];
        } else {
            $result = json_decode($json, false);
        }
	}
	?>
	<form method="post" action="<?php print $this->url('/server/services' . (!empty($tab) ? '/' . $tab : '')); ?>">
		<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
					<li class="breadcrumb-item active" aria-current="page">
						<a href="<?php print $this->url('/server/services'); ?>"><?php I18N::__('Services'); ?></a>
					</li>
				</ol>
			</nav>
			<div class="btn-toolbar mb-2 mb-md-0">
				<button type="submit" name="action" value="refresh" class="btn btn-sm btn-outline-primary"><?php I18N::__('Refresh'); ?></button>
			</div>
		</header>
	
		<div class="border rounded overflow-hidden mb-5">
			<table class="table table-borderless table-striped table-hover mb-0">
				<thead>
					<tr>
						<th class="bg-secondary-subtle" scope="col" colspan="2"><?php I18N::__('Service'); ?></th>
						<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Actions'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						if (empty($result)) {
							?>
							<tr>
								<td scope="col" colspan="3"><?php I18N::__('No services found.'); ?></td>
							</tr>
							<?php
						}
						
						foreach($result AS $service) {
							if(empty($service) || empty(trim($service->unit))) {
								continue;
							}
							?>
								<tr>
									<td width="1px">
										<span class="d-block badge badge-pill module-badge badge-<?php print ($service->sub === 'running' ? 'success' : 'danger');?>" data-toggle="tooltip" title="<?php print ($service->sub == 'running' ? 'Service is running.' : 'Service is stopped.');?>"></span>
									</td>
									<td>
										<?php print $service->unit; ?>
										<p><small><?php print $service->description; ?></small></p>
									</td>
									<td class="text-end">
										<div class="btn-group mr-2">
											<?php
												if($service->sub === 'running') {
													?>
														<button type="submit" name="action" value="restart" class="btn btn-sm btn-outline-warning m-0"><?php Icon::show('restart'); ?></button>
														<button type="submit" name="action" value="stop" class="btn btn-sm btn-outline-danger m-0"><?php Icon::show('stop'); ?></button>
													<?php
												} else {
													?>
														<button type="submit" name="action" value="start" class="btn btn-sm btn-outline-success m-0"><?php Icon::show('start'); ?></button>
													<?php
												}
											?>
										</div>
										<button type="submit" name="action" value="start" class="btn btn-sm btn-outline-info"><?php I18N::__('Info'); ?></button>
									</td>
								</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
	<?php
	$template->footer();
?>