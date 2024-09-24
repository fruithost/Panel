<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author  Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Modules;
	
	class ModuleError {
		private ?string $message = null;
		private ?string $code = null;
		private ?string $file = null;
		private ?string $line = null;
		private ?string $module = null;
		private array $trace = [];
		
		public function __construct($module, $exception) {
			$this->module = $module;
			$this->message = $exception->getMessage();
			$this->code = $exception->getCode();
			$this->file = $exception->getFile();
			$this->line = $exception->getLine();
			$this->trace = $exception->getTrace();
		}
		
		public function getModule() : ?string {
			return $this->module;
		}
		
		public function getMessage() : ?string {
			return $this->message;
		}
		
		public function getCode() : ?string {
			return $this->code;
		}
		
		public function getFile() : ?string {
			return $this->file;
		}
		
		public function getLine() : ?string {
			return $this->line;
		}
		
		public function getTrace() : array {
			return $this->trace;
		}
	}
?>