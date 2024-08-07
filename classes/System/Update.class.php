<?php
	namespace fruithost\System;
	
    use fruithost\Storage\Database;
	 
	class Update {
		public static function check() {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> 'UPDATE_ENABLED'
			])) {
				$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
					'key'		=> 'UPDATE_ENABLED'
				]);
				
				if($result->value === 'true') {
					$license = self::getLicense();
					
					$result = self::get('getVersion', [
						'license'	=> $license,
						'host'		=> $_SERVER['SERVER_NAME'],
						'ip'		=> $_SERVER['SERVER_ADDR'],
						'admin'		=> $_SERVER['SERVER_ADMIN'],
						'ssl'		=> $_SERVER['HTTPS'] == 'on'
					]);
					
					/* Refresh License */
					if($result->status == false && $result->error == 'LICENSE_PROBLEM') {
						Database::delete(DATABASE_PREFIX . 'settings', [
							'key' => 'UPDATE_LICENSE'
						]);
						self::check();
						return;
					}
					
					self::setOnlineVersion($result->version);
				}
			} else {
				Database::insert(DATABASE_PREFIX . 'settings', [
					'id'			=> null,
					'key'			=> 'UPDATE_ENABLED',
					'value'			=> 'true'
				]);
			}
		}
		
		protected static function get($action, $data) {
			$ch = curl_init(sprintf('https://%s/', UPDATE_ENDPOINT));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge([
				'action'	=> $action
			], $data));
			$response = curl_exec($ch);
			curl_close($ch);
			
			try {
				$json = json_decode($response);
				
				if($json != NULL) {
					$response = $json;
				}
			} catch(Exception $e) {
				
			}
			return $response;
		}
		
		protected static function setOnlineVersion($version) {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> 'UPDATE_VERSION'
			])) {
				Database::update(DATABASE_PREFIX . 'settings', 'key', [
					'key'			=> 'UPDATE_VERSION',
					'value'			=> $version
				]);
			
			} else {
				Database::insert(DATABASE_PREFIX . 'settings', [
					'id'			=> null,
					'key'			=> 'UPDATE_VERSION',
					'value'			=> $version
				]);
			}
		}
		
		public static function getLicense() {
			if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
				'key'		=> 'UPDATE_LICENSE'
			])) {
				$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
					'key'		=> 'UPDATE_LICENSE'
				]);
				
				return $result->value;
			} else {
				$result = self::get('getLicense', [
					'host'	=> $_SERVER['SERVER_NAME'],
					'ip'	=> $_SERVER['SERVER_ADDR'],
					'admin'	=> $_SERVER['SERVER_ADMIN'],
					'ssl'	=> $_SERVER['HTTPS'] == 'on'
				]);
				
				Database::insert(DATABASE_PREFIX . 'settings', [
					'id'			=> null,
					'key'			=> 'UPDATE_LICENSE',
					'value'			=> $result->license
				]);
				
				return $result->license;
			}
		}
	}
?>