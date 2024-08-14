<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\System;
	
    use fruithost\Storage\Database;
	use fruithost\System\Core;

	class Update {
		protected static Core $core;

		public static function bind($core) : Update {
			self::$core = $core;

			return new self();
		}

		public static function check() {
			if(self::$core->getSettings('UPDATE_ENABLED', true)) {


				$license	= self::getLicense();
				$result		= self::get('getVersion', [
					'license'	=> $license,
					'host'		=> $_SERVER['SERVER_NAME'],
					'ip'		=> $_SERVER['SERVER_ADDR'],
					'admin'		=> $_SERVER['SERVER_ADMIN'],
					'ssl'		=> $_SERVER['HTTPS'] == 'on'
				]);

				/* Refresh License */
				if(!$result->status && $result->error == 'LICENSE_PROBLEM') {
					self::$core->removeSettings('UPDATE_LICENSE');
					self::check();
					return;
				}

				self::setOnlineVersion($result->version);
			}
		}
		
		protected static function get($action, $data) {
			$request = curl_init(sprintf('https://%s/', UPDATE_ENDPOINT));
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, array_merge([
				'action'	=> $action
			], $data));
			$response = curl_exec($request);
			curl_close($request);
			
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
			self::$core->setSettings('UPDATE_VERSION', $version);
		}
		
		public static function getLicense() {
			if(self::$core->hasSettings('UPDATE_LICENSE')) {
				return self::$core->getSettings('UPDATE_LICENSE', true);
			} else {
				$result = self::get('getLicense', [
					'host'	=> $_SERVER['SERVER_NAME'],
					'ip'	=> $_SERVER['SERVER_ADDR'],
					'admin'	=> $_SERVER['SERVER_ADMIN'],
					'ssl'	=> $_SERVER['HTTPS'] == 'on'
				]);

				self::$core->setSettings('UPDATE_LICENSE', $result->license);
				
				return $result->license;
			}
		}
	}
?>