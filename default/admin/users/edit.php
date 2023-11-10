<?php
	use fruithost\Accounting\Auth;
	use fruithost\I18N;
	
	$template->header();
	?>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item"><a class="nav-link<?php print (empty($action) ? ' active' : ''); ?>" id="account-tab" href="<?php print $this->url(sprintf('/admin/users/%s', $tab)); ?>" role="tab"><?php I18N::__('Account'); ?></a></li>
		<li class="nav-item"><a class="nav-link<?php print (!empty($action) && $action === 'password' ? ' active' : ''); ?>" id="password-tab" href="<?php print $this->url(sprintf('/admin/users/%s/password', $tab)); ?>" role="tab"><?php I18N::__('Password Settings'); ?></a></li>
		<li class="nav-item"><a class="nav-link<?php print (!empty($action) && $action === 'settings' ? ' active' : ''); ?>" id="settings-tab" href="<?php print $this->url(sprintf('/admin/users/%s/settings', $tab)); ?>" role="tab"><?php I18N::__('Settings'); ?></a></li>
	</ul>
	<?php
		if(isset($error)) {
			?>
				<div class="alert alert-danger mt-4" role="alert"><?php (is_array($error) ? var_dump($error) : print $error); ?></div>
			<?php
		}
		
		if(isset($success)) {
			?>
				<div class="alert alert-success mt-4" role="alert"><?php (is_array($success) ? var_dump($success) : print $success); ?></div>
			<?php
		}
	?>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane<?php print (empty($action) ? ' show active' : ''); ?>" id="account" role="tabpanel" aria-labelledby="account-tab">
			<?php
				$template->display('admin/users/edit/account', [
					'user'	=> $user
				]);
			?>
		</div>
		<div class="tab-pane<?php print (!empty($action) && $action === 'password' ? ' show active' : ''); ?>" id="password" role="tabpanel" aria-labelledby="password-tab">
			<?php
				$template->display('admin/users/edit/password', [
					'user'		=> $user,
					'timezones' => $timezones
				]);
			?>
		</div>
		<div class="tab-pane<?php print (!empty($action) && $action === 'settings' ? ' show active' : ''); ?>" id="settings" role="tabpanel" aria-labelledby="settings-tab">
			<?php
				$template->display('admin/users/edit/settings', [
					'user'	=> $user
				]);
			?>
		</div>
	</div>
	<?php
	$template->footer();
?>