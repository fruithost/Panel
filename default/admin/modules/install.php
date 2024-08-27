<?php
	
	use fruithost\Localization\I18N;
	
	$results = [];
	
	foreach($repositorys as $entry) {
		$headers = [];
		$options = [];
		$branch = 'master';
		
		// Load GitHub by RAW
		if(preg_match('/github\.com\/([^\/]+)\/([^\/]+)$/Uis', rtrim($entry->url, '/'), $matches)) {
			$user = rtrim($matches[1], '/');
			$repo = rtrim($matches[2], '/');
			$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/%s', $user, $repo, $branch);
			
			// Load by Git-Variables
		} else if(str_starts_with($entry->url, 'git:')) {
			$parts = explode(' ', $entry->url);
			$user = null;
			$repo = null;
			$token = null;
			
			foreach($parts as $part) {
				if(str_starts_with($part, 'user:')) {
					$user = str_replace('user:', '', $part);
				} else if(str_starts_with($part, 'repo:')) {
					$repo = str_replace('repo:', '', $part);
				} else if(str_starts_with($part, 'branch:')) {
					$branch = str_replace('branch:', '', $part);
				} else if(str_starts_with($part, 'token:')) {
					$token = str_replace('token:', '', $part);
				}
			}
			
			$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/%s', $user, $repo, $branch);
			
			if(!empty($token)) {
				$headers['Authorization'] = sprintf('token %s', $entry->token);
				$headers['Accept'] = 'application/vnd.github.raw+json';
				$headers['User-Agent'] = sprintf('%s@%s (PHP v1.0.0)', $user, $repo);
				$headers['X-GitHub-Api-Version'] = '2022-11-28';
				$entry->url = sprintf('https://api.github.com/repos/%s/%s/contents', $user, $repo);
			}
		}
		
		if(!empty($headers)) {
			$h = '';
			
			foreach($headers as $name => $value) {
				$h .= sprintf('%s: %s%s', $name, $value, "\r\n");
			}
			
			$options = [
				"http" => [
					"header" => $h
				]
			];
		}
		
		// @ToDo Cache
		$request = sprintf('%s/modules.list', $entry->url);
		$context = stream_context_create($options);
		$list = @file_get_contents($request, false, $context);
		
		if(!empty($list)) {
			$modules = explode(PHP_EOL, $list.PHP_EOL);
			
			foreach($modules as $name) {
				if(empty($name)) {
					continue;
				}
				
				// @ToDo Cache
				$name = trim($name);
				$info = file_get_contents(sprintf('%s/%s/module.package', $entry->url, $name), false, $context);
				$results[$name] = $info;
			}
		}
	}
?>
<div class="container mt-5">
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
                                                <button class="btn btn-success btn-sm"><?php I18N::__('Install'); ?></button>
												<?php
											}
										?>
                                        <button class="btn btn-outline-light btn-sm"><?php I18N::__('Info'); ?></button>
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