<?php
	/**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Storage;

	class PropertiesCache {
		private array $cache = [];

		public function exists(string $table, string $name) : bool {
			if(isset($this->cache[$name])) {
				return true;
			}

			return Database::exists(sprintf('SELECT `id` FROM `%s%s` WHERE `key`=:key LIMIT 1', DATABASE_PREFIX, $table), [
				'key'		=> $name
			]);
		}

		public function remove(string $table, string $name) : void {
			if(isset($this->cache[$name])) {
				unset($this->cache[$name]);
			}

			Database::delete(DATABASE_PREFIX . $table, [
				'key' => $name
			]);
		}

		public function get(string $table, string $name, mixed $default = null) : mixed {
			if(isset($this->cache[$name])) {
				return $this->cache[$name];
			}

			$result = Database::single(sprintf('SELECT * FROM `%s%s` WHERE `key`=:key LIMIT 1', DATABASE_PREFIX, $table), [
				'key'		=> $name
			]);

			if(!empty($result) && !empty($result->value)) {
				// Is Boolean: False
				if(in_array(strtolower($result->value), [
					'off', 'false', 'no'
				])) {
					$this->cache[$name] = false;
					return $this->cache[$name];

				// Is Boolean: True
				} else if(in_array(strtolower($result->value), [
					'on', 'true', 'yes'
				])) {
					$this->cache[$name] = true;
					return $this->cache[$name];
				}

				$this->cache[$name] = $result->value;
				return $this->cache[$name];
			}

			return $default;
		}

		public function set(string $table, string $name, mixed $value) : void {
			if(is_bool($value)) {
				$value = ($value ? 'true' : 'false');
			}

			if(Database::exists(sprintf('SELECT `id` FROM `%s%s` WHERE `key`=:key LIMIT 1', DATABASE_PREFIX, $table), [
				'key'		=> $name
			])) {
				Database::update(DATABASE_PREFIX . $table, [ 'key' ], [
					'key'			=> $name,
					'value'			=> $value
				]);
			} else {
				Database::insert(DATABASE_PREFIX . $table, [
					'id'			=> null,
					'key'			=> $name,
					'value'			=> $value
				]);
			}

			$this->cache[$name] = $value;
		}
	}
?>