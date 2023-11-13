<?php
    use fruithost\Accounting\Auth;
    use fruithost\Network\Response;
    use fruithost\Localization\I18N;
    use fruithost\Templating\TemplateFiles;

    if(isset($_POST['action']) && $_POST['action'] === 'command') {
        if (!Auth::hasPermission('SERVER::MANAGE')) {
            $this->assign('error', I18N::get('You have no permissions for this action!'));
            exit();
        }

        Response::addHeader('Content-Type', 'text/plain; charset=UTF-8');

        $user = trim(shell_exec('whoami'));
        $directory = trim(shell_exec('pwd'));
        $hostname = $_SERVER['SERVER_NAME'];
        $prefix = sprintf("\033[38;2;200;110;110m%s\033[34m@\033[1;32m%s\033[90m:\033[39m%s\033[90m#", $user, $hostname, $directory);
        $command = escapeshellcmd($_POST['command']);

        if (defined('DEMO') && DEMO) {
            if ($command === 'motd') {
                $output = 'Welcome to the Demoversion of fruithost!';
                printf("%s\n\033[39m%s", $prefix, $output);
                exit();
            }

            $output = "\033[31mERROR:\033[39m Your command is forbidden at the demo version.";
            printf("%s %s\n\033[39m%s", $prefix, $command, $output);
            exit();
        }

        if ($command === 'ColorTest') {
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

            foreach ($tests as $name => $values) {
                printf("--- %s\n", $name);

                foreach ($values as $code => $color) {
                    printf("\033[%sm%s\n", $code, $color);
                }

                print "\n";
            }

            exit();
        }

		setlocale(LC_ALL, I18N::set() . '.UTF-8');
		putenv('LC_ALL=' . I18N::set() . '.UTF-8');
		

        $build = [
            'export MAN_KEEP_FORMATTING=1;',
            'export SHELL=/bin/bash;',
            sprintf('export LANG=%s.UTF-8;', I18N::set()),
            'export TERM=xterm-256color;',
            'export _=/usr/bin/env;',
            'export USER=fruithost;',
            'export HOME="' . PATH . '";',
            #'/bin/bash -c "',
            ($command === 'motd' ? 'cat /etc/motd' : $command),
            #'"',
            ' 2>&1'
        ];

        // @ToDo try to fork it to /dev/ttys000, /dev/pts/0 or other for coloring output?
        // Or using SSH? https://www.php.net/manual/en/function.ssh2-connect

        $process = proc_open(implode('', $build), [
            ['pipe', 'r'],  // stdin
            ['pipe', 'w'],  // stdout
            ['pipe', 'w']      // stderr
        ], $pipes);

        if ($command === 'motd') {
            $command = '';
        }

        if (is_resource($process)) {
            $stdin = $pipes[0];
            $stdout = $pipes[1];
            $stderr = $pipes[2];

            fclose($stdin);

            stream_set_blocking($stdout, false);
            stream_set_blocking($stderr, false);

            $outEof = false;
            $errEof = false;
            $result = '';
            $error = '';

            if (!posix_isatty($stdout)) {
                #$result = "NOT TTY : " . posix_ttyname($stdout);
            }

            do {
                $read = [$stdout, $stderr];
                $write = null;
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
            } while (!$outEof || !$errEof);

            fclose($stdout);
            fclose($stderr);
            proc_close($process);

            if ($result == "\x1B[H\x1B[2J\x1B[3J") {
                print $result;
            } else {
                if ($result === null) {
                    $result = '-bash: ' . $command . ': command not found';
                } else if ($result === false) {
                    $result = '-bash: piped error';
                }

                printf("%s \033[39m %s\n%s", $prefix, $command, $result);
            }
        }
        exit();
    }
	
	$template->getFiles()->addJavascript('terminal', $this->url('js/terminal.js'), '1.0.0', [ 'ajax' ], TemplateFiles::FOOTER);
	$template->getFiles()->addJavascript('console', $this->url('js/console.js'), '1.0.0', [ 'terminal' ], TemplateFiles::FOOTER);
?>