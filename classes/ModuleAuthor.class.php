<?php
	namespace fruithost;
	
	class ModuleAuthor {
		private $name	= null;
		private $email	= null;
		private $url	= null;
		
		public function __construct($object) {
			if(!empty($object->name)) {
				$this->name = $object->name;
			}
			
			if(!empty($object->email)) {
				$this->email = $object->email;
			}
			
			if(!empty($object->url)) {
				$this->url = $object->url;
			}
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getMail() {
			return $this->email;
		}
		
		public function getWebsite() {
			return $this->url;
		}
	}
?>