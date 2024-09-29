<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Installer;
	
	use fruithost\Storage\Database;
	
	class Repository {
		const BAD_RESPONSE = 422;
		const FORBIDDEN    = 403;
		const EMPTY        = null;
		private static ?Repository $instance = null;
		
		public function __construct() {}
		
		public static function getInstance() : Repository {
			if(self::$instance === null) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		/*
		 * Register a Repository by Endpoint.
		 *
		 * @example https://github.com/<user>/<repo>
		 * @example git: user:<user> repo:<repo> branch:<branch> token:<token>
		 */
		public function createRepository(string $url) : void {
			Database::insert(DATABASE_PREFIX.'repositorys', [ 'id' => null, 'url' => $url, 'time_updated' => null ]);
		}
		
		/*
		 * Update a Repository by ID.
		 */
		public function updateRepository(int $id, string $timestamp) : void {
			Database::update(DATABASE_PREFIX.'repositorys', 'id', [ 'id' => $id, 'time_updated' => $timestamp ]);
		}
		
		/*
		 * Delete a Repository by ID.
		 */
		public function deleteRepository(int $id) : void {
			Database::delete(DATABASE_PREFIX.'repositorys', [ 'id' => $id ]);
		}
		
		/*
		 * Get a Repository by Endpoint.
		 */
		public function getRepositorys(?string $url = null) : array {
			if($url !== null) {
				$repositorys = Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'repositorys` WHERE `url`=:url', [ 'url' => $url ]);
			} else {
				$repositorys = Database::fetch('SELECT * FROM `'.DATABASE_PREFIX.'repositorys`');
			}
			foreach($repositorys as $repository) {
				$repository = $this->updateRepositoryData($repository);
			}
			
			return $repositorys;
		}
		
		private function updateRepositoryData($repository) {
			if(str_starts_with($repository->url, 'git:')) {
				$parts  = explode(' ', $repository->url);
				$user   = null;
				$repo   = null;
				$token  = null;
				$branch = null;
				foreach($parts as $part) {
					if(str_starts_with($part, 'user:')) {
						$user = str_replace('user:', '', $part);
					} else if(str_starts_with($part, 'repo:')) {
						$repo = str_replace('repo:', '', $part);
					} else if(str_starts_with($part, 'branch:')) {
						$branch = str_replace('branch:', '', $part);
					} else if(str_starts_with($part, 'token:')) {
						$token = str_replace('token:', '', $part);
					}
				}
				$str = [];
				foreach([ 'user' => $user, 'repo' => $repo, 'branch' => $branch, 'token' => '[*** PROTECTED ***]', ] as $name => $value) {
					if(!empty($value)) {
						$str[] = sprintf('%s:%s', $name, $value);
					}
				}
				$repository->url   = sprintf('git: %s', implode(' ', $str));
				$repository->token = $token;
			}
			
			return $repository;
		}
		
		/*
		 * Internal update Repository-Data to override critical informations (for sample: security token/passwords)
		 */
		public function getRepositorysByID(array $ids) : array {
			$repositorys = [];
			foreach($ids as $id) {
				$repositorys[] = Installer::getRepository($id);
			}
			
			return $repositorys;
		}
		
		/*
		 * Get a Repository by ID.
		 */
		public function getRepository(int $id) {
			return $this->updateRepositoryData(Database::single('SELECT * FROM `'.DATABASE_PREFIX.'repositorys` WHERE `id`=:id', [ 'id' => $id ]));
		}
		
		public function getFile($repository, $file) : string | int | null {
			$headers = [];
			$url     = $repository->url;
			$branch  = 'master';
		
			// Load GitHub by RAW
			if(preg_match('/github\.com\/([^\/]+)\/([^\/]+)$/Uis', rtrim($repository->url, '/'), $matches)) {
				$user = rtrim($matches[1], '/');
				$repo = rtrim($matches[2], '/');
				$url  = sprintf('https://raw.githubusercontent.com/%s/%s/%s', $user, $repo, $branch);
				
			// Load by Git-Variables
			} else if(str_starts_with($url, 'git:')) {
				$parts  = explode(' ', $url);
				$user   = null;
				$repo   = null;
				$token  = $repository->token;
				$branch = null;
				
				foreach($parts as $part) {
					if(str_starts_with($part, 'user:')) {
						$user = str_replace('user:', '', $part);
					} else if(str_starts_with($part, 'repo:')) {
						$repo = str_replace('repo:', '', $part);
					} else if(str_starts_with($part, 'branch:')) {
						$branch = str_replace('branch:', '', $part);
					}
				}
				
				$url = sprintf('https://raw.githubusercontent.com/%s/%s/%s', $user, $repo, $branch);
				
				if(!empty($token)) {
					$headers['Authorization']        = sprintf('token %s', $token);
					$headers['Accept']               = 'application/vnd.github.raw+json';
					$headers['User-Agent']           = sprintf('%s@%s (PHP v1.0.0)', $user, $repo);
					$headers['X-GitHub-Api-Version'] = '2022-11-28';
					$url                             = sprintf('https://api.github.com/repos/%s/%s/contents', $user, $repo);
				}
			}
			
			$request = curl_init();
            curl_setopt($request, CURLOPT_URL,				sprintf('%s/%s', $url, $file));
            curl_setopt($request, CURLOPT_HEADER,			false);
            curl_setopt($request, CURLOPT_RETURNTRANSFER,	true);
			
			if(!empty($headers)) {
				$temp = [];
				
				foreach($headers AS $name => $value) {
					$temp[] = sprintf('%s: %s', $name, $value);
				}
				
				curl_setopt($request, CURLOPT_HTTPHEADER, $temp);
			}
			
            $response	= curl_exec($request);
            $code		= curl_getinfo($request, CURLINFO_HTTP_CODE);
            curl_close($request);
			
			if($code != 200) {
				return self::BAD_RESPONSE;
			} else if($code == 403) {
				return self::FORBIDDEN;
			} else if(empty($response)) {
				return self::EMPTY;
			}
			
			return $response;
		}
	}
?>