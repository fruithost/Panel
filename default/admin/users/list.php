<?php
    use fruithost\Accounting\User;
    use fruithost\Localization\I18N;
?>
<div class="border rounded overflow-hidden mb-5">
	<table class="table table-borderless table-striped table-hover mb-0">
		<thead>
			<tr>
				<th class="bg-secondary-subtle" scope="col" colspan="3"><?php I18N::__('User'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Name'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('E-Mail'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($users AS $u) {
					$user = new User();
					$user->fetch($u->id);
					
					if($user->getID() == null) {
						continue;
					}
					?>
					<tr>
						<td scope="row" width="1px">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="user_<?php print $user->getID(); ?>" name="user[]" value="<?php print $user->getID(); ?>"<?php print ($user->getID() == 1 ? ' DISABLED' : ''); ?> />
								<label class="form-check-label" for="user_<?php print $user->getID(); ?>"></label>
							</div>
						</td>
						<td width="1px">
							<img src="<?php print $user->getGravatar(); ?>" class="object-fit-cover bg-dark border rounded" alt="" />
						</td>
						<td>
							<small>#<?php print $user->getID(); ?></small> <strong><?php print $user->getUsername(); ?></strong>
						</td>
						<td>
							<?php print $user->getFullName(); ?>
						</td>
						<td>
							<?php print $user->getMail(); ?>
						</td>
						<td width="1px">
							<div class="btn-group" role="group">
								<?php
									printf('<a href="%s" class="btn btn-info">%s</a>', $this->url('/admin/users/' . $user->getID()), I18N::get('Edit'));
									
									if($user->getID() != 1) {
										printf('<button name="action" value="deletes" data-confirm="%s" class="btn btn-danger deletes">%s</button>', I18N::get('Do you really wan\'t to delete the user?'), I18N::get('Delete'));
									}
								?>
							</div>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>