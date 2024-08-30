<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */
	namespace fruithost\Accounting;
	class Auth {
		public static function isLoggedIn() : bool {
			return AuthFactory::getInstance()->isLoggedIn();
		}
		
		public static function TwoFactorLogin(string $username, #[\SensitiveParameter] string $password) : bool {
			return AuthFactory::getInstance()->TwoFactorLogin($username, $password);
		}
		
		public static function login(string $username, #[\SensitiveParameter] string $password) : bool {
			return AuthFactory::getInstance()->login($username, $password);
		}
		
		public static function logout() : bool {
			return AuthFactory::getInstance()->logout();
		}
		
		public static function getID() : ?int {
			return AuthFactory::getInstance()->getID();
		}
		
		public static function getUsername() : ?string {
			return AuthFactory::getInstance()->getUsername();
		}
		
		public static function getMail() : ?string {
			return AuthFactory::getInstance()->getMail();
		}
		
		public static function getGravatar() : string {
			return AuthFactory::getInstance()->getGravatar();
		}
		
		public static function setSettings(string $name, int | string | null $user_id = null, mixed $value = null) : void {
			AuthFactory::getInstance()->setSettings($name, $user_id, $value);
		}
		
		public static function getSettings(string $name, int | string | null $user_id = null, mixed $default = null) : mixed {
			return AuthFactory::getInstance()->getSettings($name, $user_id, $default);
		}
		
		public static function removeSettings(string $name, int | string | null $user_id = null) : void {
			AuthFactory::getInstance()->removeSettings($name, $user_id);
		}
		
		public static function hasPermission(string $name) : bool {
			return AuthFactory::getInstance()->hasPermission($name);
		}
		
		public static function getPermissions() : array {
			return AuthFactory::getInstance()->getPermissions();
		}
	}
	
	?>