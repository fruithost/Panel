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

		public static function fromName(string $name) : NetworkState {
			foreach(self::cases() as $status) {
				if($name === $status->value){
					return $status;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function for(NetworkState $name) : string {
			foreach(self::cases() as $status) {
				if($status->name === $name->name) {
					return $status->name;
				}
			}

			throw new \ValueError("$name is not a valid backing value for enum " . self::class);
		}

		public static function tryFromName(string $name) : NetworkState | null {
			try {
				return self::fromName($name);
			} catch (\ValueError $error) {
				return null;
			}
		}
	}
?>