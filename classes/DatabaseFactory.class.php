<?php
	namespace fruithost;
	
	class DatabaseFactory extends \PDO {
		private static $instance = NULL;
		
		public static function getInstance() {
			if(self::$instance === NULL) {
				self::$instance = new self(sprintf('mysql:host=%s;port=%d;dbname=%s', DATABASE_HOSTNAME, DATABASE_PORT, DATABASE_NAME), DATABASE_USERNAME, DATABASE_PASSWORD, [
					\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
				]);
			}
			
			return self::$instance;
		}
		
		public function query($query, $parameters = []) {
			$stmt = $this->prepare($query);
			$stmt->execute($parameters);
			return $stmt;
		}
		
		public function single($query, $parameters = []) {
			return $this->query($query, $parameters)->fetch(\PDO::FETCH_OBJ);
		}
		
		public function count($query, $parameters = []) {
			return $this->query($query, $parameters)->rowCount();
		}
		
		public function fetch($query, $parameters = []) {
			return $this->query($query, $parameters)->fetchAll(\PDO::FETCH_OBJ);
		}
		
		public function update($table, $where, $parameters = []) {
			$fields = '';
			
			foreach($parameters AS $name => $value) {
				$fields .= sprintf('`%1$s`=:%1$s, ', $name);
			}
			
			return $this->query(sprintf('UPDATE `%1$s` SET %2$s WHERE `%3$s`=:%3$s', $table, rtrim($fields, ', '), $where), $parameters)->fetchAll(\PDO::FETCH_OBJ);
		}
		
		public function reset($table, $where, $old, $new) {
			return $this->query(sprintf('UPDATE `%1$s` SET %2$s=%4$d WHERE `%2$s`=:%3$d', $table, $where, $old, $new));
		}
		
		public function delete($table, $parameters = []) {
			$where = [];
			
			foreach($parameters AS $name => $value) {
				$where[] = sprintf('`%s`=:%s', $name, $name);
			}
			
			return $this->query(sprintf('DELETE FROM `%s` WHERE %s', $table, implode(', ', $where)), $parameters);
		}
		
		public function deleteWhereNot($table, $delete_not = [], $parameters = []) {
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
			
			return $this->query(sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $where)), $parameters);
		}
		
		public function insert($table, $parameters = []) {
			$names		= [];
			$values		= [];
			
			foreach($parameters AS $name => $value) {
				$names[]	= '`' . $name . '`';
				$values[]	= ':' . $name;
			}
			
			$this->query(sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, implode(', ', $names), implode(', ', $values)), $parameters);
			return $this->lastInsertId();
		}
	}
?>