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
			
			switch($this->id) {
				case 'account':
					$hardcoded = [
						(object) [
							'name'		=> 'Account',
							'icon'		=> '<i class="material-icons">account_circle</i>',
							'order'		=> 1,
							'url'		=> '/account',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/account')
						],
						(object) [
							'name'		=> 'Settings',
							'icon'		=> '<i class="material-icons">settings</i>',
							'order'		=> 2,
							'url'		=> '/settings',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/settings')
						],
						(object) [
							'name'		=> 'Logout',
							'icon'		=> '<i class="material-icons">power_settings_new</i>',
							'order'		=> 99999,
							'url'		=> '/logout',
							'active'	=> $this->navigation->getCore()->getRouter()->is('/logout')
						]
					];
				break;
				case 'admin':
					$hardcoded = [];
					
					if(Auth::hasPermission('*')) {
						$hardcoded[] = (object) [
							'name'		=> 'Overview',
							'icon'		=> '<i class="material-icons">apps</i>',
							'order'		=> 1,
							'url'		=> '/admin',
							'active'	=> $this->navigation->getCore()->getRouter()->is('/admin')
						];
					}
					
					if(Auth::hasPermission('USERS::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> 'Users',
							'icon'		=> '<i class="material-icons">supervised_user_circle</i>',
							'order'		=> 1,
							'url'		=> '/admin/users',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/users')
						];
					}
					
					if(Auth::hasPermission('THEMES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> 'Themes',
							'icon'		=> '<i class="material-icons">palette</i>',
							'order'		=> 2,
							'url'		=> '/admin/themes',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/themes')
						];
					}
					
					if(Auth::hasPermission('MODULES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> 'Modules',
							'icon'		=> '<i class="material-icons">extension</i>',
							'order'		=> 3,
							'url'		=> '/admin/modules',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/modules')
						];
					}
					
					if(Auth::hasPermission('LOGFILES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> 'Logfiles',
							'icon'		=> '<i class="material-icons">insert_drive_file</i>',
							'order'		=> 4,
							'url'		=> '/admin/logs',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/logs')
						];
					}
					
					if(Auth::hasPermission('SERVER::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> 'Server',
							'icon'		=> '<i class="material-icons">memory</i>',
							'order'		=> 5,
							'url'		=> '/admin/server',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/server')
						];
					}
				break;
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