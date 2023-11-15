<?php
    use fruithost\Accounting\Auth;
    use fruithost\Localization\I18N;
?>
<div class="border rounded overflow-hidden mt-5 mb-5">
	<table class="table table-borderless table-striped table-hover mb-0">
		<thead>
			<tr>
				<th class="bg-secondary-subtle" scope="col" colspan="2"><?php I18N::__('Repository'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Status'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($repositorys AS $repository) {
					?>
					<tr>
						<td scope="row" width="1px">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="repository_<?php print $repository->id; ?>" name="repository[]" value="<?php print $repository->id; ?>" />
								<label class="form-check-label" for="repository_<?php print $repository->id; ?>"></label>
							</div>
						</td>
						<td>
							<a href="<?php print $repository->url; ?>" target="_blank"><?php print $repository->url; ?></a>
						</td>
						<td>
							<?php
								if(empty($repository->time_updated)) {
									printf('<span class="text-warning">%s</span>', I18N::get('Never updated'));
								} else if($repository->time_updated == '0000-00-00 00:00:00') {
									printf('<span class="text-danger">%s<br />%s (<a href="https://github.com/fruithost/Documentation/blob/main/Repositorys.md" target="_blank">%s</a>)</span>', I18N::get('Error'), I18N::get('Malformed Repository'), I18N::get('More Informations'));
								} else {
									printf('<small>%s:</small><br />%s', I18N::get('Last updated'), date(Auth::getSettings('TIME_FORMAT', NULL, 'd.m.Y - H:i'), strtotime($repository->time_updated)));
								}
							?>
						</td>
						<td class="text-end">
							<button class="update btn btn-sm btn-info" type="submit" name="action" value="update" id="update_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>"><?php I18N::__('Update'); ?></button>
							<button class="delete btn btn-sm btn-danger" type="submit" name="action" value="delete" id="delete_<?php print $repository->id; ?>" value="<?php print $repository->id; ?>"><?php I18N::__('Delete'); ?></button>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>