<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
?>
<!DOCTYPE html>
<html lang="<?php print $template->getLanguage(true); ?>" data-bs-theme="auto">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title><?php print $project_name; ?></title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
		 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
		<?php
			$template->head();
		?>
	</head>
		<?php
			if(!Auth::isLoggedIn()) {
				?>
					<body class="d-flex align-items-center py-4 bg-body-tertiary">
				<?php
			}
			
			if(Auth::isLoggedIn()) {
				?>
				<body>
					<nav class="navbar sticky-top flex-md-nowrap p-0 bg-dark" data-bs-theme="dark">
						<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="<?php print $template->url('/'); ?>"><?php print $project_name; ?></a>
						
						<!-- Small -->
						<ul class="navbar-nav flex-row d-md-none">
							<li class="nav-item text-nowrap">
								<button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
									<i class="bi bi-list"></i>
								</button>
							</li>
						</ul>
					</nav>
					
					<!-- Navigation -->
					<div class="container-fluid">
						<div class="row">
							<nav class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
								<div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
									<div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
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
														<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-2 text-muted" data-bs-toggle="collapse" data-bs-target="#collapse_<?php print $category->getID(); ?>" aria-expanded="<?php print ($visible ? 'true' : 'false'); ?>" aria-controls="collapse_<?php print $category->getID(); ?>">
															<span><?php print $category->getLabel(); ?></span>
															<!--<a class="d-flex align-items-center text-muted" href="#"><span data-feather="plus-circle"></span></a>-->
															<i class="bi bi-caret-down-square-fill d-flex text-muted"></i>
														</h6>
														<div class="collapse<?php print ($visible ? ' show' : ''); ?>" id="collapse_<?php print $category->getID(); ?>">
															<?php
																foreach($category->getEntries() AS $entry) {
																	?>
																		<ul class="nav flex-column mb-2">
																			<li class="nav-item" style="order: <?php print $entry->order; ?>;">
																				<a class="nav-link<?php print ($entry->active ? ' active' : ''); ?>" href="<?php print (preg_match('/^(http|https):\/\//Uis', $entry->url) ? $entry->url : $template->url($entry->url)); ?>"<?php print (isset($entry->target) ? sprintf(' target="%s"', $entry->target) : ''); ?>>
																					<?php print $entry->icon; ?> <?php print $entry->name; ?>
																				</a>
																			</li>
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
								</div>
							</nav>
							<?php
								if(isset($module) && $module->isFrame() && !$template->getCore()->getRouter()->startsWith('/admin')) {
									?>
										<main role="main" class="frame col-md-9 ms-sm-auto col-lg-10 px-md-4">
									<?php
								} else {
									?>
										<main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
									<?php
								}
			}
		?>