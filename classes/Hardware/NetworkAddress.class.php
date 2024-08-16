<?php
	namespace fruithost\Hardware;

	class NetworkAddress {
		private NetworkFamily $type = NetworkFamily::UNKNOWN;
		private string $name = "";
		private int $prefix = 0;
		private string $address = "";
		private string $broadcast = "";
		private int $time_ttl = 0;
		private int $time_valid = 0;

		public function __construct(mixed $data) {
			// inet
			if(isset($data->family)) {
				$this->type			= NetworkFamily::tryFromName($data->family);
			}

			if(isset($data->label)) {
				$this->name			= $data->label;
			}

			// IP-Address
			if(isset($data->local)) {
				$this->address		= $data->local;
			}

			// Integer - Subnetting
			if(isset($data->prefixlen)) {
				$this->prefix		= $data->prefixlen;
			}

			// IP-Address
			if(isset($data->broadcast)) {
				$this->broadcast	= $data->broadcast;
			}

			// @see https://github.com/iproute2/iproute2/blob/main/ip/ip_common.h#L219
			// INFINITY_LIFE_TIME = 0xFFFFFFFFU, unsigned

			// Int - Timestamp
			if(isset($data->valid_life_time)) {
				$this->time_ttl		= $data->valid_life_time;
			}

			// Int - Timestamp
			if(isset($data->valid_life_time)) {
				$this->time_valid	= $data->valid_life_time;
			}
		}

		public function getName() : string {
			return $this->name;
		}

		public function getFamily() : NetworkFamily {
			return $this->type;
		}

		public function getAddress() : string {
			return $this->address;
		}

		public function getPrefix() : int {
			return $this->prefix;
		}

		public function getBroadcast() : string {
			return $this->broadcast;
		}

		public function getTTL() : int {
			return $this->time_ttl;
		}

		public function getValidTTL() : int {
			return $this->time_valid;
		}
	}
?>