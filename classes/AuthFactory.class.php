<?php
	namespace fruithost;
	
	use \fruithost\User;
	use \fruithost\Response;
	use \fruithost\Session;
	
	class AuthFactory {
		private static ?AuthFactory $instance	= null;
		private array $permissions				= [];
		private ?User $user						= null;
		
		public static function getInstance() : AuthFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		protected function __construct() {
			$this->user = new User();
			
			if(self::isLoggedIn()) {
				$this->user->fetch(self::getID());
				
				foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'users_permissions` WHERE `user_id`=:user_id', [
					'user_id'	=> self::getID()
				]) AS $entry) {
					$this->permissions[] = $entry->permission;
				}
				
				if(defined('DEBUG') && DEBUG) {
					Response::addHeader('USER', json_encode([
						'ID' 			=> $this->user->getID(),
						'Username'		=> $this->user->getUsername(),
						'IsLoggedIn'	=> $this->isLoggedIn()
					]));
					
					Response::addHeader('PERMISSIONS', json_encode($this->permissions));
				}
			}
		}
		
		public function isLoggedIn() : bool {
			$user_id = Session::get('user_id');
			
			return (!empty($user_id) && $user_id > 0);
		}
		
		public function getID() : ?int {
			return Session::get('user_id');
		}
		
		public function getUsername() : ?string {
			return Session::get('user_name');
		}
		
		public static function logout() : bool {
			Session::remove('user_id');
			Session::remove('user_name');
			
			return true;
		}
		
		public function getMail() : ?string {
			return $this->user->getMail();
		}
		
		public function login(string $username, #[\SensitiveParameter]  string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
				'username'	=> $username,
				'password'	=> $password,
				'salt'		=> MYSQL_PASSWORTD_SALT
			]);
			
			if($result == false) {
				throw new \Exception(I18N::get('Unknown User'));
				return false;
			}
			
			if($result->password !== $result->crypted) {
				throw new \Exception(I18N::get('Password mismatched.'));
				return false;
			}
			
			if($result->id > 0) {
				$this->user->fetch((int) $result->id);
				Session::set('user_name',	$result->username);
				Session::set('user_id',		(int) $result->id);
			} else {
				throw new \Exception(I18N::get('Unknown User'));
				return false;				
			}
			
			return true;
		}
		
		public function TwoFactorLogin(string $username, #[\SensitiveParameter]  string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `email`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `' . DATABASE_PREFIX . 'users` WHERE `username`=:username LIMIT 1', [
				'username'	=> $username,
				'password'	=> $password,
				'salt'		=>	MYSQL_PASSWORTD_SALT
			]);
			
			if($result == false) {
				throw new \Exception(I18N::get('Unknown User'));
				return false;
			}
			
			if($result->password !== $result->crypted) {
				throw new \Exception(I18N::get('Password mismatched.'));
				return false;
			}
			
			if(!filter_var($result->email, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
			
			if($result->id > 0) {
				/* Do Nothing */
			} else {
				throw new \Exception(I18N::get('Unknown User'));
				return false;				
			}
			
			return true;
		}
		
		public function getSettings(string $name, int | string | null $user_id = null, mixed $default = null) : mixed {
			return $this->user->getSettings($name, $user_id, $default);
		}
		
		public function removeSettings(string $name, int | string | null $user_id = null) {
			$this->user->removeSettings($name, $user_id);
		}
		
		public function setSettings(string $name, int | string | null $user_id = null, mixed $value = null) {
			$this->user->setSettings($name, $user_id, $value);
		}
		
		public function getGravatar() : string {
			return $this->user->getGravatar();
		}
		
		public function hasPermission(string $name) : bool {
			if(count($this->permissions) > 0) {
				if($name === '*') {
					return count($this->permissions) >= 1;
				}
				
				return in_array($name, $this->permissions);
			}
			
			if($name === '*') {
				return count($this->permissions) >= 1;
			}
			
			return in_array($name, $this->permissions);
		}
	}
?>