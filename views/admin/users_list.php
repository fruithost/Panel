<table class="table table-borderless table-striped table-hover">
	<thead>
		<tr>
			<th scope="col" colspan="3">User</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($users AS $user) {
				?>
				<tr>
					<td scope="row" width="1px">
						<input type="checkbox" name="module[]" value="<?php print $user->id; ?>" />
					</td>
					<td>
						<small>#<?php print $user->id; ?></small> <strong><?php print $user->username; ?></strong>
						<p class="module-actions"><?php
							$links = [];
							
							$links['delete'] = sprintf('<a href="%s" data-confirm="Do you really wan\'t to delete the user?" class="text-danger">Delete</a>', $this->url('/admin/users/?delete=' . $user->id));
							
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