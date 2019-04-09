<?php
	namespace fruithost;
	
	class TemplateDefaults {
		public function head_robots() {
			printf('<meta name="robots" content="%s" />', $this->getCore()->getHooks()->applyFilter('meta_robots', 'noindex,follow'));
		}
		
		public function head_scripts() {
			$loaded = [];
			
			foreach($this->getFiles()->getHeaderStylesheets() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (strpos($entry->file, '?') === false ? '?t=' . time() . '&' : '&t=' . time() . '&'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getHeaderJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
		
		public function foot_modals() {
			$modals = $this->getCore()->getHooks()->applyFilter('modals', []);
			$template = $this;
			
			foreach($modals AS $modal) {
				?>
					<div class="modal fade" id="<?php print $modal->getName(); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php print $modal->getName(); ?>" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"><?php print $modal->getTitle(); ?></h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<?php print $modal->getContent($template); ?>
								</div>
								<div class="modal-footer text-center">
									<?php
										$buttons = $modal->getButtons();
										
										foreach($buttons AS $button) {
											if(is_array($button)) {
												foreach($button AS $entry) {
													if($entry->isDismissable()) {
														printf('<button type="button" name="%1$s" data-dismiss="modal" class="btn %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
													} else {
														printf('<button type="button" name="%1$s" class="btn %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
													}
												}
											} else {
												if($entry->isDismissable()) {
													printf('<button type="button" name="%1$s" data-dismiss="modal" class="btn %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false));
												} else {
													printf('<button type="button" name="%1$s" class="btn %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false));
												}
											}
										}
									?>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		}
		
		public function foot_scripts() {
			$loaded = [];
			
			foreach($this->getFiles()->getFooterStylesheets() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
							
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<link rel="stylesheet" type="text/css" href="%s%sv=%s" />', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
			
			$loaded = [];
			
			foreach($this->getFiles()->getFooterJavascripts() AS $name => $entry) {
				if(!empty($entry->depencies)) {
					$continue = true;
					
					foreach($entry->depencies AS $needed) {
						if(!array_search($needed, $loaded)) {
							$continue = false;
						}
					}
					
					if($continue) {
						continue;
					}
				}
				
				if(!in_array($name, $loaded)) {
					printf('<script type="text/javascript" src="%s%sv=%s"></script>', $entry->file, (strpos($entry->file, '?') === false ? '?' : '&'), $entry->version);
					$loaded[] = $name;
				}
			}
		}
	}
?>