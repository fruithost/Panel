<?php
	use fruithost\Database;
	use fruithost\Auth;
	use fruithost\I18N;
	
	$uname			= explode(' ', shell_exec('uname -a'));
	$uptime_array	= explode(" ", exec("cat /proc/uptime"));
	$seconds		= round($uptime_array[0], 0);
	$minutes		= $seconds / 60;
	$hours			= $minutes / 60;
	$days			= floor($hours / 24);
	$hours			= sprintf('%02d', floor($hours - ($days * 24)));
	$minutes		= sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
	
	if($days == 0) {
		$uptime = $hours . ":" .  $minutes . " (hh:mm)";
	} elseif($days == 1) {
		$uptime = $days . " day, " .  $hours . ":" .  $minutes . " (hh:mm)";
	} else {
		$uptime = $days . " days, " .  $hours . ":" .  $minutes . " (hh:mm)";
	}

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

	$disks		= [];
	$output		= shell_exec('df -T -h');
	$search		= [ 'G', 'M' ];
	$replace	= [ ' GB', ' MB' ];
	
	foreach(explode(PHP_EOL, $output) AS $index => $line) {
		if($index === 0 || empty(trim($line))) {
			continue;
		}
		
		preg_match('/^(?P<filesystem>[\da-zA-Z\/]+)\s+(?P<type>[\da-zA-Z\/]+)\s+(?P<size>[0-9A-Z\.]+)\s+(?P<used>[0-9A-Z\.]+)\s+(?P<avail>[0-9A-Z\.]+)\s+(?P<percent>\d+%)\s+(?P<mount>[\da-zA-Z\/]+)$/', $line, $matches);
		
		$disks[] = [
			'filesystem'	=> (isset($matches['filesystem']) ? $matches['filesystem'] : NULL),
			'type'			=> (isset($matches['type']) ? $matches['type'] : NULL),
			'size'			=> (isset($matches['size']) ? str_replace($search, $replace, $matches['size']) : NULL),
			'used'			=> (isset($matches['used']) ? str_replace($search, $replace, $matches['used']) : NULL),
			'avail'			=> (isset($matches['avail']) ? str_replace($search, $replace, $matches['avail']) : NULL),
			'percent'		=> (isset($matches['percent']) ? $matches['percent'] : NULL),
			'mount'			=> (isset($matches['mount']) ? $matches['mount'] : NULL)
		];
	}
	
	
	$template->assign('hostname',		exec('hostname'));
	$template->assign('time_system',	exec('date +\'%d %b %Y %T %Z\''));
	$template->assign('time_php',		date('d M Y H:i:s T'));
	$template->assign('os',				$uname[0]);
	$template->assign('kernel',			$uname[2]);
	$template->assign('uptime',			$uptime);
	$template->assign('memory',			$memory);
	$template->assign('disks',			$disks);
	$template->assign('daemon',			[
		'started'	=> strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0)),
		'start'		=> date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_START', 0))),
		'end'		=> date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0))),
		'ended'		=> strtotime($this->getCore()->getSettings('DAEMON_TIME_END', 0)),
		'time'		=> number_format($this->getCore()->getSettings('DAEMON_RUNNING_END', 0) - $this->getCore()->getSettings('DAEMON_RUNNING_START', 0), 4, ',', '.')
	]);
?>