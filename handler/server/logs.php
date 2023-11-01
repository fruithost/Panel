<?php
	use fruithost\Response;
	use fruithost\Auth;
	use fruithost\Encryption;
	
	function directory_map(string $sourceDir): array {
		$list		= [];
		$fp 		= opendir($sourceDir);
		$sourceDir 	= rtrim($sourceDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		while(false !== ($file = readdir($fp))) {
			if($file === '.' || $file === '..') {
				continue;
			}

			if(is_dir($sourceDir . $file)) {
				if(!is_readable($sourceDir . $file)) {
					$list[]	= $sourceDir . $file . DIRECTORY_SEPARATOR;
					continue;
				}
				
				$list	= array_merge($list, directory_map($sourceDir . $file));
			} else {
				$list[]	= $sourceDir . $file;
			}
		}

		closedir($fp);
		return $list;
    }
	
	$list = array_merge(directory_map(LOG_PATH), directory_map('/var/log/'));
	
	if(isset($_POST['action'])) {
		if(!Auth::hasPermission('SERVER::MANAGE')) {
			$this->assign('error', I18N::get('You have no permissions for this action!'));
			exit();
		}
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
			
			switch($_POST['action']) {
				case 'refresh':
					print json_encode($list);
				break;
				case 'file':
					$file		= $_POST['file'];
					$content	= null;
					
					if(!file_exists($file)) {
						$content = null;
					}
					
					if(!is_readable($file)) {
						$content = false;
					}
					
					if(!empty($file)) {
						try {
							$content = @file_get_contents($file);
						} catch(\Exception $e) {
							$content = false;
						}
					}
					
					print json_encode([
						'file'		=> $file,
						'content'	=> $content
					]);
				break;
			}
			
			exit();
		}
	}
	
	$template->assign('list', $list); 
?>