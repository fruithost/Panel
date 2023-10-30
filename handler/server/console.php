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
			$result = htmlentities($result);
			$result = preg_replace_callback('/\\033\[(:?[^m]+)?m([^\\033\[]+)\\033\[39m/', function($matches) {
				return '<span data-color="' . $matches[1] .  '">' . $matches[2] . '</span>';
			}, $result, -1, $count);
			
			$result = preg_replace('/\\033\[([^m]+)m/Uis', '', $result);
			return nl2br($result);
		}
	}
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'command':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', I18N::get('You have no permissions for this action!'));
					return;
				}
				
				Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
				
				$command	= escapeshellcmd($_POST['command']);
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
						printf('<span data-color="0;32">%s</span><span data-color="1;34">@</span><span data-color="38;5;202">%s</span><span data-color="90">:</span><span data-color="39">~</span><span data-color="90">#</span> %s<br />%s', $_SERVER['USER'], $_SERVER['SERVER_NAME'], $command, $output);
						print format($command, $error);
					}
				}
				exit();
			break;
		}
	}
?>