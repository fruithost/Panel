<?php
	use fruithost\Auth;
	use fruithost\I18N;
?>
<table class="table table-borderless table-striped table-hover">
	<thead>
		<tr>
			<th scope="col" colspan="2"><?php I18N::__('Repository'); ?></th>
			<th scope="col"><?php I18N::__('Status'); ?></th>
			<th scope="col"><?php I18N::__('Actions'); ?></th>
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
								printf('<span class="text-warning">%s</span>', I18N::get('Never updated'));
							} else {
								printf('<small>%s:</small><br />%s', I18N::get('Last updated'), date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($repository->time_updated)));
							}
						?>
					</td>
					<td class="text-right">
						<button class="update btn btn-sm btn-info" type="submit" name="action" value="update" id="update_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>"><?php I18N::__('Update'); ?></button>
						<button class="delete btn btn-sm btn-danger" type="submit" name="action" value="delete" id="delete_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>"><?php I18N::__('Delete'); ?></button>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>