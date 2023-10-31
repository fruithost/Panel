<?php
	use fruithost\Database;
	use fruithost\Response;
	use fruithost\Auth;
	
	function format($command, $result) {
		if($result === null) {
			return '-bash: ' . $command . ': command not found';
		} else if($result === false) {
			return '-bash: piped error';
		} else {
			return $result;
			/*
			$result = htmlentities($result);
			$result = preg_replace_callback('/\\033\[(:?[^m]+)?m([^\\033\[]+)\\033\[39m/', function($matches) {
				return '<span data-color="' . $matches[1] .  '">' . $matches[2] . '</span>';
			}, $result, -1, $count);
			
			$result = preg_replace('/\\033\[([^m]+)m/Uis', '', $result);
			return nl2br($result);*/
		}
	}
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'command':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					exit();
				}
				
				Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
				
				$prefix		= sprintf("\033[0;32m%s\033[34m@\033[38;2;255;100;100m%s\033[90m:\033[39m~\033[90m#", get_current_user(), $_SERVER['SERVER_NAME']);
				$command	= escapeshellcmd($_POST['command']);
				
				if(defined('DEMO') && DEMO && $command == 'motd') {					
					$output = '<span data-color="1;34">Welcome to the Demoversion of fruithost!</span>';
					printf("%s\n\033[39m%s", $prefix, $output);
					exit();
				} else if($command == 'ColorTest') {
					
					$tests = [
						'styles' => [
							0 => 'reset',
							1 => 'bold',
							2 => 'faint',
							3 => 'italic',
							4 => 'underline',
							5 => 'blink'
						],
						'foreground' => [
							30 => 'black',
							31 => 'red',
							32 => 'green',
							33 => 'yellow',
							34 => 'blue',
							35 => 'magenta',
							36 => 'cyan',
							37 => 'white',
							38 => 'multicolor',
							90 => 'bright_black',
							91 => 'bright_red',
							92 => 'bright_green',
							93 => 'bright_yellow',
							94 => 'bright_blue',
							95 => 'bright_magenta',
							96 => 'bright_cyan',
							97 => 'bright_white'
						],
						'background' => [
							40 => 'black',
							41 => 'red',
							42 => 'green',
							43 => 'yellow',
							44 => 'blue',
							45 => 'magenta',
							46 => 'cyan',
							47 => 'white',
							48 => 'multicolor',
							100 => 'bright_black',
							101 => 'bright_red',
							102 => 'bright_green',
							103 => 'bright_yellow',
							104 => 'bright_blue',
							105 => 'bright_magenta',
							106 => 'bright_cyan',
							107 => 'bright_white',
						]
					];
					
					foreach($tests AS $name => $values) {
						printf("--- %s\n", $name);
						
						foreach($values AS $code => $color) {
							printf("\033[%sm%s\n", $code, $color);				
						}
						
						print "\n";
					}
					
					exit();
				}
				
				$build		= [
					'export MAN_KEEP_FORMATTING=1;',
					'export SHELL=/bin/bash;',
					'export TERM=xterm-256color;',
					'export _=/usr/bin/env;',
					'export USER=fruithost;',
					'export HOME="' . PATH . '";',
					#'/bin/bash -c "',
					($command == 'motd' ? 'cat /etc/motd' : $command),
					#'"',
					' 2>&1'
				];
				
				$process	= proc_open(implode('', $build), [
					[ 'pipe', 'r' ],  // stdin
					[ 'pipe', 'w' ],  // stdout
					[ 'pipe', 'w' ]	  // stderr
				], $pipes);
				
				if($command == 'motd') {
					$command = '';
				}
				
				if(is_resource($process)) {
					$stdin	= $pipes[0];
					$stdout = $pipes[1];
					$stderr = $pipes[2];

					fclose($stdin);

					stream_set_blocking($stdout, false);
					stream_set_blocking($stderr, false);
					
					$outEof = false;
					$errEof = false;
					$result = '';
					$error 	= '';
					
					do {
						$read 	= [ $stdout, $stderr ];
						$write 	= null;
						$except = null;
						
						stream_select($read, $write, $except, 1, 0);

						$outEof = $outEof || feof($stdout);
						$errEof = $errEof || feof($stderr);

						if(!$outEof) {
							$result .= fgets($stdout);
						}

						if(!$errEof) {
							$error .= fgets($stderr);
						}
					} while(!$outEof || !$errEof);
					
					fclose($stdout);
					fclose($stderr);
					proc_close($process);
					
					$output = format($command, $result);
					if($output == "\x1B[H\x1B[2J\x1B[3J") {
						print $output;
					} else {
						printf("%s\n\033[39m %s\n%s", $prefix, $command, $output);
						print format($command, $error);
					}
				}
				exit();
			break;
		}
	}
?>