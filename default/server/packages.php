<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;

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
    $lines = (empty($result) ? [] : explode(PHP_EOL, $result));
    // @ToDo group by names (for sample php-*)
	?>
	<form method="post" action="<?php print $this->url('/server/packages' . (!empty($tab) ? '/' . $tab : '')); ?>">
		<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
					<li class="breadcrumb-item active" aria-current="page">
						<a href="<?php print $this->url('/server/packages'); ?>"><?php I18N::__('Services'); ?></a>
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
						<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Package'); ?></th>
						<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Version'); ?></th>
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
							
							if(empty(trim($info[0]))) {
								continue;
							}
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
		</div>
	</form>
	<?php
	$template->footer();
?>