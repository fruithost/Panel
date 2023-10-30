<?php
	namespace fruithost;
	
	use \fruithost\Auth;
	use \fruithost\Database;
	
	class User {
		private $id			= null;
		private $username	= null;
		private $email		= null;
		private $data		= [];
		
		public function __construct() {}
		
		public function fetch(int $id) {
			$result = Database::single('SELECT *, "**********" as `password` FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:id LIMIT 1', [
				'id'	=> $id
			]);
			
			$this->id 		= $result->id;
			$this->username = $result->username;
			$this->email 	= $result->email;
			
			$this->data = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'users_data` WHERE `user_id`=:user_id LIMIT 1', [
				'user_id'	=> $this->id
			]);
			
			if($this->data !== false) {
				foreach($this->data AS $index => $entry) {
					if(in_array($index, [ 'id', 'user_id' ])) {
						continue;
					}
					
					$this->data->{$index} = Encryption::decrypt($entry, ENCRYPTION_SALT);
				}
			}
		}
		
		public function getID() : int | null {
			/*if($this->id == null && Auth::getID() != null) {
				return Auth::getID();
			}*/
			
			return $this->id;
		}
		
		public function getFullName() : string | null {
			if(empty($this->data->name_first)) {
				return '';
			}
			
			return sprintf('%s %s', $this->data->name_first, $this->data->name_last);
		}
		
		public function getMail() : string | null {
			return $this->email;
		}
		
		public function getUsername() : string | null {
			return $this->username;
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
				'user_id'	=> (empty($user_id) ? $this->getID() : $user_id),
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
				'user_id'	=> (empty($user_id) ? $this->getID() : $user_id),
				'key'		=> $name
			])) {
				Database::update(DATABASE_PREFIX . 'users_settings', [ 'user_id', 'key' ], [
					'user_id'		=> (empty($user_id) ? $this->getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert(DATABASE_PREFIX . 'users_settings', [
					'id'			=> NULL,
					'user_id'		=> (empty($user_id) ? $this->getID() : $user_id),
					'key'			=> $name,
					'value'			=> $value
				]);
			}
		}
		
		public function getGravatar() : string {
			return sprintf('https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s', md5(strtolower(trim($this->email))), 22, 'mp', 'g');
		}
	}
?>