<?php
    use fruithost\Accounting\Auth;

	/* Uptime */
    $uname			= explode(' ', shell_exec('uname -a'));
	$uptime_array	= explode(' ', shell_exec('cat /proc/uptime'));
	$seconds		= round($uptime_array[0], 0);
	$minutes		= $seconds / 60;
	$hours			= $minutes / 60;
	$days			= floor($hours / 24);
	$hours			= sprintf('%02d', floor($hours - ($days * 24)));
	$minutes		= sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
	
	if($days == 0) {
		$uptime = sprintf('%s:%s', $hours, $minutes);
	} else {
		$uptime = sprintf('%s:%s:%s', $days, $hours, $minutes);
	}

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
	$template->assign('time_system',		shell_exec('date +\'%d %b %Y %T %Z\''));
	$template->assign('time_php',			date('d M Y H:i:s T'));
	$template->assign('os',					$uname[0]);
	$template->assign('kernel',				$uname[2]);
	$template->assign('uptime',				$uptime);
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