<?php
	use fruithost\I18N;
?>
<table class="table table-borderless table-striped table-hover">
	<thead>
		<tr>
			<th scope="col" colspan="3"><?php I18N::__('User'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($users AS $user) {
				?>
				<tr>
					<td scope="row" width="1px">
						<div class="custom-control custom-checkbox">
							<input class="custom-control-input" type="checkbox" id="user_<?php print $user->id; ?>" name="user[]" value="<?php print $user->id; ?>" />
							<label class="custom-control-label" for="user_<?php print $user->id; ?>"></label>
						</div>
					</td>
					<td>
						<small>#<?php print $user->id; ?></small> <strong><?php print $user->username; ?></strong>
						<p class="module-actions"><?php
							$links = [];
							
							$links['delete'] = sprintf('<a href="%s" data-confirm="%s" class="text-danger">%s</a>', $this->url('/admin/users/?delete=' . $user->id), I18N::get('Do you really wan\'t to delete the user?'), I18N::get('Delete'));
							
							print implode(' | ', $links);
						?></p>
					</td>
					<td>
						<?php print $user->email; ?>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>