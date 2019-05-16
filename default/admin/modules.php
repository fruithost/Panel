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
										<button type="button" name="add_repository" data-toggle="modal" data-target="#add_repository" class="btn btn-sm btn-outline-primary">Add new</button>
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
				<li class="nav-item"><a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="globals-tab" href="<?php print $this->url('/admin/modules'); ?>" role="tab">Installed Modules<?php
					$updates = count((array) $upgradeable);
					
					if($updates > 0) {
						printf('<span class="badge badge-pill badge-danger ml-1">%d</span>', $updates);
					}
				?></a></li>
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
							
								if(count($repositorys) === 0) {
									?>
										<div class="jumbotron text-center bg-transparent text-muted">
											<i class="material-icons">sentiment_very_dissatisfied</i>
											<h2>No Repositorys available!</h2>
											<p class="lead">Please adding some Repositorys to keep updates.</p>
											<button type="button" name="add_repository" data-toggle="modal" data-target="#add_repository" class="btn btn-lg btn-primary mt-4">Add Repository</button>
										</div>
									<?php
								} else {
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
															<?php
																if(empty($repository->time_updated)) {
																	print '<span class="text-warning">Never updated</span>';
																} else {
																	print date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($repository->time_updated));
																}
															?>
														</td>
														<td>
															<button class="update btn btn-sm btn-info" type="submit" name="action" value="update" id="update_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>">Update</button>
															<button class="delete btn btn-sm btn-danger" type="submit" name="action" value="delete" id="delete_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>">Delete</button>
														</td>
													</tr>
													<?php
												}
											?>
										</table>
									<?php
								}
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
		<script type="text/javascript">
			_watcher_modules = setInterval(function() {
				if(typeof(jQuery) !== 'undefined') {
					clearInterval(_watcher_modules);
					
					(function($) {						
						$('button[name="action"].delete, button[name="action"].update').on('click', function(event) {
							$(event.target).parent().parent().find('input[type="checkbox"]').prop('checked', true);
						});
					}(jQuery));
				}
			}, 500);
		</script>
	<?php
	$template->footer();
?>