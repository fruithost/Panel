<?php
	namespace fruithost;
	
	class Database {
		public static function file(string $file, callable $callback) {
			return DatabaseFactory::getInstance()->file($file, $callback);
		}
		
		public static function getError(?object $object = null) {
			return DatabaseFactory::getInstance()->getError($object);
		}
		
		public static function query(string $query, array $parameters = []) : \PDOStatement | false {
			return DatabaseFactory::getInstance()->query($query, null, $parameters);
		}
		
		public static function single(string $query, array $parameters = []) : mixed {
			return DatabaseFactory::getInstance()->single($query, $parameters);
		}
		
		public static function count(string $query, array $parameters = []) : int {
			return DatabaseFactory::getInstance()->count($query, $parameters);
		}
		
		public static function exists(string $query, array $parameters = []) : bool {
			return DatabaseFactory::getInstance()->count($query, $parameters) > 0;
		}
		
		public static function fetch(string $query, array $parameters = []) : mixed {
			return DatabaseFactory::getInstance()->fetch($query, $parameters);
		}
		
		public static function update(string $table, string | array $where, array $parameters = []) {
			return DatabaseFactory::getInstance()->update($table, $where, $parameters);
		}
		
		public static function insert(string $table, array $parameters = []) : int {
			return DatabaseFactory::getInstance()->insert($table, $parameters);
		}
		
		public static function delete(string $table, array $parameters = []) : \PDOStatement | false {
			return DatabaseFactory::getInstance()->delete($table, $parameters);
		}
		
		public static function tableExists(string $table) : bool {
			return DatabaseFactory::getInstance()->tableExists($table);
		}
		
		public static function deleteWhereNot(string $table, array $delete_not = [], array $parameters = []) {
			return DatabaseFactory::getInstance()->deleteWhereNot($table, $delete_not, $parameters);
		}
	}
?>
