<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    namespace fruithost\System;
	
	if(is_readable('.DEBUG') || is_readable('../.DEBUG')) {
		define('DEBUG', true);
	}

	if(is_readable('~DEMO') || is_readable('../~DEMO')) {
		define('DEMO', true);
	}
	
	if(defined('DEBUG') && DEBUG) {
		@ini_set('display_errors', true);
		@ini_set('log_errors ', false);
		error_reporting(E_ALL);
	}
	
	if(!defined('UPDATE_ENDPOINT')) {
		define('UPDATE_ENDPOINT', 'update.fruithost.de');
	}
	
	define('TAB',	"\t");
	define('BS',	'\\');
	define('DS',	DIRECTORY_SEPARATOR);
	
	class Loader {
		public function __construct() {
			if((defined('DAEMON') && DAEMON) || (!empty($_SERVER['DAEMON']))) {
				if(!defined('DAEMON')) {
					define('DAEMON', true);
				}
			}
			
			if(!defined('PATH')) {
				define('PATH', sprintf('%s/', dirname(__FILE__, 3)));
			}
			
			if($this->readable('.security')) {
				$this->require('.security');
			} else if($this->readable('../.security')) {
				$this->require('../.security');
			} else if($this->readable('../../.security')) {
				$this->require('../../.security');
			} else {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading .security ' . PHP_EOL;

				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
			}
			
			if($this->readable('.mail')) {
				$this->require('.mail');
			} else if($this->readable('../.mail')) {
				$this->require('../.mail');
			} else if($this->readable('../../.mail')) {
				$this->require('../../.mail');
			}
			
			if($this->readable('.config')) {
				$this->require('.config');
			} else if($this->readable('../.config')) {
				$this->require('../.config');
            } else if($this->readable('../../.config')) {
                $this->require('../../.config');
			} else {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading .config.php ' . PHP_EOL;
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
			}
			
			# @since 1.0.4
			if(!defined('CONFIG_PATH')) {
				define('CONFIG_PATH', sprintf('%s/config/', dirname(__FILE__, 4)));
			}
			
			spl_autoload_register([ $this, 'load' ]);

			// @ToDo Hash verify?
			if(!empty($_SERVER['MODULE'])) {
				if(file_exists($_SERVER['MODULE'])) {
					$this->require('libraries/skoerfgen/ACMECert');
					
					if(is_readable($_SERVER['MODULE'])) {
						require_once($_SERVER['MODULE']);
					} else {
						printf('Error: Module is not readable (%s).', $_SERVER['MODULE']);
					}
				} else {
					printf('Error: Can\'t load module (%s).', $_SERVER['MODULE']);
				}
			}
		}
	
		private function readable(string $file) : bool {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				return false;
			}
			
			return is_readable($path);
		}
		
		private function require(string $file) : void {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading: ' . $path . PHP_EOL;
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
				return;
			}
			
			require_once($path);
		}
		
		public function load(string $class) : void {
			$file			= trim($class, BS);
			$file_array		= explode(BS, $file);
			
			array_shift($file_array);
			array_unshift($file_array, 'classes');

			$path			= sprintf('%s%s.class.php', PATH, implode(DS, $file_array));

			if(!file_exists($path)) {
				// Check if it's an Enum
				$enum		= sprintf('%s%s.enum.php', PATH, implode(DS, $file_array));

				if(file_exists($enum)) {
					require_once($enum);

				// Check it's a Library
				} else {
					$file_array = explode(BS, $file);
					array_unshift($file_array, 'libraries');
					$path		= sprintf('%s%s.php', PATH, implode(DS, $file_array));

					if(!is_readable($path)) {
						if(defined('DAEMON') && DAEMON) {
							print "\033[31;31m";
						}

						print 'Error accessing Library: ' . $path . PHP_EOL;

						if(defined('DAEMON') && DAEMON) {
							print "\033[39m";
						}
						return;
					}

					if(file_exists($path)) {
						require_once($path);
						return;
					}
					// Check it's a Library on a module!
					/*} else if(preg_match('/\/module\//Uis', $_SERVER['REQUEST_URI'])) {
						$file_array		= explode(BS, $file);
						array_unshift($file_array, 'www');
						array_unshift($file_array, str_replace('module', 'modules', $_SERVER['REQUEST_URI']));
						$path	= sprintf('%s%s.php', dirname(PATH), implode(DS, $file_array));
						require_once($path);
						return;
					}*/

					if(defined('DAEMON') && DAEMON) {
						print "\033[31;31m";
					}

					print 'Error Loading Library: ' . $path . PHP_EOL;

					if(defined('DAEMON') && DAEMON) {
						print "\033[39m";
					}
				}

				return;
			}

			require_once($path);
		}
	}
	
	if((defined('DAEMON') && DAEMON) || (!empty($_SERVER['DAEMON']))) {
		new Loader();
	}
?>