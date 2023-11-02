<?php
	use fruithost\User;
	use fruithost\I18N;
?>
<table class="table table-borderless table-striped table-hover">
	<thead>
		<tr>
			<th scope="col" colspan="3"><?php I18N::__('User'); ?></th>
			<th scope="col"><?php I18N::__('Name'); ?></th>
			<th scope="col"><?php I18N::__('E-Mail'); ?></th>
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
						<div class="custom-control custom-checkbox">
							<input class="custom-control-input" type="checkbox" id="user_<?php print $user->getID(); ?>" name="user[]" value="<?php print $user->getID(); ?>" />
							<label class="custom-control-label" for="user_<?php print $user->getID(); ?>"></label>
						</div>
					</td>
					<td width="1px">
						<img src="<?php print $user->getGravatar(); ?>" />
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
								printf('<a href="%s" data-confirm="%s" class="btn btn-danger">%s</a>', $this->url('/admin/users/' . $user->getID() . '/delete'), I18N::get('Do you really wan\'t to delete the user?'), I18N::get('Delete'));
							?>
						</div>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>