<?php
	namespace fruithost\Hardware;
	
	class PhysicalDrivesFactory {
		private static ?PhysicalDrivesFactory $instance = null;
		private array $devices = [];
		
		public static function getInstance() : PhysicalDrivesFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		protected function __construct() {
			foreach(file('/proc/diskstats', \FILE_SKIP_EMPTY_LINES) AS $line) {
				preg_match('/\s+(\d+)\s+(\d+)\s+([a-zA-Z0-9]+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $line, $matches);
				
				if(!empty($matches)) {
					if(str_starts_with($matches[3], 'loop') || str_starts_with($matches[3], 'ram')) {
						continue;
					}
					
					$df	= preg_split('/\s+/', (explode(PHP_EOL, trim(shell_exec(sprintf('df /dev/%s', $matches[3])))))[1]);

					$this->devices[] = [
						'name'			=> $matches[3],
						'text'			=> trim(shell_exec(sprintf('cat /sys/block/%s/device/model', $matches[3]))),
						'type'			=> shell_exec(sprintf('blkid -o value -s TYPE /dev/%s 2>&1', $matches[3])),
						'blocks'		=> trim(shell_exec(sprintf('cat /sys/class/block/%s/queue/logical_block_size', $matches[3]))),
						'size'			=> $df[1],
						'used'			=> $df[2],
						'available'		=> $df[3],
						'percent'		=> (int) $df[4],
						'filesystem'	=> $df[5]
					];
				}
			}
		}
		
		public function getDevices() : array {
			return $this->devices;
		}
	}
	
	class PhysicalDrives {
		public static function get() : ?PhysicalDrivesFactory {
			return PhysicalDrivesFactory::getInstance();
		}
		
		public static function getDevices() : array {
			return PhysicalDrivesFactory::getInstance()->getDevices();
		}
	}
?>