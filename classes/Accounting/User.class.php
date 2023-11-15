<?php
	namespace fruithost\Accounting;
	
    use fruithost\Security\Encryption;
    use fruithost\Storage\Database;

    class User {
		private ?int $id				= null;
		private ?string $username		= null;
		private ?string $email			= null;
		private ?string $crypted_mail	= null;
		private mixed $data		        = [];
		
		public function __construct() {}
		
		public function fetch(int $id)  : void {
			$result = Database::single('SELECT *, "[*** PROTECTED ***]" as `password`, UPPER(SHA2(CONCAT(`id`, :salt, `email`), 512)) AS `crypted_mail` FROM `' . DATABASE_PREFIX . 'users` WHERE `id`=:id LIMIT 1', [
				'id'	=> $id,
				'salt'	=> RESET_PASSWORD_SALT
			]);
			
			$this->id 				= $result->id;
			$this->username 		= $result->username;
			$this->email 			= $result->email;
			$this->crypted_mail 	= $result->crypted_mail;
			
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
		
		public function getID() : ?int {
			/*if($this->id == null && Auth::getID() != null) {
				return Auth::getID();
			}*/
			
			return $this->id;
		}
		
		public function getFirstName() : ?string {
			if(!$this->data) {
				return null;
			}
			
			return $this->data->name_first;
		}
		
		public function getLastName() : ?string {
            if(!$this->data) {
				return null;
			}
			
			return $this->data->name_last;
		}
		
		public function getPhoneNumber() : ?string {
            if(!$this->data) {
				return null;
			}
			
			return $this->data->phone_number;
		}
		
		public function getAddress() : ?string {
            if(!$this->data) {
				return null;
			}
			
			return $this->data->address;
		}
		
		public function getFullName() : ?string {
            if(!$this->data) {
				return '';
			}
			
			if(empty($this->data->name_first)) {
				return '';
			}
			
			return sprintf('%s %s', $this->data->name_first, $this->data->name_last);
		}
		
		public function getCryptedMail() : ?string {
			return $this->crypted_mail;
		}
		
		public function getMail() : ?string {
			return $this->email;
		}
		
		public function getUsername() : ?string {
			return $this->username;
		}
		
		public function getSettings(string $name, int | string | null $user_id = null, mixed $default = null) : mixed {
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
		
		public function removeSettings(string $name, int | string | null $user_id = null) : void {
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
		
		public function setSettings(string $name, int | string | null $user_id = null, mixed $value = null) : void {
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
		
		public function delete() : void {
			Database::update(DATABASE_PREFIX . 'users', [ 'id' ], [
				'id'		=> (empty($user_id) ? $this->getID() : $user_id),
				'deleted'	=> 'Yes'
			]);
		}
	}
?>