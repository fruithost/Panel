<?php
	namespace fruithost;
	use \fruithost\Session;
	
	class AuthFactory {
		private static $instance	= NULL;
		private $permissions		= [];
		
		public static function getInstance() : AuthFactory {
			if(self::$instance === NULL) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function isLoggedIn() : bool {
			$user_id = Session::get('user_id');
			
			return (!empty($user_id) && $user_id > 0);
		}
		
		public function getID() : int | null {
			return Session::get('user_id');
		}
		
		public function getUsername() : string | null {
			return Session::get('user_name');
		}
		
		public function getData(string $name, int | null $id = NULL, mixed $default = NULL) : mixed {
			$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:id LIMIT 1', [
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
		
		public function login(string $username, string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
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
				Session::set('user_id',		(int) $result->id);
			} else {
				throw new \Exception('Unknown User');
				return false;				
			}
			
			return true;
		}
		
		public function TwoFactorLogin(string $username, string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
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
		
		public function getSettings(string $name, int | string | null $user_id = NULL, mixed $default = NULL) : mixed {
			if(!empty($user_id) && is_string($user_id)) {
				$result = Database::single('SELECT `id` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
					'username'	=> $user_id
				]);
				
				if(!empty($result)) {
					$user_id = $result->id;
				}
			}
			
			$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users_settings` WHERE `user_id`=:user_id AND `key`=:key LIMIT 1', [
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
		
		public function removeSettings(string $name, int | string | null $user_id = NULL) {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'users_settings` WHERE `user_id`=:user_id AND `key`=:key LIMIT 1', [
				'user_id'	=> (empty($user_id) ? $this->getID() : $user_id),
				'key'		=> $name
			])) {
				Database::delete(DATABASE_PREFIX . 'users_settings', [
					'user_id'	=> (empty($user_id) ? $this->getID() : $user_id),
					'key'		=> $name
				]);
			}
		}
		
		public function setSettings(string $name, int | string | null $user_id = NULL, mixed $value = NULL) {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'users_settings` WHERE `user_id`=:user_id AND `key`=:key LIMIT 1', [
				'user_id'	=> (empty($user_id) ? Auth::getID() : $user_id),
				'key'		=> $name
			])) {
				Database::update(DATABASE_PREFIX . 'users_settings', [ 'user_id', 'key' ], [
					'user_id'		=> (empty($user_id) ? Auth::getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert(DATABASE_PREFIX . 'users_settings', [
					'id'			=> NULL,
					'user_id'		=> (empty($user_id) ? Auth::getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			}
		}
		
		public function getGravatar() : string {
			return sprintf('https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s', md5(strtolower(trim($this->getData('email')))), 22, 'mp', 'g');
		}
		
		public function hasPermission(string $name, int | string | null $user_id = NULL) : bool {
			if(count($this->permissions) > 0) {
				if($name === '*') {
					return count($this->permissions) >= 1;
				}
				
				return in_array($name, $this->permissions);
			}
			
			foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'users_permissions` WHERE `user_id`=:user_id', [
				'user_id'	=> (empty($user_id) ? self::getID() : $user_id)
			]) AS $entry) {
				$this->permissions[] = $entry->permission;
			}
			
			
			if($name === '*') {
				return count($this->permissions) >= 1;
			}
			
			return in_array($name, $this->permissions);
		}
	}
?>