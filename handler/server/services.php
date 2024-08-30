<?php
	
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	use fruithost\UI\Button;
	use fruithost\UI\Modal;
	
	$type = 'systemd';
	
	if(!Auth::hasPermission('SERVER::SERVICES')) {
		$services = [];
	} else {
		$command = 'systemctl list-units --type service --full --all --no-pager';
		$json    = shell_exec($command.' --output json');
		if(empty($json)) {
			$type		= 'init.d';
			$services	= [];
			$data		= shell_exec('service --status-all | sed \'s/ \[ \(\-\) \]  /-/\' | sed \'s/ \[ \(\+\) \]  /+/\' | sed \'s/ \[ \(\?\) \]  /?/\'');
			$tiles		= explode(PHP_EOL, $data);
			foreach($tiles AS $tile) {
				$tile = trim($tile);
				if(empty($tile)) {
					continue;
				}
				$services[] = (object) [
					'unit'	=> ltrim($tile, '+,-,?'),
					'sub'	=> $tile[0] == '+' ? 'running' : '?'
				];
			}
		} else {
			$services = json_decode($json, false);
			/* @ToDo Better check, if the used systemctl version supports json output format! */
			if($services == null) {
				$json     = shell_exec($command.' --plain --no-legend | sed \'s/ \{1,\}/,/g\' | jq --raw-input --slurp \'split("\n") | map(split(",")) | .[0:-1] | map( { "unit": .[0], "load": .[1], "active": .[2], "sub": .[3] } )\'');
				$services = json_decode($json, false);
			}
		}
	}
	
	if(isset($_POST['action'])) {
		if(!Auth::hasPermission('SERVER::SERVICES')) {
			$this->assign('error', I18N::get('You have no permissions for this action!'));
			
			return;
		}
		switch($_POST['action']) {
			case 'start':
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					
					return;
				}
				$service = $_POST['service'];
				$command = '';
				switch($type) {
					case 'init.d':
						$command = sprintf('service %s start', $service);
					break;
					case 'systemd':
						$command = sprintf('systemctl start %s', $service);
					break;
				}
				
				if(empty($command)) {
					$this->assign('success', I18N::get('Unsupported operating system?'));
					return;
				}
				shell_exec($command);
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be started!'), $service));
				break;
			case 'stop':
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					
					return;
				}
				$service = $_POST['service'];
				$command = '';
				switch($type) {
					case 'init.d':
						$command = sprintf('service %s stop', $service);
					break;
					case 'systemd':
						$command = sprintf('systemctl stop %s', $service);
					break;
				}
				
				if(empty($command)) {
					$this->assign('success', I18N::get('Unsupported operating system?'));
					return;
				}
				shell_exec($command);
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be stopped!'), $service));
				break;
			case 'restart':
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					
					return;
				}
				$service = $_POST['service'];
				$command = '';
				switch($type) {
					case 'init.d':
						$command = sprintf('service %s restart', $service);
					break;
					case 'systemd':
						$command = sprintf('systemctl restart %s', $service);
					break;
				}
				
				if(empty($command)) {
					$this->assign('success', I18N::get('Unsupported operating system?'));
					return;
				}
				shell_exec($command);
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be restarted!'), $service));
				break;
			case 'status':
				$service = $_POST['service'];
				$command = '';
				switch($type) {
					case 'init.d':
						$command = sprintf('service %s status', $service);
					break;
					case 'systemd':
						$command = sprintf('systemctl status %s', $service);
					break;
				}
				
				if(empty($command)) {
					$this->assign('success', I18N::get('Unsupported operating system?'));
					return;
				}
				
				$result  = shell_exec($command);
				$modal   = new Modal('status', I18N::get('Status'), __DIR__.'/services.modal.php');
				$modal->addButton([
					(new Button())->setName('ok')->setLabel(I18N::get('OK'))->addClass('btn-outline-success')->setDismissable()
				]);
				$this->addModal($modal);
				$modal->show([
					'result' => $result
				]);
				break;
		}
	}
	$template->assign('services', $services);
?>