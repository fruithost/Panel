<?php
    /**
	 * fruithost | OpenSource Hosting
	 *
	 * @author  Adrian Preuß
	 * @version 1.0.0
	 * @license MIT
	 */

	namespace fruithost\Installer;

	class Installer {
		/* Repositorys */
		public static function getRepositorys(?string $url = null) : array {
			return Repository::getInstance()->getRepositorys($url);
		}
		
		public static function getRepository(int $id) {
			return Repository::getInstance()->getRepository($id);
		}
		
		public static function createRepository(string $url) : void {
			Repository::getInstance()->createRepository($url);
		}
		
		public static function updateRepository(string $id, string $timestamp) : void {
			Repository::getInstance()->updateRepository($id, $timestamp);
		}
		
		public static function deleteRepository(int $id) : void {
			Repository::getInstance()->deleteRepository($id);
		}
		
		public static function getFile($repository, $file) : string | int | null {
			return Repository::getInstance()->getFile($repository, $file);
		}
		
		public static function getRepositorysByID(array $ids) : array {
			return Repository::getInstance()->getRepositorysByID($ids);
		}
	}
?>