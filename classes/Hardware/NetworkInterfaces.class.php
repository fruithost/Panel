<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

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
			$result = shell_exec('ls /sys/class/net | jq -R -s -c \'split("\n")[:-1]\'');

			foreach(json_decode($result) AS $entry) {
				$this->devices[$entry] = new NetworkInterface($entry);
			}

			$result = shell_exec('ip --json address show');

			foreach(json_decode($result) AS $entry) {
				$device = $this->devices[$entry->ifname];
				$device->debug = $entry;

				if(isset($entry->link_type)) {
					$device->setType($entry->link_type);
				}
				if(isset($entry->operstate)) {
					$device->setState(NetworkState::tryFromName($entry->operstate));
				}

				if(isset($entry->address)) {
					$device->setAddress($entry->address);
				}

				if(isset($entry->broadcast)) {
					$device->setBroadcast($entry->broadcast);
				}
			}
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

		public function getDevice($id) : NetworkInterface | null {
			return $this->devices[$id];
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

		public static function getDevice($id) : NetworkInterface | null {
			return NetworkInterfacesFactory::getInstance()->getDevice($id);
		}
	}
?>