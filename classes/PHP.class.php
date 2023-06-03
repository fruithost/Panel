<?php
	namespace fruithost;
	
	class PHP {
		private $path		= null;
		private $content	= null;
		private $data		= [];
		
		public function setPath($path) {
			$this->path = $path;
		}
		
		public function execute() {
			$this->content = explode("\n", shell_exec(sprintf('echo "<?php phpinfo(); ?>" | (php --php-ini %s)', $this->path)));
		}
		
		public function parse() {
			foreach($this->content as $data) {
				$row = explode("=>", trim($data));
				
				if(isset($row[1])) {
					$this->data[trim($row[0])] = $row[1];
				}
			}
		}
		
		private function getByKey($key)
		{
			return $this->data[$key];
		}

		public function getSystem()
		{
			return $this->getByKey("System");
		}

		public function getBuildDate()
		{
			return $this->getByKey("Build Date");
		}

		public function getConfigureCommand()
		{
			$result = explode("' '", $this->getByKey("Configure Command"));

			$lastIdx = (count($result) - 1);
			unset($result[$lastIdx]);

			$lastIdx = (count($result) - 1);
			unset($result[$lastIdx]);

			unset($result[0]);

			return $result;
		}

		public function getServerApi()
		{
			return $this->getByKey("Server API");
		}

		public function getVirtualDirectorySupport()
		{
			return $this->getByKey("Virtual Directory Support");
		}

		public function getConfigurationFilePath()
		{
			return $this->getByKey("Configuration File (php.ini) Path");
		}

		public function getLoadedConfigurationFile()
		{
			return $this->getByKey("Loaded Configuration File");
		}

		public function getAdditionalIni()
		{
			return $this->getByKey("Scan this dir for additional .ini files");
		}

		public function getAdditionalIniParsed()
		{
			return $this->getByKey("Additional .ini files parsed");
		}

		public function getPhpApi()
		{
			return $this->getByKey("PHP API");
		}

		public function getPhpExtension()
		{
			return $this->getByKey("PHP Extension");
		}

		public function getZendExtension()
		{
			return $this->getByKey("Zend Extension");
		}

		public function getZendExtensionBuild()
		{
			return $this->getByKey("Zend Extension Build");
		}

		public function getPhpExtensionBuild()
		{
			return $this->getByKey("PHP Extension Build");
		}

		public function getDebugBuild()
		{
			return $this->getByKey("Debug Build");
		}

		public function getThreadSafety()
		{
			return $this->getByKey("Thread Safety");
		}

		public function getZendSignalHandling()
		{
			return $this->getByKey("Zend Signal Handling");
		}

		public function getZendMemoryManager()
		{
			return $this->getByKey("Zend Memory Manager");
		}

		public function getZendMultibyteSupport()
		{
			return $this->getByKey("Zend Multibyte Support");
		}

		public function getIPv6Support()
		{
			return $this->getByKey("IPv6 Support");
		}

		public function getDTraceSupport()
		{
			return $this->getByKey("DTrace Support");
		}

		public function getRegisteredPhpStreams()
		{
			return $this->getByKey("Registered PHP Streams");
		}

		public function getRegisteredStreamSocketTransports()
		{
			return $this->getByKey("Registered Stream Socket Transports");
		}

		public function getRegisteredStreamFilters()
		{
			return $this->getByKey("Registered Stream Filters");
		}
		
		public function getInfo() {			
			$this->execute();
			$this->parse();
			
			$result = [
				"System"						=> $this->getSystem(),
				"BuildDate"						=> $this->getBuildDate(),
				"CMD" 							=> $this->getConfigureCommand(),
				"ServerAPI"						=> $this->getServerApi(),
				"VirtualDirectorySupport"		=> $this->getVirtualDirectorySupport(),
				"ConfigurationFilePath"			=> $this->getConfigurationFilePath(),
				"LoadedConfigurationFile"		=> $this->getLoadedConfigurationFile(),
				"AdditionalIni"					=> $this->getAdditionalIni(),
				"AdditionalIniParsed"			=> $this->getAdditionalIniParsed(),
				"API"							=> $this->getPhpApi(),
				"Extension"						=> $this->getPhpExtension(),
				"ZEND"							=> $this->getZendExtension(),
				"ZENDBuild"						=> $this->getZendExtensionBuild(),
				"DebugBuild"					=> $this->getDebugBuild(),
				"ThreadSafety"					=> $this->getThreadSafety(),
				"ZendSignalHandling"			=> $this->getZendSignalHandling(),
				"ZendMemoryManager"				=> $this->getZendMemoryManager(),
				"MultibyteSupport"				=> $this->getZendMultibyteSupport(),
				"IPv6Support"					=> $this->getIPv6Support(),
				"DTraceSupport"					=> $this->getDTraceSupport(),
				"RegisteredPhpStreams"			=> $this->getRegisteredPhpStreams(),
				"RegisteredStreamSocketTransports"			=> $this->getRegisteredStreamSocketTransports(),
				"RegisteredStreamFilters"			=> $this->getRegisteredStreamFilters(),
			];
			
			return $result;
		}
	}
?>