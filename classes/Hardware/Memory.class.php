<?php
	namespace fruithost\Hardware;
	
	class MemoryFactory {
		private static ?MemoryFactory $instance = null;
		private array $memory = [];
		
		public static function getInstance() : MemoryFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		protected function __construct() {
			foreach(file('/proc/meminfo', \FILE_SKIP_EMPTY_LINES) AS $line) {
				preg_match('/(?<name>[a-zA-Z]+):([\s]+)(?<value>[0-9]+)\s(?<size>[a-zA-Z]+)$/Uis', $line, $matches);
				
				if(!empty($matches['name'])) {
					$this->memory[trim($matches['name'])] = (int) $matches['value'] * 1024;
				}
			}
		}
		
		public function getTotal() : ?int {
			return $this->memory['MemTotal'] + $this->memory['Cached'] + $this->memory['Buffers'];
		}
		
		public function getUsed() : ?int {
			return $this->memory['MemFree'];
		}
		
		public function getSwap() : ?int {
			return ($this->memory['SwapTotal'] - $this->memory['SwapFree']);
		}
		
		public function getAssured() : ?int {
			return $this->memory['MemAvailable'] + $this->memory['Cached'];
		}
		
		public function getInCache() : ?int {
			return ($this->memory['Cached'] + $this->memory['SReclaimable'] - $this->memory['Shmem']) * 10;
		}
		
		public function getPercentage() : ?float {
			return (float) number_format(($this->getUsed() / $this->getTotal()) * 100, 2, '.', ',');
		}
	}
	
	class Memory {
		public static function get() : ?MemoryFactory {
			return MemoryFactory::getInstance();
		}
		
		public static function getUsed() : ?int {
			return MemoryFactory::getInstance()->getUsed();
		}
		
		public static function getTotal() : ?int {
			return MemoryFactory::getInstance()->getTotal();
		}
		
		public static function getSwap() : ?int {
			return MemoryFactory::getInstance()->getSwap();
		}
		
		public static function getAssured() : ?int {
			return MemoryFactory::getInstance()->getAssured();
		}
		
		public static function getInCache() : ?int {
			return MemoryFactory::getInstance()->getInCache();
		}
		
		public static function getPercentage() : ?float {
			return MemoryFactory::getInstance()->getPercentage();
		}
	}
?>