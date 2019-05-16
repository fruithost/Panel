<?php
	use fruithost\Auth;
?>
<table class="table table-borderless table-striped table-hover">
	<thead>
		<tr>
			<th scope="col" colspan="2">Repository</th>
			<th scope="col">Status</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($repositorys AS $repository) {
				?>
				<tr>
					<td scope="row" width="1px"><input type="checkbox" name="repository[]" value="<?php print $repository->id; ?>" /></td>
					<td>
						<a href="<?php print $repository->url; ?>" target="_blank"><?php print $repository->url; ?></a>
					</td>
					<td>
						<?php
							if(empty($repository->time_updated)) {
								print '<span class="text-warning">Never updated</span>';
							} else {
								printf('<small>Last updated:</small><br />%s', date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($repository->time_updated)));
							}
						?>
					</td>
					<td class="text-right">
						<button class="update btn btn-sm btn-info" type="submit" name="action" value="update" id="update_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>">Update</button>
						<button class="delete btn btn-sm btn-danger" type="submit" name="action" value="delete" id="delete_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>">Delete</button>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>