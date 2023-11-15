<?php
    use fruithost\Accounting\Auth;
	use fruithost\OS\OperatingSystem;

	/* Memory */
	$memory		= [];
	$meminfo	= file('/proc/meminfo', \FILE_SKIP_EMPTY_LINES);
	
	foreach($meminfo AS $line) {
		preg_match('/(?<name>[a-zA-Z]+):([\s]+)(?<value>[0-9]+)\s(?<size>[a-zA-Z]+)$/Uis', $line, $matches);
		
		if(!empty($matches['name'])) {
			switch(trim($matches['name'])) {
				case 'MemTotal':	$memory['total'] = $matches['value'];		break;
				case 'MemFree':		$memory['free'] = $matches['value'];		break;
				case 'SwapTotal':	$memory['total_swap'] = $matches['value'];	break;
				case 'SwapFree':	$memory['free_swap'] = $matches['value'];	break;
				case 'Buffers':		$memory['buffer'] = $matches['value'];		break;
				case 'Cached':		$memory['cache'] = $matches['value'];		break;
				default: break;
			}
		}
	}

	/* Discs */
	$disks		= [];
	$output		= shell_exec('df -T -h');
	$search		= [ 'G', 'M' ];
	$replace	= [ ' GB', ' MB' ];
	
	foreach(explode(PHP_EOL, $output) AS $index => $line) {
		if($index === 0 || empty(trim($line))) {
			continue;
		}
		
		preg_match('/^(?P<filesystem>[\da-zA-Z\/]+)\s+(?P<type>[\da-zA-Z\/]+)\s+(?P<size>[0-9A-Z\.]+)\s+(?P<used>[0-9A-Z\.]+)\s+(?P<avail>[0-9A-Z\.]+)\s+(?P<percent>\d+%)\s+(?P<mount>[\da-zA-Z\/]+)$/', $line, $matches);
		
		$filesystem =  null;
	
		switch((isset($matches['type']) ? $matches['type'] : null)) {
			case 'rootfs':
			case 'ext2':
			case 'ext3':
			case 'ext4':
			case 'fat12':
			case 'fat16':
			case 'fat32':
				$filesystem = 'storage';
			break;
			case 'tmpfs':
			case 'devtmpfs':
				$filesystem = 'memory';
			break;
		}
		
		if(empty($filesystem)) {
			continue;
		}
		
		if(!isset($disks[$filesystem])) {
			$disks[$filesystem] = [];
		}
		
		$disks[$filesystem][] = [
			'filesystem'	=> (isset($matches['filesystem']) ? $matches['filesystem'] : null),
			'type'			=> (isset($matches['type']) ? $matches['type'] : null),
			'size'			=> (isset($matches['size']) ? str_replace($search, $replace, $matches['size']) : null),
			'used'			=> (isset($matches['used']) ? str_replace($search, $replace, $matches['used']) : null),
			'avail'			=> (isset($matches['avail']) ? str_replace($search, $replace, $matches['avail']) : null),
			'percent'		=> (isset($matches['percent']) ? $matches['percent'] : null),
			'mount'			=> (isset($matches['mount']) ? $matches['mount'] : null)
		];
	}

	$template->assign('hostname',			shell_exec('hostname'));
	$template->assign('hostname_panel',		$_SERVER['HTTP_HOST']);
	$template->assign('ip_address',			$_SERVER['SERVER_ADDR']);
	$template->assign('time_php',			date('d M Y H:i:s T'));
	$template->assign('time_system',		OperatingSystem::getTime());
	$template->assign('os',					OperatingSystem::getPrettyName());
	$template->assign('kernel',				OperatingSystem::getKernel());
	$template->assign('machine_type',		OperatingSystem::getMachineType());
	$template->assign('uptime',				OperatingSystem::getUptime(true));
	$template->assign('memory',				$memory);
	$template->assign('disks',				$disks);
	$template->assign('daemon',			[
		'started'	=> strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0)),
		'start'		=> date(Auth::getSettings('TIME_FORMAT', null, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0))),
		'end'		=> date(Auth::getSettings('TIME_FORMAT', null, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0))),
		'ended'		=> strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0)),
		'time'		=> number_format($this->getCore()->getSettings('DAEMON_RUNNING_END', 0) - $this->getCore()->getSettings('DAEMON_RUNNING_START', 0), 4, ',', '.')
	]);
?>