<?php
	
	use fruithost\Localization\I18N;
	
	$permissions = [
		'*'       => true,
		'SERVER'  => [
			'SERVER'   => I18N::get('May view the server overview.'),
			'SETTINGS' => I18N::get('May change the server settings.'),
			'LOGFILES' => I18N::get('May view the log files.'),
			'CONSOLE'  => I18N::get('Can use the console/terminal.'),
			'PACKAGES' => I18N::get('Can see the installed packages of the system.'),
			'SERVICES' => I18N::get('May view the system services.'),
			'NETWORK'  => I18N::get('May manage the network settings.'),
			'REBOOT'   => I18N::get('May reboot the server.')
		],
		'THEMES'  => [
			'VIEW'    => I18N::get('May view the installed themes.'),
			'INSTALL' => I18N::get('May install new themes.'),
			'SET'     => I18N::get('May set and change themes globally.')
		],
		'USERS'   => [
			'VIEW'        => I18N::get('May view the users of the system.'),
			'CREATE'      => I18N::get('May create new users.'),
			'DELETE'      => I18N::get('Allows to delete users.'),
			'EDIT'        => I18N::get('Allows to edit users.'),
			'PERMISSIONS' => I18N::get('May change the authorizations of users.'),
		],
		'MODULES' => [
			'VIEW'        => I18N::get('May view the installed modules.'),
			'REPOSITORYS' => I18N::get('May administer the registered repositories.'),
			'ERRORS'      => I18N::get('May view error messages from active modules.'),
			'HANDLE'      => I18N::get('May handle modules and change their properties.'),
			'INSTALL'     => I18N::get('May install new modules.'),
			'DEINSTALL'   => I18N::get('May deinstall modules.'),
		]
	];
?>
<p></p>
<div class="container">
    <div class="form-group row mx-5">
        <div class="row g-3 gx-5">
            <h4><?php I18N::__('Global'); ?></h4>
        </div>
        <div class="row g-3 gx-5">
            <div class="col-auto form-check">
                <input class="form-check-input" type="radio" name="permissions_global" value="true"
                       id="permissions_globals"<?php print ($user->hasPermission('*') ? ' CHECKED' : ''); ?>>
                <label class="form-check-label" for="permissions_globals">
					<?php I18N::__('Global rights'); ?>
                </label>
            </div>
            <div class="col-auto">
                <span class="form-text">
                    <?php I18N::__('The user has global root privileges'); ?>
                </span>
            </div>
        </div>

        <div class="row g-3 gx-5">
            <div class="col-auto form-check">
                <input class="form-check-input" type="radio" name="permissions_global" value="false"
                       id="permissions_specific"<?php print (!$user->hasPermission('*') ? ' CHECKED' : ''); ?>>
                <label class="form-check-label" for="permissions_specific">
					<?php I18N::__('Specific rights'); ?>
                </label>
            </div>
            <div class="col-auto">
                <span class="form-text">
                    <?php I18N::__('The user only has specific rights'); ?>
                </span>
            </div>
        </div>
    </div>
    <hr class="mb-4"/>
    <div class="form-group row mx-5">
        <div class="row">
            <h4><?php I18N::__('Specific'); ?></h4>
        </div>
    </div>
    <div class="form-group row border rounded overflow-hidden mb-5 mx-5">
        <table class="table table-borderless table-hover mb-0">
            <thead>
            <tr>
                <th class="bg-secondary-subtle" scope="col"><?php I18N::__('Permissions'); ?></th>
                <th class="bg-secondary-subtle" scope="col"><?php I18N::__('Description'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
				foreach($permissions as $section => $rights) {
					if($section === '*') {
						continue;
					}
					?>
                    <tr>
                        <th colspan="2" class="bg-dark-subtle"><?php print $section; ?></th>
                    </tr>
					<?php
					foreach($rights as $right => $description) {
						?>
                        <tr>
                            <td class="ps-5">
                                <label for="staticEmail" class="col-sm-2 col-form-label">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               name="permissions[<?php printf('%s::%s', $section, $right); ?>]"
                                               id="<?php printf('%s_%s', $section, $right); ?>"/>
                                        <label class="form-check-label"
                                               for="<?php printf('%s_%s', $section, $right); ?>"><?php print $right; ?></label>
                                    </div>
                                </label>
                            </td>
                            <td>
								<?php print $description; ?>
                            </td>

                        </tr>
						<?php
					}
				}
			?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        (() => {
            'use strict'

            window.addEventListener('DOMContentLoaded', () => {
                function change(state) {
                    document.querySelectorAll('[role="switch"]').forEach(element => {
                        element.disabled = state;
                    });
                }

                document.querySelectorAll('input[name="permissions_global"]').forEach(element => {
                    element.addEventListener('change', (event) => {
                        change(event.target.value === 'true');
                    });
                });

                change(<?php print ($user->hasPermission('*') ? 'true' : 'false'); ?>);
            })
        })();
    </script>