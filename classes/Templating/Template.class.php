<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Templating;

    use fruithost\Accounting\Auth;
    use fruithost\System\Core;
    use fruithost\System\CoreAdmin;
    use fruithost\Localization\I18N;
    use fruithost\Network\Request;
    use fruithost\Network\Response;
	use fruithost\UI\Modal;

    class Template extends TemplateDefaults {
		private Core $core;
		private ?string $theme					= null;
		private array $assigns					= [];
		private ?TemplateFiles $files			= null;
		private ?TemplateNavigation $navigation	= null;

		private ?string $path = null;

		public function __construct(Core $core) {
			$this->path			= dirname(PATH);
			$this->core			= $core;
			$this->files		= new TemplateFiles();
			$this->navigation	= new TemplateNavigation($this->core);
			$this->theme		= $this->core->getHooks()->applyFilter('theme_name', $this->getDefaultTheme());
			$this->assigns		= [
				'project_name' 		=> $this->core->getSettings('PROJECT_NAME', 'fruithost'),
				'project_copyright' => $this->core->getSettings('PROJECT_COPYRIGHT', true)
			];

			// gzip, deflate, br, zstd
			if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				@ob_start('ob_gzhandler');
				Response::addHeader('Content-Encoding', 'gzip');
			}
			
			$this->core->getHooks()->addAction('html_head', [ $this, 'head_robots' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'head_description' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'head_scripts' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'theme_color' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'modal_showing' ], 10, false);
			$this->core->getHooks()->addAction('html_head', [ $this, 'favicon' ], 10, false);
			$this->core->getHooks()->addAction('html_foot', [ $this, 'foot_modals' ], 10, false);
			$this->core->getHooks()->addAction('html_foot', [ $this, 'foot_scripts' ], 10, false);
			
			$this->files->addStylesheet('bootstrap', $this->url('css/bootstrap/bootstrap.min.css'), '5.3.2');
            $this->files->addStylesheet('bootstrap-icons', $this->url('fonts/bootstrap-icons/bootstrap-icons.css'), '1.11.1', [ 'bootstrap' ]);
            $this->files->addStylesheet('cascadia-mono', $this->url('fonts/cascadia-mono/cascadia-mono.css'), '2111.01', [ 'bootstrap' ]);
            $this->files->addJavascript('bootstrap', $this->url('js/bootstrap/bootstrap.bundle.min.js'), '5.3.2', [], TemplateFiles::FOOTER);

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
			$this->assign('navigation',   $this->navigation);
			
			$this->core->getHooks()->runAction('template_init', $this);
		}

        public function getDefaultTheme() {
            $theme = 'default';

            if(isset($_GET['theme'])) {
                $theme = $_GET['theme'];
            }

            // @ToDo Check if Theme-Directory exists
            if($theme != 'default') {

            }
            
            return $theme;
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

        public function resolveThemePath($file, $handler = false) : string {
            $entry  = 'default';

            if($handler) {
                $entry  = 'handler';
                $theme	= sprintf('%1$s%2$sthemes%2$shandler%2$s%4$s%2$s%3$s.php', $this->path, DS, $file, $this->theme);
            } else {
                $theme	= sprintf('%1$s%2$sthemes%2$s%4$s%2$s%3$s.php', $this->path, DS, $file, $this->theme);
            }
			
			if(!file_exists($theme)) {
                $theme	= sprintf('%1$s%2$s%4$s%2$s%3$s.php', $this->path, DS, $file, $entry);
            }

            if(!file_exists($theme)) {
                $theme	= sprintf('%1$s%4$s%2$s%3$s.php', PATH, DS, $file, $entry);
            }
			
            return $theme;
        }

		public function setPath($path) {
			$this->path = $path;
		}

		public function getPath() {
			return $this->path;
		}

		public function getFiles() : TemplateFiles {
			return $this->files;
		}

        public function getTheme() : ?string {
            return $this->theme;
        }
		
		public function addModal(Modal $modal) : void {
			$this->core->getHooks()->addFilter('modals', function($modals) use($modal) {
				$modals[] = $modal;
				return $modals;
			}, 10);
		}
		
		public function assign(string $name, mixed $value) : void {
			$this->assigns[$name] = $value;
		}
		
		public function display(string $file, array $arguments = [], bool $basedir = true, bool $once = true) : ?bool {
			$template	= $this;
			$functions	= $this->resolveThemePath('functions');
			
            if(file_exists($functions)) {
               if($once) {
                    require_once($functions);
                } else {
                    require($functions);
                }
            }

			$this->core->getHooks()->runAction('html_render');
			
			foreach($arguments AS $name => $value) {
				$this->assigns[$name] = $value;
			}
			
			if($basedir) {
                $path   = $this->resolveThemePath($file);
			} else {
				$path	= $file;
			}

			$handler	= $this->resolveThemePath($file, true);

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
			}

			return false;
		}
		
		public function header() : void {
			$template	= $this;
            $path       = $this->resolveThemePath('header');

			foreach($this->assigns AS $name => $value) {
				${$name} = $value;
			}

			if(file_exists($path)) {
				@require_once($path);
			}
		}
		
		public function footer() : void {
			$template	= $this;
            $path       = $this->resolveThemePath('footer');
			
			foreach($this->assigns AS $name => $value) {
				${$name} = $value;
			}
			
			if(file_exists($path)) {
				require_once($path);
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
			
			if($parameters == null) {
				$parameters = [];
			}
			
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
				$scheme = 'https';
			}
			
			if($path === true || $path === null) {
				$path = $_SERVER['REQUEST_URI'];
			}
			
			if(substr($path, 0, 1) === '/') {
				$path = substr($path, 1);
			}

			if(strpos($path, '?')) {
				$parts	= explode('?', $path);
				$path	= $parts[0];
				
				parse_str($parts[1], $arguments);
				
				$parameters = array_merge($arguments, $_GET, $parameters);
			}
			
			foreach([
				'lang',
				'theme'
			] AS $include) {
				if(!array_key_exists($include, $parameters) && Request::has($include)) {
					$parameters[$include] = Request::get($include);
				}
			}
			
			if(count($parameters) > 0) {
				$path .= '?' . http_build_query($parameters);
			}
			
			return sprintf('%s://%s/%s', $scheme, $_SERVER['HTTP_HOST'], $path);
		}
	}
?>