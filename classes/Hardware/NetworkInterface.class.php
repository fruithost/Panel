<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Hardware;

	class NetworkInterface {
		private ?string $id = null;
		private bool $virtual = false;
		private bool $tunnel = false;
		private ?string $address = null;
		private ?string $broadcast = null;
		private ?string $type = null;
		private ?NetworkState $state = null;
		private array $flags = [];

		private array $addresses = [];

		public function __construct(string $id) {
			$this->id = $id;

			switch($this->id) {
				case 'sit0':				// Tunnel-Device for IPv6 in IPv4
					$this->tunnel	= true;
					$this->virtual	= true;
				break;
				case 'teql0':
					$this->virtual	= true;
				break;
			}
		}

		public function getID() {
			return $this->id;
		}

		public function isVirtual() : bool {
			return $this->virtual;
		}

		public function isTunnel() : bool {
			return $this->tunnel;
		}

		public function getState() : NetworkState {
			return $this->state;
		}
		public function setState(NetworkState $state) {
			$this->state = $state;
		}

		public function getType() {
			return $this->type;
		}
		public function setType($type) {
			$this->type = $type;
		}

		public function addFlag(string $flag) : void {
			$this->flags[] = NetworkFlag::tryFromName($flag);
		}

		public function getFlags() : array {
			return $this->flags;
		}

		public function hasFlag(string $flag) : bool {
			return in_array(NetworkFlag::tryFromName($flag), $this->flags);
		}

		public function setAddress($address) {
			$this->address = $address;
		}

		public function getAddress() {
			return $this->address;
		}

		public function addAddress(NetworkAddress $address) {
			$this->addresses[] = $address;
		}

		public function getAddresses() : array {
			return $this->addresses;
		}

		public function hasAddresses() : bool {
			return !empty($this->addresses);
		}

		public function setBroadcast($broadcast) {
			$this->broadcast = $broadcast;
		}

		public function getBroadcast() {
			return $this->broadcast;
		}

		public function enable() {
			shell_exec(sprintf('ifup %s', $this->id));
		}

		public function disable() {
			shell_exec(sprintf('ifdown %s', $this->id));
		}
	}
?>
