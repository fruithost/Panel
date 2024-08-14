<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

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

					$df	= preg_split('/\s+/', (explode(PHP_EOL, trim(shell_exec(sprintf('df -kP --print-type --sync --total /dev/%s', $matches[3])))))[1]);

					if($df[1] == 'devtmpfs') {
						continue;
					}

					$text 	= shell_exec(sprintf('cat /sys/block/%s/device/model', $matches[3]));
					$blocks = shell_exec(sprintf('cat /sys/class/block/%s/queue/logical_block_size', $matches[3]));

					if($text !== null) {
						$text = trim($text);
					}

					if($blocks !== null) {
						$blocks = trim($blocks);
					}

					$this->devices[] = [
						'name'			=> $matches[3],
						'text'			=> $text,
						'type'			=> $df[1],
						'blocks'		=> $blocks,
						'size'			=> (int) $df[2] * 1024,
						'used'			=> (int) $df[3] * 1024,
						'available'		=> (int) $df[4] * 1024,
						'percent'		=> (int) $df[5],
						'filesystem'	=> $df[6]
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