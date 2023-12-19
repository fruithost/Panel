<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;
?>
<!DOCTYPE html>
<html lang="<?php print $template->getLanguage(true); ?>" data-bs-theme="auto">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title><?php print $project_name; ?></title>
		<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">-->
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
				<body class="d-flex overflow-hidden flex-column p-0 m-0 align-items-stretch">
					<nav class="navbar sticky-top flex-nowrap p-0 border-bottom user-select-none">
						<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-light fs-6" href="<?php print $template->url('/'); ?>"><?php print $project_name; ?></a>
						
						<div class="d-flex w-100 justify-content-between">
							<div class="navbar-nav justify-content-start flex-row d-md-block d-sm-none"></div>
							<ul class="navbar-nav justify-content-start flex-row d-md-none">
								<li class="nav-item text-nowrap">
									<button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
										<?php Icon::show('list'); ?>
									</button>
								</li>
							</ul>
							<ul class="navbar-nav justify-content-end flex-row">
								<li class="nav-item nav-account drodown-center">
									<a class="nav-link dropdown-toggle text-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
										<img class="object-fit-cover bg-dark border rounded picture" alt="Profile Picture" src="<?php print Auth::getGravatar(); ?>" /> <?php I18N::__('Account'); ?>
									</a>
									<div class="dropdown-menu">
										<?php
											foreach($topbar->getEntries() AS $entry) {
												printf('<a class="dropdown-item" href="%s">%s %s</a>', $entry->url, $entry->icon, $entry->name);
											}
										?>
									</div>
								</li>
							</ul>
						</div>
					</nav>
					
					<div class="d-flex flex-row h-100 align-items-stretch p-0 m-0">
						<!-- Navigation -->
						<nav class="sidebar overflow-auto user-select-none align-items-stretch p-0 m-0 h-100 bg-body-tertiary d-sm-none d-md-block" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
							<div class="h-100 border-end" tabindex="-1">
								<div class="d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
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
													<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-2" data-bs-toggle="collapse" data-bs-target="#collapse_<?php print $category->getID(); ?>" aria-expanded="<?php print ($visible ? 'true' : 'false'); ?>" aria-controls="collapse_<?php print $category->getID(); ?>">
														<span><?php print $category->getLabel(); ?></span>
														<?php
															Icon::show('arrow-down', [
																'classes'	=> [ 'd-flex', 'text-muted' ]
															]);
														?>
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
						
						<!-- Content -->
						<div class="h-100 flex-fill overflow-auto">
							<div class="container-fluid h-100">
								<div class="row h-100">
									<?php
										if(isset($module) && $module->isFrame() && !$template->getCore()->getRouter()->startsWith('/admin')) {
											?>
												<main class="frame col px-md-4">
											<?php
										} else {
											?>
												<main class="col px-md-4 d-block">
											<?php
										}
					}
			?>