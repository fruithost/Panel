<?php
	use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
	use fruithost\UI\Modal;
	use fruithost\UI\Button;
	
	$command	= 'systemctl list-units --type service --full --all --no-pager';
	$json		= shell_exec($command . ' --output json');
	
	if(empty($json)) {
		$services = [];
	} else {
		$services = json_decode($json, false);
		
		/* @ToDo Better check, if the used systemctl version supports json output format! */
		if($services == null) {
			$json = shell_exec($command . ' --plain --no-legend | sed \'s/ \{1,\}/,/g\' | jq --raw-input --slurp \'split("\n") | map(split(",")) | .[0:-1] | map( { "unit": .[0], "load": .[1], "active": .[2], "sub": .[3] } )\'');
			$services = json_decode($json, false);
		}
	}
	
	if(isset($_POST['action'])) {
		if(!Auth::hasPermission('SERVER::MANAGE')) {
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
				shell_exec(sprintf('systemctl start %s', $service));
				
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be started!'), $service));
			break;
			case 'stop':
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					return;
				}
				
				$service = $_POST['service'];
				shell_exec(sprintf('systemctl stop %s', $service));
				
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be stopped!'), $service));
			break;
			case 'restart':
				if(defined('DEMO') && DEMO) {
					$this->assign('error', I18N::get('DEMO-VERSION: This action can\'t be used!'));
					return;
				}
				
				$service = $_POST['service'];
				shell_exec(sprintf('systemctl restart %s', $service));
				
				$this->assign('success', sprintf(I18N::get('The Service "%s" will be restarted!'), $service));
			break;
			case 'status':
				$service	= $_POST['service'];
				$result		= shell_exec(sprintf('systemctl status %s', $service));
				$modal		= new Modal('status', I18N::get('Status'),  __DIR__ . '/services.modal.php');
				
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