<?php
	namespace fruithost\Hardware;

	enum NetworkFlag : string {
		// @see https://github.com/iproute2/iproute2/blob/e9096586e0701d5ae031df2f2708d20d34ae7bd4/bridge/link.c#L61

		// Valid broadcast address set.
		case BROADCAST			= "BROADCAST";

		// Supports multicast
		case MULTICAST			= "MULTICAST";

		// Master of a load balancing bundle.
		case MASTER				= "MASTER";

		// Slave of a load balancing bundle.
		case SLAVE				= "SLAVE";

		// Interface is running.
		case UP					= "UP";

		// Driver signals L1 up (since Linux 2.6.17)
		case LOWER_UP			= "LOWER_UP";

		// Interface is a loopback interface.
		case LOOPBACK			= "LOOPBACK";

		// Interface is a point-to-point link.
		case POINTOPOINT		= "POINTOPOINT";

		// No arp protocol, L2 destination address not set.
		case NOARP				= "NOARP";

		// Receive all multicast packets.
		case ALLMULTI			= "ALLMULTI";

		// Interface is in promiscuous mode.
		case PROMISC			= "PROMISC";

		// Internal debugging flag.
		case DEBUG				= "DEBUG";

		// The addresses are lost when the interface goes down.
		case DYNAMIC			= "DYNAMIC";

		// Auto media selection active
		case AUTOMEDIA			= "AUTOMEDIA";

		// Is able to select media type via ifmap.
		case PORTSEL			= "PORTSEL";

		// Avoid use of trailers. NOT used by the Linux and exists for BSD compatibility
		case NOTRAILERS			= "NOTRAILERS";

		// Driver signals dormant (since Linux 2.6.17)
		// @see https://docs.nvidia.com/networking-ethernet-software/knowledge-base/Configuration-and-Usage/Network-Interfaces/What-Does-DORMANT-Mean-for-MLAG-Bond-Interfaces/
		case DORMANT			= "DORMANT";

		public static function fromName(string $name) : NetworkFlag {
			foreach(self::cases() as $status) {
				if($name === $status->value){
					return $status;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function for(NetworkFlag $name) : string {
			foreach(self::cases() as $status) {
				if($status->name === $name->name) {
					return $status->name;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function tryFromName(string $name) : NetworkFlag | null {
			try {
				return self::fromName($name);
			} catch (\ValueError $error) {
				return null;
			}
		}
	}
?>