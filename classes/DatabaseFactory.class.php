<?php
	namespace fruithost;
	
	class DatabaseFactory extends \PDO {
		private static $instance = NULL;
		
		public static function getInstance() {
			if(self::$instance === NULL) {
				self::$instance = new self(sprintf('mysql:host=%s;port=%d;dbname=%s', DATABASE_HOSTNAME, DATABASE_PORT, DATABASE_NAME), DATABASE_USERNAME, DATABASE_PASSWORD, [
					\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					#\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					#\PDO::ATTR_EMULATE_PREPARES => false
				]);
			}
			
			return self::$instance;
		}
		
		public function getError(object $object = NULL) {
			if(empty($object)) {
				return $this->errorInfo();
			}
			
			return $object->errorInfo();
		}
		
		public function file(string $file, callable $callback) {
			if(!file_exists($file)) {
				call_user_func_array($callback, [ 'Can\'t found file: ' . $file ]);
				return;
			}
			
			$sql = file_get_contents($file);
			
			if(empty(trim($sql))) {
				call_user_func_array($callback, [ 'File is empty: ' . $file ]);
				return;
			}
			
			$sql = str_replace([
				'[DATABASE_PREFIX]'
			], [
				DATABASE_PREFIX
			], $sql);
			
			$stmt = $this->query($sql);
			
			if(!$stmt) {
				call_user_func_array($callback, [ $this->getError() ]);
				return;
			}
			
			call_user_func_array($callback, [ NULL ]);
		}
		
		public function query(string $query, ?int $fetchMode = null, mixed ...$parameters): \PDOStatement | false {
			$parameters = $parameters[0];
			$stmt = $this->prepare($query);
			
			if($stmt) {
				$stmt->execute($parameters);
			}
			
			return $stmt;
		}
		
		public function single(string $query, array $parameters = []) {
			return $this->query($query, null, $parameters)->fetch(\PDO::FETCH_OBJ);
		}
		
		public function count(string $query, array $parameters = []) {
			return $this->query($query, null, $parameters)->rowCount();
		}
		
		public function fetch(string $query, array $parameters = []) {
			return $this->query($query, null, $parameters)->fetchAll(\PDO::FETCH_OBJ);
		}
		
		public function update(string $table, string $where, array $parameters = []) {
			$fields = '';
			
			foreach($parameters AS $name => $value) {
				$fields .= sprintf('`%1$s`=:%1$s, ', $name);
			}
			
			if(is_array($where)) {
				$query	= sprintf('UPDATE `%1$s` SET %2$s WHERE', $table, rtrim($fields, ', '));
				$checks	= [];
				
				foreach($where AS $entry) {
					$checks[] = sprintf('`%1$s`=:%1$s', $entry);
				}
				
				$query .= implode(' AND ', $checks);
			} else {
				$query = sprintf('UPDATE `%1$s` SET %2$s WHERE `%3$s`=:%3$s', $table, rtrim($fields, ', '), $where);
			}
			
			return $this->query($query, null, $parameters)->fetchAll(\PDO::FETCH_OBJ);
		}
		
		public function reset(string $table, $where, $old, $new) {
			return $this->query(sprintf('UPDATE `%1$s` SET %2$s=%4$d WHERE `%2$s`=:%3$d', $table, $where, $old, $new));
		}
		
		public function delete(string $table, array $parameters = []) {
			$where = [];
			
			foreach($parameters AS $name => $value) {
				$where[] = sprintf('`%s`=:%s', $name, $name);
			}
			
			return $this->query(sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $where)), null, $parameters);
		}
		
		public function deleteWhereNot(string $table, array $delete_not = [], array $parameters = []) {
			$where				= [];
			$default_parameters	= [];
			
			foreach($parameters AS $name => $value) {
				$default_parameters[] = sprintf('`%s`=:%s', $name, $name);
			}
			
			foreach($delete_not AS $name => $values) {
				if(is_array($values)) {
					foreach($values AS $index => $value) {
						$parameters[$name . '_' . $index]	= $value;
						$where[]							= sprintf('(`%s`!=:%s_%d AND %s)', $name, $name, $index, implode(' AND ', $default_parameters));
					}
				} else {
					$parameters[$name]	= $values;
					$where[]			= sprintf('(`%s`!=:%s AND ?)', $name, $name);
				}
			}
			
			return $this->query(sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $where)), null, $parameters);
		}
		
		public function insert(string $table, array $parameters = []) {
			$names		= [];
			$values		= [];
			
			foreach($parameters AS $name => $value) {
				$names[]	= '`' . $name . '`';
				$values[]	= ':' . $name;
			}
			
			$this->query(sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, implode(', ', $names), implode(', ', $values)), null, $parameters);
			return $this->lastInsertId();
		}
	}
?>