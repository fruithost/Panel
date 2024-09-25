<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

	namespace fruithost\Network;
	
	use fruithost\System\Core;

    class Router {
		private array $routes		= [];
		private ?string $redirect	= null;
		private ?string $current	= null;
		private Core $core;
		
		public function __construct(Core $core) {
			$this->core = $core;
		}
		
		public function addRoute(string $name, callable $callback) : Route | null {
			$route					= new Route();
			$this->routes[$name]	= $route;
			$route->setPath($name);
			$route->setCallback($callback);
			
			return $route;
		}

		public function cleanUp() : void {
			$this->routes = [];
		}
		
		public function routeExists(string $name) : bool {
			foreach($this->routes AS $route) {
				if(preg_match('/(\(|\)|\[|\])/Uis', $route->getPath()) && preg_match('#' . $route->getPath() .  '#Uis', $name)) {
					return true;
				}
			}
			
			return array_key_exists($name, $this->routes);
		}
		
		public function executeRoute(string $name) : void {
			$this->current	= $name;
			
			foreach($this->routes AS $route) {
				if(preg_match('/(\(|\)|\[|\])/Uis', $route->getPath())) {
					if(preg_match('#' . $route->getPath() .  '#Uis', $name, $matches)) {
						array_shift($matches);
						call_user_func_array($route->getCallback(), $matches);
						return;
					}
				}
			}
			
			$route			= $this->routes[$name];
			$callback		= $route->getCallback();
			$callback();
		}
		
		public function getCurrent() : ?string {
			return $this->current;
		}
		
		public function is(string $name) : bool {
			return (strtolower($this->getCurrent()) === strtolower($name));
		}
		
		public function startsWith(string $name) : bool {
			return (str_starts_with(strtolower($this->getCurrent()), strtolower($name)));
		}
		
		public function redirectTo(string $redirect) : void {
			$this->redirect = $redirect;
		}
		
		public function run(bool $ajax = false) : void {
			// @ToDo is it secure?
			if($ajax) {
				$data = preg_replace('#(http|https)://([^/]+)#', '', $_SERVER['HTTP_REFERER']);
			} else if(isset($_SERVER['REQUEST_URI'])) {
				$data = $_SERVER['REQUEST_URI'];
			} else {
				$data = null;
			}
			
			if(empty($data)) {
				return;
			}
		
			$uri	= explode('/',	$data);
			$name	= explode('/',	(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/'));
			
			for($i = 0; $i < sizeof($name); $i++) {
				if($uri[$i] === $name[$i]) {
					unset($uri[$i]);
				}
			}
		
			$command	= array_values($uri);
			$route		= '';
			
			foreach($command AS $index => $directory) {
				$route .= '/' . $directory;
			}
			
			// Remove "GET"
			if(str_contains($route, "?")) {
				$split	= explode('?', $route);
				$route	= $split[0];
			}
			
			// Remove "ajax"
			if($ajax) {
				$route	= str_replace('/ajax', '', $route);
			}
			
			Response::header();
			
			if($this->routeExists($route)) {
				$this->executeRoute($route);
			} else if(empty($this->redirect)) {
                $this->core->getTemplate()->display('error/404');
            } else {
                Response::redirect($this->redirect);
            }
		}
	}
?>