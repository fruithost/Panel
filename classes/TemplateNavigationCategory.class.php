<?php
	namespace fruithost;
	
	class TemplateNavigationCategory {
		private $navigation		= null;
		private $id				= null;
		private $label			= null;
		
		public function __construct($navigation, $id, $label) {
			$this->navigation	= $navigation;
			$this->id			= $id;
			$this->label		= $label;
		}
		
		public function getID() {
			return $this->id;
		}
		
		public function getLabel() {
			return $this->label;
		}
		
		public function isEmpty() {
			return (count($this->getEntries()) === 0);
		}
		
		public function getEntries() {
			$hardcoded = [];
			
			if($this->id === 'account') {
				$hardcoded = [
					(object) [
						'name'		=> 'Account',
						'icon'		=> '<i class="material-icons">account_circle</i>',
						'order'		=> 1,
						'url'		=> '/account',
						'active'	=> $this->navigation->getCore()->getRouter()->is('/account')
					],
					(object) [
						'name'		=> 'Settings',
						'icon'		=> '<i class="material-icons">settings</i>',
						'order'		=> 2,
						'url'		=> '/settings',
						'active'	=> $this->navigation->getCore()->getRouter()->is('/settings')
					],
					(object) [
						'name'		=> 'Logout',
						'icon'		=> '<i class="material-icons">power_settings_new</i>',
						'order'		=> 99999,
						'url'		=> '/logout',
						'active'	=> $this->navigation->getCore()->getRouter()->is('/logout')
					]
				];
			}
			
			$results = $this->navigation->getCore()->getHooks()->applyFilter(strtoupper(sprintf('%s_MANAGEMENT', $this->id)), $hardcoded);
			
			usort($results, function ($a, $b) {
				if($a->{'order'} === $b->{'order'}) {
					return 0;
				}
				
				return ($a->{'order'} < $b->{'order'}) ? -1 : 1;
			});

			usort($results, function($a, $b) {
				if(empty($a->order)) {
					$a->order = 0;
				}
				
				if(empty($b->order)) {
					$b->order = 999;
				}
				
				return $a->order > $b->order;
			});
			
			return $results;
		}
	}
?>