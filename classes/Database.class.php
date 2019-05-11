<?php
	namespace fruithost;
	
	class Database {
		public static function query($query, $parameters = []) {
			return DatabaseFactory::getInstance()->query($query, $parameters);
		}
		
		public static function single($query, $parameters = []) {
			return DatabaseFactory::getInstance()->single($query, $parameters);
		}
		
		public static function count($query, $parameters = []) {
			return DatabaseFactory::getInstance()->count($query, $parameters);
		}
		
		public static function exists($query, $parameters = []) {
			return DatabaseFactory::getInstance()->count($query, $parameters) > 0;
		}
		
		public static function fetch($query, $parameters = []) {
			return DatabaseFactory::getInstance()->fetch($query, $parameters);
		}
		
		public static function update($table, $where, $parameters = []) {
			return DatabaseFactory::getInstance()->update($table, $where, $parameters);
		}
		
		public static function insert($table, $parameters = []) {
			return DatabaseFactory::getInstance()->insert($table, $parameters);
		}
		
		public static function delete($table, $parameters = []) {
			return DatabaseFactory::getInstance()->delete($table, $parameters);
		}
		
		public static function deleteWhereNot($table, $delete_not = [], $parameters = []) {
			return DatabaseFactory::getInstance()->deleteWhereNot($table, $delete_not, $parameters);
		}
	}
?>