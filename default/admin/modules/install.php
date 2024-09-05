<?php
	
	use fruithost\Installer\Installer;
	use fruithost\Localization\I18N;
	
	$results = [];
	foreach($repositorys as $entry) {
		$repository = Installer::getRepository($entry->id);
		$content    = Installer::getFile($repository, 'modules.list');
		if(!empty($content)) {
			$modules = explode(PHP_EOL, $content.PHP_EOL);
			foreach($modules as $name) {
				if(empty($name)) {
					continue;
				}
				// @ToDo Cache
				$name           = trim($name);
				$info           = Installer::getFile($repository, sprintf('%s/module.package', $name));
				$results[$name] = $info;
			}
		}
	}
	// @ToDo Make Ajax Request
?>
<div class="container mt-5 mb-5">
    <div class="row row-cols-1 row-cols-md-3 g-4">
		<?php
			if(!empty($results)) {
				foreach($results as $name => $result) {
					if(empty($result)) {
						continue;
					}
					$json = json_decode($result, false);
					if(empty($result)) {
						continue;
					}
					$installed = $this->getCore()->getModules()->hasModule($name, true);
					?>
                    <div class="col">
                        <div class="card">
                            <div class="card-body row">
                                <div class="col-2 fs-1">
									<?php
										if(preg_match('/^(http|https|data):/', $json->icon)) {
											print sprintf('<img alt="" class="module-icon" src="%s" />', $json->icon);
										} else if(str_starts_with($json->icon, '/')) {
											print sprintf('<img alt="" class="module-icon" src="/app/%s" />', $json->icon);
										} else {
											printf('<i class="bi bi-%s"></i>', $json->icon);
										}
									?>
                                </div>
                                <div class="col-6">
                                    <h5 class="card-title"><a><?php print $json->name; ?></a></h5>
                                    <p style="height: 120px;" class="card-text"><?php print $json->description; ?></p>
                                </div>
                                <div class="col-4">
                                    <div class="d-grid gap-2 mx-auto">
										<?php
											if($installed) {
												?>
                                                <a href="<?php print $this->url(sprintf('/admin/modules/?deinstall=%s', $name)); ?>"
                                                   class="btn btn-outline-danger btn-sm"
                                                   data-confirm="<?php printf(I18N::get('Do you really wan\'t to delete the Module %s?'), sprintf('<strong>%s</strong>', $json->name)); ?>"><?php I18N::__('Deinstall'); ?></a>
												<?php
											} else {
												?>
                                                <a href="<?php print $this->url(sprintf('/admin/modules/?install=%s', $name)); ?>"
                                                   class="btn btn-success btn-sm"
                                                   data-loading="<?php I18N::__('Installing'); ?>"><?php I18N::__('Install'); ?></a>
												<?php
											}
										?>
                                        <button type="button" name="module_info" data-bs-toggle="modal"
                                                data-bs-target="#module_info" data-module="<?php print $name; ?>"
                                                class="btn btn-outline-light btn-sm"><?php I18N::__('Info'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-body-secondary">
                                <div class="row">
                                    <div class="col small text-nowrap">
										<?php I18N::__('From'); ?> <?php printf('<a href="mailto:%s" class="text-decoration-none" target="_blank">%s</a>', $json->author->email, $json->author->name); ?>
                                        | <?php printf('<a href="%1$s" class="text-decoration-none" target="_blank">%1$s</a>', $json->author->url); ?>
                                    </div>
                                    <div class="col text-end">
                                        <span class="badge text-bg-secondary"><?php print $json->version; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
		?>
    </div>
</div>