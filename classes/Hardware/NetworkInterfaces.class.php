<?php
	namespace fruithost\Hardware;
	
	class NetworkInterfacesFactory {
		private static ?NetworkInterfacesFactory $instance = null;
		private array $devices = [];
		
		public static function getInstance() : NetworkInterfacesFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		protected function __construct() {
			
		}
		
		public function getDevices() : array {
			return $this->devices;
		}
		
		public function getHostname() : string {
			return trim(shell_exec('hostname'));
		}
		
		public function getPanelHostname() : string {
			return trim($_SERVER['HTTP_HOST']);
		}
		
		public function getIPAddress() : string {
			return trim($_SERVER['SERVER_ADDR']);
		}
	}
	
	class NetworkInterfaces {
		public static function get() : ?NetworkInterfacesFactory {
			return NetworkInterfacesFactory::getInstance();
		}
		
		public static function getDevices() : array {
			return NetworkInterfacesFactory::getInstance()->getDevices();
		}
		
		public static function getHostname() : string {
			return NetworkInterfacesFactory::getInstance()->getHostname();
		}
		
		public static function getPanelHostname() : string {
			return NetworkInterfacesFactory::getInstance()->getDevices();
		}
		
		public static function getIPAddress() : string {
			return NetworkInterfacesFactory::getInstance()->getDevices();
		}
	}
?>