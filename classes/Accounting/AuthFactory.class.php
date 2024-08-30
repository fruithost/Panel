<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */
	namespace fruithost\Accounting;
	
	use fruithost\Localization\I18N;
	use fruithost\Network\Response;
	use fruithost\Storage\Database;
	
	class AuthFactory {
		private static ?AuthFactory $instance    = null;
		private array               $permissions = [];
		private ?User               $user        = null;
		
		protected function __construct() {
			$this->user = new User();
			if(self::isLoggedIn()) {
				$this->user->fetch(self::getID());
				foreach(Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'users_permissions` WHERE `user_id`=:user_id', [ 'user_id' => self::getID() ]) as $entry) {
					$this->permissions[] = $entry->permission;
				}
				if(defined('DEBUG') && DEBUG) {
					Response::addHeader('USER', json_encode([
						'ID'         => $this->user->getID(),
						'Username'   => $this->user->getUsername(),
						'IsLoggedIn' => $this->isLoggedIn()
					]));
					Response::addHeader('PERMISSIONS', json_encode($this->permissions));
				}
			}
		}
		
		public function isLoggedIn() : bool {
			$user_id = Session::get('user_id');
			
			return (!empty($user_id) && $user_id > 0);
		}
		
		/*
			Check if user is logged in.

			@return bool
		 */
		public function getID() : ?int {
			return Session::get('user_id');
		}
		
		/*
			Get user id of the current user.

			@return int | null
		 */
		public function getUsername() : ?string {
			return Session::get('user_name');
		}
		
		/*
            Get username of the current user.

            @return string | null
         */
		public static function getInstance() : AuthFactory {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		/*
            Log out the current user.

            @return bool
         */
		public static function logout() : bool {
			Session::remove('user_id');
			Session::remove('user_name');
			
			return true;
		}
		
		/*
            Get E-Mail address of the current user.

            @return string | null
         */
		public function getMail() : ?string {
			return $this->user->getMail();
		}
		
		/*
            Log in a specific user by `$username` and `$password`.

			@param string $username
			@param string $password
            @return bool
			@throw Exception
         */
		public function login(string $username, #[\SensitiveParameter] string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `'.DATABASE_PREFIX.'users` WHERE `username`=:username LIMIT 1', [
				'username' => $username,
				'password' => $password,
				'salt'     => MYSQL_PASSWORTD_SALT
			]);
			if(!$result) {
				throw new \Exception(I18N::get('Unknown User'));
			}
			if($result->password !== $result->crypted) {
				throw new \Exception(I18N::get('Password mismatched.'));
			}
			if($result->id > 0) {
				$this->user->fetch((int) $result->id);
				Session::set('user_name', $result->username);
				Session::set('user_id', (int) $result->id);
			} else {
				throw new \Exception(I18N::get('Unknown User'));
			}
			
			return true;
		}
		
		/*
            Check a specific user by `$username` and `$password` for Two-Factor-Authentication.

			@param string $username
			@param string $password
            @return bool
			@throw Exception
         */
		public function TwoFactorLogin(string $username, #[\SensitiveParameter] string $password) : bool {
			$result = Database::single('SELECT `id`, `username`, `email`, `password`, UPPER(SHA2(CONCAT(`id`, :salt, :password), 512)) as `crypted` FROM `'.DATABASE_PREFIX.'users` WHERE `username`=:username LIMIT 1', [
				'username' => $username,
				'password' => $password,
				'salt'     => MYSQL_PASSWORTD_SALT
			]);
			if(!$result) {
				throw new \Exception(I18N::get('Unknown User'));
			}
			if($result->password !== $result->crypted) {
				throw new \Exception(I18N::get('Password mismatched.'));
			}
			if(!filter_var($result->email, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
			if($result->id <= 0) {
				throw new \Exception(I18N::get('Unknown User'));
			}
			
			return true;
		}
		
		/*
            Get Settings from (given) user account.

			@param string				$name
			@param string | int | null	$user_id
			@param mixed				$default
            @return mixed
         */
		public function getSettings(string $name, int | string | null $user_id = null, mixed $default = null) : mixed {
			return $this->user->getSettings($name, $user_id, $default);
		}
		
		/*
            Remove Settings from (given) user account.

			@param string				$name
			@param string | int | null	$user_id
         */
		public function removeSettings(string $name, int | string | null $user_id = null) : void {
			$this->user->removeSettings($name, $user_id);
		}
		
		/*
            Set Settings from (given) user account.

			@param string				$name
			@param string | int | null	$user_id
			@param mixed				$value
         */
		public function setSettings(string $name, int | string | null $user_id = null, mixed $value = null) : void {
			$this->user->setSettings($name, $user_id, $value);
		}
		
		/*
            Get Gravatar-URL from actual user.

			@return string
         */
		public function getGravatar() : string {
			return $this->user->getGravatar();
		}
		
		/*
           	Check given Permission from actual user.

			@param string				$name
			@return bool
         */
		public function hasPermission(string $name) : bool {
			if(count($this->permissions) > 0) {
				if($name === '*') {
					return count($this->permissions) >= 1;
				}
				if(in_array('*', $this->permissions)) {
					return true;
				}
				if(stristr($name, '::*') !== false) {
					$count = 0;
					$split = explode('::*', $name);
					foreach($this->permissions as $permission) {
						if(str_starts_with($permission, $split[0])) {
							++$count;
						}
					}
					if($count > 0) {
						return true;
					}
				}
				
				return in_array($name, $this->permissions);
			}
			if($name === '*') {
				return (count($this->permissions) >= 1);
			}
			
			return in_array($name, $this->permissions);
		}
		
		public function getPermissions() : array {
			return $this->permissions;
		}
	}
	
	?>