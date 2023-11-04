<?php
	use fruithost\Router;
	use fruithost\Auth;
	use fruithost\I18N;
?>
<!DOCTYPE html>
<html lang="<?php print $template->getLanguage(true); ?>">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title><?php print $project_name; ?></title>
		<?php
			$template->head();
		?>
	</head>
	<body>
		<?php
			if(Auth::isLoggedIn()) {
				?>
					<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0">
						<a class="navbar-brand col-12 col-sm-4 col-md-3 col-lg-2 mr-0" href="<?php print $template->url('/'); ?>"><?php print $project_name; ?></a>
						<button class="d-md-none navbar-toggler p-0 border-0 mr-auto ml-2" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>
						<?php 
							if(defined('DEMO') && DEMO) {
								?>
									<ul class="navbar-nav mr-auto">
										<li class="nav-item p-1">
											<h3 class="p-0 m-0"><span class="badge badge-danger"><?php I18N::__('DEMO VERSION'); ?></span></h3>
										</li>
									</ul>
								<?php
							}
						?>
						<ul class="navbar-nav px-3">
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><img class="picture" src="<?php print Auth::getGravatar(); ?>" /> <?php I18N::__('Account'); ?></a>
								<div class="dropdown-menu bg-dark">
									<?php
										foreach($topbar->getEntries() AS $entry) {
											printf('<a class="dropdown-item" href="%s">%s %s</a>', $entry->url, $entry->icon, $entry->name);
										}
									?>
								</div>
							</li>
						</ul>
					</nav>
					<div class="container-fluid">
						<div class="row">
							<nav class="col-12 col-sm-4 col-md-3 col-lg-2 bg-light sidebar d-md-none d-md-block collapse navbar-collapse" id="sidebar">
								<div class="sidebar-sticky">
									<?php
										if(!$navigation->isEmpty()) {
											foreach($navigation->getEntries() AS $category) {
												if($category->isEmpty()) {
													continue;
												}
												
												$visible = false;
												
												foreach($category->getEntries() AS $entry) {
													if($visible) {
														break;
													}
													
													if($entry->active) {
														$visible = true;
													}
												}
												?>
													<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-2 text-muted" data-toggle="collapse" data-target="#collapse_<?php print $category->getID(); ?>" aria-expanded="<?php print ($visible ? 'true' : 'false'); ?>" aria-controls="collapse_<?php print $category->getID(); ?>">
														<span><?php print $category->getLabel(); ?></span>
														<!--<a class="d-flex align-items-center text-muted" href="#"><span data-feather="plus-circle"></span></a>-->
														<i class="d-flex material-icons text-muted">arrow_drop_down</i>
													</h6>
													<div class="collapse<?php print ($visible ? ' show' : ''); ?>" id="collapse_<?php print $category->getID(); ?>">
														<?php
															foreach($category->getEntries() AS $entry) {
																?>
																	<ul class="nav flex-column mb-2">
																		<li class="nav-item" style="order: <?php print $entry->order; ?>;"><a class="nav-link<?php print ($entry->active ? ' active' : ''); ?>" href="<?php print (preg_match('/^(http|https):\/\//Uis', $entry->url) ? $entry->url : $template->url($entry->url)); ?>"<?php print (isset($entry->target) ? sprintf(' target="%s"', $entry->target) : ''); ?>><?php print $entry->icon; ?> <?php print $entry->name; ?></a></li>
																	</ul>
																<?php
															}
														?>
													</div>
												<?php
											}
										}
									?>
								</div>
							</nav>
							<?php
								if(isset($module) && $module->isFrame() && !$template->getCore()->getRouter()->startsWith('/admin')) {
									?>
										<main role="main" class="frame col-md-9 ml-sm-auto col-lg-10 px-4">
									<?php
								} else {
									?>
										<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
									<?php
								}
			}
		?>