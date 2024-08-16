<?php
	namespace fruithost\Hardware;

	enum NetworkFamily : string {
		case IPV4			= "inet";
		case IPV6			= "inet6";
		case UNKNOWN		= "";

		public static function fromName(string $name) : NetworkFamily {
			foreach(self::cases() as $status) {
				if($name === $status->value){
					return $status;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function for(NetworkFamily $name) : string {
			foreach(self::cases() as $status) {
				if($status->name === $name->name) {
					return $status->name;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function tryFromName(string $name) : NetworkFamily | null {
			try {
				return self::fromName($name);
			} catch (\ValueError $error) {
				return null;
			}
		}
	}
?>
