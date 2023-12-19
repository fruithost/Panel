<?php
	namespace fruithost\Templating;

    use fruithost\Accounting\Auth;
    use fruithost\System\Core;
    use fruithost\System\CoreAdmin;
    use fruithost\Localization\I18N;
    use fruithost\Network\Request;

    class Template extends TemplateDefaults {
		private Core $core;
		private ?string $theme					= null;
		private array $assigns					= [];
		private ?TemplateFiles $files			= null;
		private ?TemplateNavigation $navigation	= null;
		
		public function __construct(Core $core) {
			$this->core			= $core;
			$this->files		= new TemplateFiles();
			$this->navigation	= new TemplateNavigation($this->core);
			$this->theme		= $this->core->getHooks()->applyFilter('theme_name', 'default');
			$this->assigns		= [
				'project_name' 		=> $this->core->getSettings('PROJECT_NAME', 'fruithost'),
				'project_copyright' => $this->core->getSettings('PROJECT_COPYRIGHT', true)
			];
			
			if(defined('DEBUG') && DEBUG) {
				ob_start();
			} else if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				ob_start('ob_gzhandler');
			}
			
			$this->core->getHooks()->addAction('html_head', [ $this, 'head_robots' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'head_scripts' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'theme_color' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'favicon' ], 10, false);
			$this->core->getHooks()->addAction('html_foot', [ $this, 'foot_modals' ], 10, false);
			$this->core->getHooks()->addAction('html_foot', [ $this, 'foot_scripts' ], 10, false);
			
			$this->files->addStylesheet('bootstrap', $this->url('css/bootstrap/bootstrap.min.css'), '5.3.2');
            $this->files->addStylesheet('bootstrap-icons', $this->url('fonts/bootstrap-icons/bootstrap-icons.css'), '1.11.1', [ 'bootstrap' ]);
            $this->files->addStylesheet('cascadia-mono', $this->url('fonts/cascadia-mono/cascadia-mono.css'), '2111.01', [ 'bootstrap' ]);
            $this->files->addStylesheet('global', $this->url('css/global.css'), '1.0.0', [ 'bootstrap' ]);
			$this->files->addJavascript('bootstrap', $this->url('js/bootstrap/bootstrap.bundle.min.js'), '5.3.2', [], TemplateFiles::FOOTER);
			$this->files->addJavascript('global', $this->url('js/global.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
			$this->files->addJavascript('ajax', $this->url('js/ajax.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
			
			$this->navigation->addCategory('account', I18N::get('Account'));
			$this->navigation->addCategory('database', I18N::get('Databases'));
			$this->navigation->addCategory('domain', I18N::get('Domains'));
			$this->navigation->addCategory('mail', I18N::get('E-Mails'));
			$this->navigation->addCategory('ftp', I18N::get('FTP'));
			$this->navigation->addCategory('hosting', I18N::get('Hosting'));
			$this->navigation->addCategory('extended', I18N::get('Extended'));
			$this->navigation->addCategory('support', I18N::get('Support'));
			$this->navigation->addCategory('admin', I18N::get('Administration'));
			$this->navigation->addCategory('server', I18N::get('Server'));
			
			$this->assign('topbar',		$this->navigation->getCategory('account'));
			$this->assign('navigation', $this->navigation);
		}
		
		public function getCore() : Core {
			return $this->core;
		}
		
		public function getAdminCore() : CoreAdmin {
			return $this->core->getAdminCore();
		}
		
		public function isAssigned(string $name) : bool {
			return array_key_exists($name, $this->assigns);
		}
		
		public function getAssigns() : array {
			return $this->assigns;
		}
		
		public function getFiles() : TemplateFiles {
			return $this->files;
		}

        public function getTheme() : ?string {
            return $this->theme;
        }
		
		public function assign(string $name, mixed $value) : void {
			$this->assigns[$name] = $value;
		}
		
		public function display(string $file, array $arguments = [], bool $basedir = true, bool $once = true) : ?bool {
			$template	= $this;
			
			foreach($arguments AS $name => $value) {
				$this->assigns[$name] = $value;
			}
			
			if($basedir) {
				$path		= sprintf('%1$s%2$sthemes%2$s%4$s%2$s%3$s.php', dirname(PATH), DS, $file, $this->theme);
			} else {
				$path		= $file;
			}
			
			$handler	= sprintf('%1$shandler%2$s%3$s.php', PATH, DS, $file);
			
			foreach($this->assigns AS $name => $value) {
				${$name} = $value;
			}
			
			if(file_exists($handler)) {
				if($once) {
					require_once($handler);
				} else {
					require($handler);
				}
				
				foreach($this->assigns AS $name => $value) {
					${$name} = $value;
				}
			}
			
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				return null;
			}
			
			if(file_exists($path) && is_readable($path)) {
				if($once) {
					require_once($path);
				} else {
					require($path);
				}
					
				return true;
			} else {
				if(!Auth::isLoggedIn()) {
					$this->getFiles()->addStylesheet('login', $this->url('css/login.css'), '2.0.0', [ 'bootstrap' ]);
                    $this->getFiles()->addJavascript('login', $this->url('js/login.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
                } else {
					$this->getFiles()->addStylesheet('style', $this->url('css/style.css'), '2.0.0', [ 'bootstrap' ]);
					$this->getFiles()->addJavascript('ui', $this->url('js/ui.js'), '2.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
				}
				
				$path = sprintf('%1$s%2$sdefault%2$s%3$s.php', PATH, DS, $file);
				
				if(file_exists($path) && is_readable($path)) {
					if($once) {
						require_once($path);
					} else {
						require($path);
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		public function header() : void {
			$template	= $this;
			$path		= sprintf('%1$s%2$sthemes%2$s%4$s%2$s%3$s.php', dirname(PATH), DS, 'header', $this->theme);
			
			foreach($this->assigns AS $name => $value) {
				${$name} = $value;
			}


			if(file_exists($path)) {
				@require_once($path);
			} else {
				$path = sprintf('%1$s%2$sdefault%2$s%3$s.php', PATH, DS, 'header');
				
				if(file_exists($path)) {
					@require_once($path);
				}
			}
		}
		
		public function footer() : void {
			$template	= $this;
			$path		= sprintf('%1$s%2$sthemes%2$s%4$s%2$s%3$s.php', dirname(PATH), DS, 'footer', $this->theme);
			
			foreach($this->assigns AS $name => $value) {
				${$name} = $value;
			}
			
			if(file_exists($path)) {
				require_once($path);
			} else {
				$path = sprintf('%1$s%2$sdefault%2$s%3$s.php', PATH, DS, 'footer');
				
				if(file_exists($path)) {
					@require_once($path);
				}
			}
		}
		
		public function getLanguage(bool $short = false) : string {
			$language	= $this->getCore()->getSettings('LANGUAGE', 'en_US');
			$language	= Auth::getSettings('LANGUAGE', NULL, $language);
			
			if($short) {
				return explode('_', $language)[0];
			}
			
			return $language;
		}
		
		public function head() : void {
			$this->core->getHooks()->runAction('html_head');
		}
		
		public function foot() : void {
			$this->core->getHooks()->runAction('html_foot');
		}
		
		public function url(bool | string $path = null, array $parameters = null) : string {
			$scheme = 'http';
			
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
				$scheme = 'https';
			}
			
			if($path === true || $path === null) {
				$path = $_SERVER['REQUEST_URI'];
			}
			
			if(substr($path, 0, 1) === '/') {
				$path = substr($path, 1);
			}
			
			if(!empty($parameters)) {
				if(strpos($path, '?')) {
					$path	= explode('?', $path);
					$path	= $path[0];
				}
				
				$path .= '?' . http_build_query(array_merge($_GET, $parameters));
			} else if(Request::has('lang') && !strpos($path, '?')) {
				$path .= '?lang=' . Request::get('lang');
			}
			
			return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $path;
		}
	}
?>