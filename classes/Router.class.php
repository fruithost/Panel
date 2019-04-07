<?php
	namespace fruithost;
	
	class Router {
		private $routes		= [];
		private $redirect	= NULL;
		private $current	= NULL;
		private $core;
		
		public function __construct($core) {
			$this->core = $core;
		}
		
		public function addRoute($name, $callback) {
			$route					= new Route();
			$this->routes[$name]	= $route;
			$route->setPath($name);
			$route->setCallback($callback);
			
			return $route;
		}
		
		public function routeExists($name) {
			foreach($this->routes AS $route) {
				if(preg_match('/(\(|\)|\[|\])/Uis', $route->getPath()) && preg_match('#' . $route->getPath() .  '#Uis', $name)) {
					return true;
				}
			}
			
			return array_key_exists($name, $this->routes);
		}
		
		public function executeRoute($name) {
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
		
		public function getCurrent() {
			return $this->current;
		}
		
		public function is($name) {
			return (strtolower($this->getCurrent()) === strtolower($name));
		}
		
		public function redirectTo($redirect) {
			$this->redirect = $redirect;
		}
		
		public function run() {
			$uri	= explode('/',	$_SERVER['REQUEST_URI']);
			$name	= explode('/',	$_SERVER['SCRIPT_NAME']);
			
			for($i = 0; $i < sizeof($name); $i++) {
				if($uri[$i] == $name[$i]) {
					unset($uri[$i]);
				}
			}
			
			$command	= array_values($uri);
			$route		= '';
			
			foreach($command AS $index => $directory) {
				$route .= '/' . $directory;
			}
			
			// Remove "GET"
			if(preg_match('/\?/Uis', $route)) {
				$split	= explode('?', $route);
				$route	= $split[0];
			}
			
			Response::header();
			
			if($this->routeExists($route)) {
				$this->executeRoute($route);
			} else {
				if(empty($this->redirect)) {
					$this->core->getTemplate()->display('error/404');
				} else {
					Response::redirect($this->redirect);
				}
			}
		}
	}
?>