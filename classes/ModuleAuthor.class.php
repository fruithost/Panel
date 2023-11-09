<?php
	namespace fruithost;
	
	class ModuleAuthor {
		private ?string $name	= null;
		private ?string $email	= null;
		private ?string $url	= null;
		
		public function __construct(object $object) {
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
		
		public function getName() : ?string {
			return $this->name;
		}
		
		public function getMail() : ?string {
			return $this->email;
		}
		
		public function getWebsite() : ?string {
			return $this->url;
		}
	}
?>