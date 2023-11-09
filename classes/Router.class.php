<?php
	namespace fruithost;
	
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
		
		public function routeExists(string $name) : bool {
			foreach($this->routes AS $route) {
				if(preg_match('/(\(|\)|\[|\])/Uis', $route->getPath()) && preg_match('#' . $route->getPath() .  '#Uis', $name)) {
					return true;
				}
			}
			
			return array_key_exists($name, $this->routes);
		}
		
		public function executeRoute(string $name) {
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
		
		public function redirectTo(string $redirect) {
			$this->redirect = $redirect;
		}
		
		public function run(bool $ajax = false) {
			// @ToDo is it secure?
			$uri	= explode('/',	($ajax ? preg_replace('#(http|https)://([^/]+)#', '', $_SERVER['HTTP_REFERER']) : $_SERVER['REQUEST_URI']));
			$name	= explode('/',	$_SERVER['SCRIPT_NAME']);
			
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
			if($ajax == true) {
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