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
							'name'		=> I18N::get('Account'),
							'icon'		=> '<i class="material-icons">account_circle</i>',
							'order'		=> 1,
							'url'		=> '/account',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/account')
						],
						(object) [
							'name'		=> I18N::get('Settings'),
							'icon'		=> '<i class="material-icons">settings</i>',
							'order'		=> 2,
							'url'		=> '/settings',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/settings')
						],
						(object) [
							'name'		=> I18N::get('Logout'),
							'icon'		=> '<i class="material-icons">power_settings_new</i>',
							'order'		=> 99999,
							'url'		=> '/logout',
							'active'	=> $this->navigation->getCore()->getRouter()->is('/logout')
						]
					];
				break;
				case 'admin':
					if(Auth::hasPermission('*')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Overview'),
							'icon'		=> '<i class="material-icons">apps</i>',
							'order'		=> 1,
							'url'		=> '/admin',
							'active'	=> $this->navigation->getCore()->getRouter()->is('/admin')
						];
					}
					
					if(Auth::hasPermission('USERS::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Users'),
							'icon'		=> '<i class="material-icons">supervised_user_circle</i>',
							'order'		=> 1,
							'url'		=> '/admin/users',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/users')
						];
					}
					
					if(Auth::hasPermission('THEMES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Themes'),
							'icon'		=> '<i class="material-icons">palette</i>',
							'order'		=> 2,
							'url'		=> '/admin/themes',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/themes')
						];
					}
					
					if(Auth::hasPermission('MODULES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Modules'),
							'icon'		=> '<i class="material-icons">extension</i>',
							'order'		=> 3,
							'url'		=> '/admin/modules',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/modules')
						];
					}
				break;
				case 'server':
					if(Auth::hasPermission('SERVER::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Server'),
							'icon'		=> '<i class="material-icons">memory</i>',
							'order'		=> 1,
							'url'		=> '/admin/server',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/server')
						];
					}
					
					if(Auth::hasPermission('LOGFILES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Logfiles'),
							'icon'		=> '<i class="material-icons">insert_drive_file</i>',
							'order'		=> 2,
							'url'		=> '/admin/logs',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/logs')
						];
					}
					
					if(Auth::hasPermission('SERVER::MANAGE')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Settings'),
							'icon'		=> '<i class="material-icons">tune</i>',
							'order'		=> 1,
							'url'		=> '/server/settings',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/settings')
						];
											
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Console'),
							'icon'		=> '<i class="material-icons">input</i>',
							'order'		=> 2,
							'url'		=> '/server/console',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/console')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Packages'),
							'icon'		=> '<i class="material-icons">unarchive</i>',
							'order'		=> 3,
							'url'		=> '/server/packages',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/packages')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Services'),
							'icon'		=> '<i class="material-icons">tag</i>',
							'order'		=> 4,
							'url'		=> '/server/services',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/services')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Reboot'),
							'icon'		=> '<i class="material-icons">power_settings_new</i>',
							'order'		=> 5,
							'url'		=> '/server/reboot',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/reboot')
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
				
				return $a->order > $b->order ? 1 : -1;
			});
			
			return $results;
		}
	}
?>