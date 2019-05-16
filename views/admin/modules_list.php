<table class="table table-sm table-striped table-hover">
	<tr>
		<th colspan="3">Module</th>
		<th>Description</th>
	</tr>
	<?php
		foreach($modules->getList() AS $module) {
			$info = $module->getInfo();
			?>
			<tr class="<?php print (isset($upgradeable->{$module->getDirectory()}) ? 'table-warning' : ''); ?>">
				<td scope="row" width="1px"><input type="checkbox" name="module[]" value="<?php print $info->getName(); ?>" /></td>
				<td>
					<span class="d-block badge badge-pill module-badge badge-<?php print ($module->isEnabled() ? 'success' : 'danger');?>" data-toggle="tooltip" title="<?php print ($module->isEnabled() ? 'Module is enabled.' : 'Module is disabled.');?>"></span>
				</td>
				<td>
					<strong><?php print $info->getName(); ?></strong>
					<p class="module-actions"><?php
						$links = [];
						
						if($module->isEnabled()) {
							$links['disable'] = '<a href="#" class="text-warning">Disable</a>';
						} else {
							$links['enable'] = '<a href="#" class="text-success">Enable</a>';
						}
						
						if($module->hasSettingsPath()) {
							$links['settings'] = sprintf('<a href="%s" class="text-primary">Settings</a>', $this->url('/admin/modules/?settings=' . $module->getDirectory()));
						}
						
						if(isset($upgradeable->{$module->getDirectory()})) {
							$links['upgrade'] = '<a href="#" class="text-warning font-weight-bold">Upgrade</a>';
						}
						
						$links['deinstall'] = '<a href="#" class="text-danger">Deinstall</a>';
						
						print implode(' | ', $links);
					?></p>
				</td>
				<td>
					<?php
						if(isset($upgradeable->{$module->getDirectory()})) {
							$upgrade = $upgradeable->{$module->getDirectory()};
							?>
								<div class="alert alert-warning d-flex p-2" role="alert">
									<i class="material-icons text-danger p-0">autorenew</i>
									<p class="p-0 m-0">The module <strong><?php print $info->getName(); ?></strong> has an new upgrade to <strong>Version <?php print $upgrade->version; ?></strong>!</p>
								</div>
							<?php
						}
					?>
					<p><?php print $info->getDescription(); ?></p>
					<small>Version <?php print $info->getVersion(); ?> | by <a href="<?php print $info->getAuthor()->getWebsite(); ?>" target="_blank"><?php print $info->getAuthor()->getName(); ?></a></small>
				</td>
			</tr>
			<?php
		}
	?>
</table>