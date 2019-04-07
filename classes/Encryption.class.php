<?php
	namespace fruithost;
	
	class Encryption {		
		public static function encrypt($data, $secret) {
			return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $secret, 0, substr(ENCRYPTION_SALT, 0, 16)));
		}

		public static function decrypt($data, $secret) {
			return openssl_decrypt(base64_decode($data), 'aes-256-cbc', $secret, 0, substr(ENCRYPTION_SALT, 0, 16));
		}
	}
?>