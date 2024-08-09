<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Hardware;

	enum NetworkState : string {
		case UP			= "UP";
		case DOWN		= "DOWN";
		case UNKNOWN	= "UNKNOWN";

		public static function fromName(string $name): NetworkState {
			foreach(self::cases() as $status) {
				if($name === $status->value){
					return $status;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class );
		}

		public static function tryFromName(string $name) : NetworkState | null {
			try {
				return self::fromName($name);
			} catch (\ValueError $error) {
				return null;
			}
		}
	}

	class NetworkInterface {
		private ?string $id = null;
		private bool $virtual = false;
		private bool $tunnel = false;
		private ?string $address = null;
		private ?string $broadcast = null;
		private ?string $type = null;
		private ?NetworkState $state = null;

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
		public function setAddress($address) {
			$this->address = $address;
		}
		public function setBroadcast($broadcast) {
			$this->broadcast = $broadcast;
		}

		public function enable() {
			shell_exec(sprintf('ifup %s', $this->id));
		}

		public function disable() {
			shell_exec(sprintf('ifdown %s', $this->id));
		}
	}
?>
