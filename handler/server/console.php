<?php
	use fruithost\Database;
	use fruithost\Response;
	use fruithost\Auth;
	
	function format($command, $result) {
		if($result === null) {
			print '-bash: ' . $command . ': command not found';
		} else if($result === false) {
			print '-bash: piped error';
		} else {
			$result = htmlentities($result);
			$result = preg_replace_callback('/\\033\[(:?[^m]+)?m([^\\033\[]+)\\033\[39m/', function($matches) {
				return '<span data-color="' . $matches[1] .  '">' . $matches[2] . '</span>';
			}, $result, -1, $count);
			
			$result = preg_replace('/\\033\[([^m]+)m/Uis', '', $result);
			print nl2br($result);
		}
	}
	
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'command':
				if(!Auth::hasPermission('SERVER::MANAGE')) {
					$this->assign('error', 'You have no permissions for this action!');
					return;
				}
				
				Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');
				
				$command	= escapeshellcmd($_POST['command']);
				#$result	= shell_exec('export TERM=xterm-256color;' . $command . ' 2>&1');
				$process = proc_open('export TERM=xterm-256color;' . $command . ' 2>&1', [
					[ 'pipe', 'r' ],  // stdin
					[ 'pipe', 'w' ],  // stdout
					[ 'pipe', 'w' ]	  // stderr
				], $pipes);
				
				print 'fruithost@localhost:~# ';
				
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

						if (!$outEof) {
							$result .= fgets($stdout);
						}

						if (!$errEof) {
							$error .= fgets($stderr);
						}
					} while(!$outEof || !$errEof);
					
					fclose($stdout);
					fclose($stderr);
					proc_close($process);
					
					print format($command, $result);
					print format($command, $error);
				}
				
				exit();
			break;
		}
	}
?>