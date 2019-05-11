<?php
	namespace fruithost;
	use \fruithost\Session;
	
	class AuthFactory {
		private static $instance = NULL;
		
		public static function getInstance() {
			if(self::$instance === NULL) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function isLoggedIn() {
			$user_id = Session::get('user_id');
			
			return (!empty($user_id) && $user_id > 0);
		}
		
		public function getID() {
			return Session::get('user_id');
		}
		
		public function getUsername() {
			return Session::get('user_name');
		}
		
		public function getData($name, $id = NULL, $default = NULL) {
			$result = Database::single('SELECT * FROM `fh_users` WHERE `id`=:id LIMIT 1', [
				'id'	=> (empty($id) ? self::getID() : $id)
			]);
			
			if(!empty($result)) {
				if(!empty($result->{$name})) {
					return $result->{$name};
				}
			}
			
			return $default;
		}
		
		public function logout() {
			Session::remove('user_id');
			Session::remove('user_name');
		}
		
		public function login($username, $password) {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `fh_users` WHERE `username`=:username LIMIT 1', [
				'username'	=> $username,
				'password'	=> $password,
				'salt'		=>	MYSQL_PASSWORTD_SALT
			]);
			
			if($result == false) {
				throw new \Exception('Unknown User');
				return false;
			}
			
			if($result->password !== $result->crypted) {
				throw new \Exception('Password mismatched.');
				return false;
			}
			
			if($result->id > 0) {
				Session::set('user_name',	$result->username);
				Session::set('user_id',		$result->id);
			} else {
				throw new \Exception('Unknown User');
				return false;				
			}
			
			return true;
		}
		
		public function TwoFactorLogin($username, $password) {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `fh_users` WHERE `username`=:username LIMIT 1', [
				'username'	=> $username,
				'password'	=> $password,
				'salt'		=>	MYSQL_PASSWORTD_SALT
			]);
			
			if($result == false) {
				throw new \Exception('Unknown User');
				return false;
			}
			
			if($result->password !== $result->crypted) {
				throw new \Exception('Password mismatched.');
				return false;
			}
			
			if($result->id > 0) {
				/* Do Nothing */
			} else {
				throw new \Exception('Unknown User');
				return false;				
			}
			
			return true;
		}
		
		public function getSettings($name, $user_id = NULL, $default = NULL) {
			if(!empty($user_id) && is_string($user_id)) {
				$result = Database::single('SELECT `id` FROM `fh_users` WHERE `username`=:username LIMIT 1', [
					'username'	=> $user_id
				]);
				
				if(!empty($result)) {
					$user_id = $result->id;
				}
			}
			
			$result = Database::single('SELECT * FROM `fh_users_settings` WHERE `user_id`=:user_id AND `key`=:key LIMIT 1', [
				'user_id'	=> (empty($user_id) ? self::getID() : $user_id),
				'key'		=> $name
			]);
			
			if(!empty($result)) {
				if(!empty($result->value)) {
					return $result->value;
				}
			}
			
			return $default;
		}
		
		public function setSettings($name, $user_id = NULL, $value = NULL) {
			if(Database::exists('SELECT `id` FROM `fh_users_settings` WHERE `user_id`=:user_id AND `key`=:key LIMIT 1', [
				'user_id'	=> (empty($user_id) ? Auth::getID() : $user_id),
				'key'		=> $name
			])) {
				Database::update('fh_users_settings', [ 'user_id', 'key' ], [
					'user_id'		=> (empty($user_id) ? Auth::getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert('fh_users_settings', [
					'id'			=> NULL,
					'user_id'		=> (empty($user_id) ? Auth::getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			}
		}
		
		public function getGravatar() {
			return sprintf('https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s', md5(strtolower(trim($this->getData('email')))), 22, 'mp', 'g');
		}
	}
	
	class Auth {
		public static function isLoggedIn() {
			return AuthFactory::getInstance()->isLoggedIn();
		}
		
		public static function TwoFactorLogin($username, $password) {
			return AuthFactory::getInstance()->TwoFactorLogin($username, $password);
		}
		
		public static function login($username, $password) {
			return AuthFactory::getInstance()->login($username, $password);
		}
		
		public static function logout() {
			return AuthFactory::getInstance()->logout();
		}
		
		public static function getID() {
			return AuthFactory::getInstance()->getID();
		}
		
		public static function getUsername() {
			return AuthFactory::getInstance()->getUsername();
		}
		
		public static function getMail() {
			return AuthFactory::getInstance()->getData('email');
		}
		
		public static function getGravatar() {
			return AuthFactory::getInstance()->getGravatar();
		}
		
		public static function setSettings($name, $user_id = NULL, $value) {
			return AuthFactory::getInstance()->setSettings($name, $user_id, $value);			
		}
		
		public static function getSettings($name, $user_id = NULL, $default = NULL) {
			return AuthFactory::getInstance()->getSettings($name, $user_id, $default);
		}
	}
?>