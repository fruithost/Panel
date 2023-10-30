<?php
	namespace fruithost;
	
	class Auth {
		public static function isLoggedIn() : bool {
			return AuthFactory::getInstance()->isLoggedIn();
		}
		
		public static function TwoFactorLogin(string $username, string $password) : bool {
			return AuthFactory::getInstance()->TwoFactorLogin($username, $password);
		}
		
		public static function login(string $username, string $password) : bool {
			return AuthFactory::getInstance()->login($username, $password);
		}
		
		public static function logout() : bool {
			return AuthFactory::getInstance()->logout();
		}
		
		public static function getID() : int | null {
			return AuthFactory::getInstance()->getID();
		}
		
		public static function getUsername() : string | null {
			return AuthFactory::getInstance()->getUsername();
		}
		
		public static function getMail() : string | null {
			return AuthFactory::getInstance()->getMail();
		}
		
		public static function getGravatar() : string {
			return AuthFactory::getInstance()->getGravatar();
		}
		
		public static function setSettings(string $name, int | string | null $user_id = NULL, mixed $value = NULL) {
			return AuthFactory::getInstance()->setSettings($name, $user_id, $value);			
		}
		
		public static function getSettings(string $name, int | string | null $user_id = NULL, mixed $default = NULL) : mixed {
			return AuthFactory::getInstance()->getSettings($name, $user_id, $default);
		}
		
		public static function removeSettings(string $name, int | string | null $user_id = NULL) {
			return AuthFactory::getInstance()->removeSettings($name, $user_id);
		}
		
		public static function hasPermission(string $name, int | string | null $user_id = NULL) : bool {
			return AuthFactory::getInstance()->hasPermission($name, $user_id);
		}
	}
?>