<?php
    namespace fruithost\Security;

	class Encryption {		
		public static function encrypt(string $data, #[\SensitiveParameter] string $secret) : string | false {
			return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $secret, 0, substr(ENCRYPTION_SALT, 0, 16)));
		}

		public static function decrypt(string $data, #[\SensitiveParameter] string $secret) : string | false {
			return openssl_decrypt(base64_decode($data), 'aes-256-cbc', $secret, 0, substr(ENCRYPTION_SALT, 0, 16));
		}
	}
?>