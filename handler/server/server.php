<?php
	
	use fruithost\Accounting\Auth;
	use fruithost\Hardware\Memory;
	use fruithost\Hardware\NetworkInterfaces;
	use fruithost\Hardware\OperatingSystem;
	use fruithost\Hardware\PhysicalDrives;
	use fruithost\Network\Response;
	use fruithost\System\Update;
	use fruithost\System\Utils;
	use fruithost\Templating\TemplateFiles;
	
	if(isset($_POST['action']) && $_POST['action'] === 'command') {
		if(!Auth::hasPermission('SERVER::SERVER')) {
			$this->assign('error', I18N::get('You have no permissions for this action!'));
			exit();
		}
		Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
		switch($_POST['command']) {
			case 'get_live_usage':
				print json_encode([
					'total'      => Utils::getFileSize(Memory::getTotal()),
					'used'       => Utils::getFileSize(Memory::getUsed()),
					'cache'      => Utils::getFileSize(Memory::getInCache()),
					'assured'    => Utils::getFileSize(Memory::getAssured()),
					'swap'       => Utils::getFileSize(Memory::getSwap()),
					'percentage' => Memory::getPercentage()
				]);
				break;
		}
		exit();
	}
	$time_format = Auth::getSettings('TIME_FORMAT', null, 'd.m.Y - H:i');
	$time_update = new \DateTime($this->getCore()->getSettings('UPDATE_TIME', null));
	$time_status = new \DateTime($this->getCore()->getSettings('DAEMON_TIME_END', null));
	$time_status->add(\DateInterval::createFromDateString('15 minutes'));
	$template->assign('update_version', $this->getCore()->getSettings('UPDATE_VERSION'));
	$template->assign('update_license', Update::getLicense());
	$template->assign('time_php', date($time_format));
	$template->assign('time_system', OperatingSystem::getTime($time_format));
	$template->assign('time_update', ($time_update->format($time_format)));
	$template->assign('os', OperatingSystem::getPrettyName());
	$template->assign('id', OperatingSystem::getID());
	$template->assign('kernel', OperatingSystem::getKernel());
	$template->assign('machine_type', OperatingSystem::getMachineType());
	$template->assign('uptime', OperatingSystem::getUptime(true));
	$template->assign('memory', Memory::get());
	$template->assign('network', NetworkInterfaces::get());
	$template->assign('disks', PhysicalDrives::getDevices());
	$template->assign('version', file_get_contents('.version'));
	$template->assign('daemon', [
		'status'  => $time_update < new \DateTime(),
		'started' => strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0)),
		'start'   => date($time_format, strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0))),
		'end'     => date($time_format, strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0))),
		'ended'   => strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0)),
		'time'    => number_format($this->getCore()->getSettings('DAEMON_RUNNING_END', 0) - $this->getCore()->getSettings('DAEMON_RUNNING_START', 0), 4, ',', '.')
	]);
	$template->getFiles()->addJavascript('statistics', $this->url('js/statistics.js'), '1.0.0', [ 'ajax' ], TemplateFiles::FOOTER);
?>
