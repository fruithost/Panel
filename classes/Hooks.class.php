<?php
	namespace fruithost;
	
	use fruithost\Auth;
	
	class Hooks {
		protected $filters			= [];
		protected $merged_filters	= [];
		protected $actions			= [];
		protected $current_filter	= [];
		
		protected function createID($method) {
			if(is_string($method)) {
				return $method;
			}
			
			if(is_object($method)) {
				$method = [ $method, '' ];
			} else {
				$method = (array) $method;
			}
			
			if(is_object($method[0])) {
				return spl_object_hash($method[0]) . $method[1];
			}
			
			if(is_string($method[0])) {
				return $method[0] . $method[1];
			}
			
			return false;
		}
		
		public function callAllHook($args) {
			reset($this->filters['all']);
			
			do {
				foreach(current($this->filters['all']) AS $entry) {
					if(!empty($entry['method']) && (isset($entry['logged_in']) && ($entry['logged_in'] == true && Auth::isLoggedIn() || $entry['logged_in'] == false))) {
						call_user_func_array($entry['method'], $args);
					}
				}
			} while(next($this->filters['all']) !== false);
		}

		/* Filter */
		public function addFilter($name, $method, $priority = 50, $logged_in = true) {
			$this->filters[$name][$priority][$this->createID($method)] = [
				'method'    	=> $method,
				'logged_in'		=> $logged_in
			];
			
			unset($this->merged_filters[$name]);
			return true;
		}
		
		public function removeFilter($name, $method, $priority = 50) {
			$method = $this->createID($method);
			
			if(!isset($this->filters[$name][$priority][$method])) {
				return false;
			}
			
			unset($this->filters[$name][$priority][$method]);
			
			if(empty($this->filters[$name][$priority])) {
				unset($this->filters[$name][$priority]);
			}
			
			unset($this->merged_filters[$name]);
			return true;
		}
		
		public function hasFilter($name, $method = false) {
			$exists = isset($this->filters[$name]);
			
			if($method === false || !$exists) {
				return $exists;
			}
			
			if(!($id = $this->createID($method))) {
				return false;
			}
			
			foreach(array_keys($this->filters[$name]) AS $priority) {
				if(isset($this->filters[$name][$priority][$id])) {
					return $priority;
				}
			}
			
			return false;
		}
		
		public function applyFilter($name, $arguments) {
			$args	= [];
			$value	= $arguments;
			
			if(isset($this->filters['all'])) {
				$this->current_filter[]	= $name;
				$args					= func_get_args();
				
				$this->callAllHook($args);
			}
			
			if(!isset($this->filters[$name])) {
				if(isset($this->filters['all'])) {
					array_pop($this->current_filter);
				}
				
				return $value;
			}
			
			if(!isset($this->filters['all'])) {
				$this->current_filter[] = $name;
			}
			
			if(!isset($this->merged_filters[$name])) {
				ksort($this->filters[$name]);
				$this->merged_filters[$name] = true;
			}
			
			reset($this->filters[$name]);
			
			if(empty($args)) {
				$args = func_get_args();
			}
			
			array_shift($args);
			
			do {
				foreach(current($this->filters[$name]) AS $entry) {
					if(!empty($entry['method']) && (isset($entry['logged_in']) && ($entry['logged_in'] == true && Auth::isLoggedIn() || $entry['logged_in'] == false))) {
						$args[0]	= $value;
						$value		= call_user_func_array($entry['method'], $args);
					}
				}
			} while(next($this->filters[$name]) !== false);
			
			array_pop($this->current_filter);
			
			return $value;
		}
		
		/* Actions */
		public function addAction($name, $method, $priority = 50, $logged_in = true) {
			return $this->addFilter($name, $method, $priority, $logged_in);
		}
		
		public function removeAction($name, $method, $priority = 50) {
			return $this->removeFilter($name, $method, $priority);
		}
		
		public function hasAction($name, $method = false) {
			return $this->hasFilter($name, $method);
		}
		
		public function runAction($name, $arguments = null) {
			if(!is_array($this->actions)) {
				$this->actions = [];
			}
			
			if(isset($this->actions[$name])) {
				++$this->actions[$name];
			} else {
				$this->actions[$name] = 1;
			}
			
			if(isset($this->filters['all'])) {
				$this->current_filter[]	= $name;
				$all_args				= func_get_args();
				
				$this->callAllHook($all_args);
			}
			
			if(!isset($this->filters[$name])) {
				if(isset($this->filters['all'])) {
					array_pop($this->current_filter);
				}
				
				return false;
			}
			
			if(!isset($this->filters['all'])) {
				$this->current_filter[] = $name;
			}
			
			$args = [];
			
			if(is_array($arguments) && isset($arguments[0]) && is_object($arguments[0]) && count($arguments) == 1) {
				$args[] =& $arguments[0];
			} else {
				$args[] = $arguments;
			}
			
			for($a = 2; $a < func_num_args(); $a++) {
				$args[] = func_get_arg($a);
			}
			
			if(!isset($this->merged_filters[$name])) {
				ksort($this->filters[$name]);
				$this->merged_filters[$name] = true;
			}
			
			reset($this->filters[$name]);
			
			do {
				foreach(current($this->filters[$name]) AS $entry) {
					if(!empty($entry['method']) && (isset($entry['logged_in']) && ($entry['logged_in'] == true && Auth::isLoggedIn() || $entry['logged_in'] == false))) {
						call_user_func_array($entry['method'], $args);
					}
				}
			} while(next($this->filters[$name]) !== false);
			
			array_pop($this->current_filter);
			
			return true;
		}
	}
?>