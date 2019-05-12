<?php
	use fruithost\Auth;
	
	$template->header();
	
	if(!Auth::hasPermission('MODULES::VIEW')) {
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
		<form method="post" action="<?php print $this->url('/admin/modules' . (!empty($tab) ? '/' . $tab : '')); ?>">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a href="<?php print $this->url('/admin/modules'); ?>">Modules</a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">				
					<?php
						switch($tab) {
							case 'repositorys':
								?>
									<div class="btn-group mr-2">
										<button type="submit" name="action" value="add" class="btn btn-sm btn-outline-primary">Add new</button>
										<button type="submit" name="action" value="update" class="btn btn-sm btn-outline-success">Update</button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Delete</button>
									</div>
								<?php
							break;
							default:
								?>
									<div class="btn-group mr-2">
										<button type="submit" name="action" value="upgrade" class="btn btn-sm btn-outline-success">Upgrade</button>
										<button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Delete</button>
									</div>
								<?php
							break;
						}
					?>
				</div>
			</div>
			
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item"><a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="globals-tab" href="<?php print $this->url('/admin/modules'); ?>" role="tab">Installed Modules</a></li>
				<li class="nav-item"><a class="nav-link<?php print (!empty($tab) && $tab === 'repositorys' ? ' active' : ''); ?>" id="security-tab" href="<?php print $this->url('/admin/modules/repositorys'); ?>" role="tab">Repositorys</a></li>
			</ul>
			<?php
				if(isset($error)) {
					?>
						<div class="alert alert-danger mt-4" role="alert"><?php print $error; ?></div>
					<?php
				} else if(isset($success)) {
					?>
						<div class="alert alert-success mt-4" role="alert"><?php print $success; ?></div>
					<?php
				}
			?>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane show active" id="globals" role="tabpanel" aria-labelledby="globals-tab">
					<?php
						switch($tab) {
							case 'repositorys':
								?>
								<table class="table table-sm table-striped table-hover">
									<tr>
										<th colspan="2">Repository</th>
										<th>Status</th>
										<th>Actions</th>
									</tr>
									<?php
										foreach($repositorys AS $repository) {
											?>
											<tr>
												<td scope="row" width="1px"><input type="checkbox" name="repository[]" value="<?php print $repository->id; ?>" /></td>
												<td>
													<a href="<?php print $repository->url; ?>" target="_blank"><?php print $repository->url; ?></a>
												</td>
												<td>
													<?php print date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($repository->time_updated)); ?>
												</td>
												<td></td>
											</tr>
											<?php
										}
									?>
								</table>
								<?php
							break;
							default:
								?>
								<table class="table table-sm table-striped table-hover">
									<tr>
										<th colspan="2">Module</th>
										<th>Description</th>
										<th>Status</th>
									</tr>
									<?php
										foreach($modules->getList() AS $module) {
											$info = $module->getInfo();
											?>
											<tr>
												<td scope="row" width="1px"><input type="checkbox" name="module[]" value="<?php print $info->getName(); ?>" /></td>
												<td>
													<strong><?php print $info->getName(); ?></strong>
													<p><?php
														$links = [];
														
														if($module->isEnabled()) {
															$links['disable'] = '<a href="#" class="text-warning">Disable</a>';
															$links['settings'] = '<a href="#">Settings</a>';
														} else {
															$links['enable'] = '<a href="#" class="text-success">Enable</a>';
														}
														
														$links['deinstall'] = '<a href="#" class="text-danger">Deinstall</a>';
														
														print implode(' | ', $links);
													?></p>
												</td>
												<td>
													<p><?php print $info->getDescription(); ?></p>
													<small>Version <?php print $info->getVersion(); ?> | by <a href="<?php print $info->getAuthor()->getWebsite(); ?>" target="_blank"><?php print $info->getAuthor()->getName(); ?></a></small>
												</td>
												<td><?php print ($module->isEnabled() ? '<span class="text-success">Enabled</span>' : '<span class="text-danger">Disabled</span>');?></td>
											</tr>
											<?php
										}
									?>
								</table>
								<?php
							break;
						}
					?>
				</div>
			</div>
		</form>
	<?php
	$template->footer();
?>