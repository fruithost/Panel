<?php
    use fruithost\Accounting\Auth;
	use fruithost\Templating\TemplateFiles;
	use fruithost\Hardware\OperatingSystem;
	use fruithost\Hardware\PhysicalDrives;
	use fruithost\Hardware\NetworkInterfaces;
	use fruithost\Hardware\Memory;
	use fruithost\Network\Response;
	use fruithost\System\Utils;
	
	if(isset($_POST['action']) && $_POST['action'] === 'command') {
		if(!Auth::hasPermission('SERVER::MANAGE')) {
			$this->assign('error', I18N::get('You have no permissions for this action!'));
			exit();
		}

		Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
	
		switch($_POST['command']) {
			case 'get_live_usage':
				print json_encode([
					'total'			=> Utils::getFileSize(Memory::getTotal()),
					'used'			=> Utils::getFileSize(Memory::getUsed()),
					'cache'			=> Utils::getFileSize(Memory::getInCache()),
					'assured'		=> Utils::getFileSize(Memory::getAssured()),
					'swap'			=> Utils::getFileSize(Memory::getSwap()),
					'percentage'	=> Memory::getPercentage()
				]);
			break;
		}
		exit();
	}

	$template->assign('time_php',			date('d M Y H:i:s T'));
	$template->assign('time_system',		OperatingSystem::getTime());
	$template->assign('os',					OperatingSystem::getPrettyName());
	$template->assign('kernel',				OperatingSystem::getKernel());
	$template->assign('machine_type',		OperatingSystem::getMachineType());
	$template->assign('uptime',				OperatingSystem::getUptime(true));
	$template->assign('memory',				Memory::get());
	$template->assign('network',			NetworkInterfaces::get());
	$template->assign('disks',				PhysicalDrives::getDevices());
	$template->assign('daemon',			[
		'started'	=> strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0)),
		'start'		=> date(Auth::getSettings('TIME_FORMAT', null, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0))),
		'end'		=> date(Auth::getSettings('TIME_FORMAT', null, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0))),
		'ended'		=> strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0)),
		'time'		=> number_format($this->getCore()->getSettings('DAEMON_RUNNING_END', 0) - $this->getCore()->getSettings('DAEMON_RUNNING_START', 0), 4, ',', '.')
	]);
	
	$template->getFiles()->addJavascript('statistics', $this->url('js/statistics.js'), '1.0.0', [ 'ajax' ], TemplateFiles::FOOTER);
?>