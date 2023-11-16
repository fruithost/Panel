<?php
	namespace fruithost\Hardware;
	
	class OperatingSystemFactory {
		private static ?OperatingSystemFactory $instance = null;
		private array $data		= [];
		private array $kernel	= [];
		private ?object $uptime	= null;
		
		public static function getInstance() : OperatingSystemFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		protected function __construct() {
			$this->catchSystem();
			$this->catchUptime();
			$this->catchKernel();
		}
		
		private function catchKernel() : void {
			$this->kernel = explode(' ', shell_exec('uname -a'));
		}
		
		private function catchUptime() : void {
			$this->uptime			= (object) [];
			$uptime_array			= explode(' ', shell_exec('cat /proc/uptime'));
			$this->uptime->seconds	= round($uptime_array[0], 0);
			$this->uptime->minutes	= $this->uptime->seconds / 60;
			$this->uptime->hours	= $this->uptime->minutes / 60;
			$this->uptime->days		= floor($this->uptime->hours / 24);
			$this->uptime->hours	= sprintf('%02d', floor($this->uptime->hours - ($this->uptime->days * 24)));
			$this->uptime->minutes	= sprintf('%02d', floor($this->uptime->minutes - ($this->uptime->days * 24 * 60) - ($this->uptime->hours * 60)));
		}
		
		private function catchSystem() : void {
			if(strtolower(substr(PHP_OS, 0, 5)) === 'linux') {
				$files	= glob('/etc/*-release');

				foreach($files AS $file) {
					$lines = array_filter(array_map(function($line) {
						$parts = explode('=', $line);
						
						if(count($parts) !== 2) {
							return false;
						}
				
						$parts[1] = str_replace(array('"', "'"), '', $parts[1]);
						
						return $parts;
					}, file($file)));

					foreach($lines AS $line) {
						$this->data[$line[0]] = $line[1];
					}
				}
			}
		}
		
		public function getKernel() : string {
			return $this->kernel[2];
		}
		
		public function getMachineType() : string {
			return $this->kernel[11];
		}
		
		public function getName() : string {
			return $this->data['ID'];
		}
		
		public function getPrettyName() : string {
			return $this->data['PRETTY_NAME'];
		}
		
		public function getCodename() : string {
			return $this->data['VERSION_CODENAME'];
		}
		
		public function getVersion() : string {
			return $this->data['VERSION_ID'];
		}
		
		public function getTime() : string {
			return shell_exec('date +\'%d %b %Y %T %Z\'');
		}
		
		public function getUptime(bool $nicename = false) : string | object {
			if(empty($this->uptime)) {
				return null;
			}
			
			if($nicename) {
				if($this->uptime->days == 0) {
					return sprintf('%s:%s', $this->uptime->hours, $this->uptime->minutes);
				} else {
					return sprintf('%s:%s:%s', $this->uptime->days, $this->uptime->hours, $this->uptime->minutes);
				}
			}
			
			return $this->uptime;
		}
	}
	
	class OperatingSystem {
		public static function getName() : string {
			return OperatingSystemFactory::getInstance()->getName();
		}
		
		public static function getKernel() : string {
			return OperatingSystemFactory::getInstance()->getKernel();
		}
		
		public static function getMachineType() : string {
			return OperatingSystemFactory::getInstance()->getMachineType();
		}
		
		public static function getUptime(bool $nicename = false) : string | object {
			return OperatingSystemFactory::getInstance()->getUptime($nicename);
		}
		
		public static function getPrettyName() : string {
			return OperatingSystemFactory::getInstance()->getPrettyName();
		}
		
		public static function getCodename() : string {
			return OperatingSystemFactory::getInstance()->getCodename();
		}
		
		public static function getVersion() : string {
			return OperatingSystemFactory::getInstance()->getVersion();
		}
		
		public static function getTime() : string {
			return OperatingSystemFactory::getInstance()->getTime();
		}
	}
?>