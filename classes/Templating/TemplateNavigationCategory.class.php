<?php
	namespace fruithost\Templating;

    use fruithost\Accounting\Auth;
    use fruithost\Localization\I18N;
	use fruithost\UI\Icon;

    class TemplateNavigationCategory {
		private ?TemplateNavigation $navigation		= null;
		private ?string $id							= null;
		private ?string $label						= null;
		
		public function __construct(TemplateNavigation $navigation, string $id, string $label) {
			$this->navigation	= $navigation;
			$this->id			= $id;
			$this->label		= $label;
		}
		
		public function getID() : ?string {
			return $this->id;
		}
		
		public function getLabel() : ?string {
			return $this->label;
		}
		
		public function isEmpty() : bool {
			return (count($this->getEntries()) === 0);
		}
		
		public function getEntries() : array {
			$hardcoded = [];
			
			switch($this->id) {
				case 'account':
					$hardcoded = [
						(object) [
							'name'		=> I18N::get('Account'),
							'icon'		=> Icon::render('account'),
							'order'		=> 1,
							'url'		=> '/account',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/account')
						],
						(object) [
							'name'		=> I18N::get('Settings'),
							'icon'		=> Icon::render('settings'),
							'order'		=> 2,
							'url'		=> '/settings',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/settings')
						],
						(object) [
							'name'		=> I18N::get('Logout'),
							'icon'		=> Icon::render('logout'),
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
							'icon'		=> Icon::render('overview'),
							'order'		=> 1,
							'url'		=> '/admin',
							'active'	=> $this->navigation->getCore()->getRouter()->is('/admin')
						];
					}
					
					if(Auth::hasPermission('USERS::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Users'),
							'icon'		=> Icon::render('users'),
							'order'		=> 1,
							'url'		=> '/admin/users',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/users')
						];
					}
					
					if(Auth::hasPermission('THEMES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Themes'),
							'icon'		=> Icon::render('themes'),
							'order'		=> 2,
							'url'		=> '/admin/themes',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/admin/themes')
						];
					}
					
					if(Auth::hasPermission('MODULES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Modules'),
							'icon'		=> Icon::render('modules'),
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
							'icon'		=> Icon::render('server'),
							'order'		=> 1,
							'url'		=> '/server/server',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/server')
						];
					}
					
					if(Auth::hasPermission('LOGFILES::VIEW')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Logfiles'),
							'icon'		=> Icon::render('logfiles'),
							'order'		=> 2,
							'url'		=> '/server/logs',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/logs')
						];
					}
					
					if(Auth::hasPermission('SERVER::MANAGE')) {
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Settings'),
							'icon'		=> Icon::render('options'),
							'order'		=> 1,
							'url'		=> '/server/settings',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/settings')
						];
											
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Console'),
							'icon'		=> Icon::render('console'),
							'order'		=> 2,
							'url'		=> '/server/console',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/console')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Packages'),
							'icon'		=> Icon::render('packages'),
							'order'		=> 3,
							'url'		=> '/server/packages',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/packages')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Services'),
							'icon'		=> Icon::render('services'),
							'order'		=> 4,
							'url'		=> '/server/services',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/services')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Network'),
							'icon'		=> Icon::render('network'),
							'order'		=> 5,
							'url'		=> '/server/network',
							'active'	=> $this->navigation->getCore()->getRouter()->startsWith('/server/network')
						];
						
						$hardcoded[] = (object) [
							'name'		=> I18N::get('Reboot'),
							'icon'		=> Icon::render('reboot'),
							'order'		=> 6,
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