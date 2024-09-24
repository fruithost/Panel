<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    use fruithost\Localization\I18N;
    use fruithost\UI\Icon;
	
	function build($data) {
		$args = [];
		
		foreach($data AS $arg) {
			$type = gettype($arg);
			
			switch($type) {
				case 'resource':
					$args[] = sprintf('<span class="badge text-bg-secondary">%s</span>', $arg);
				break;
				case 'string':
					$args[] = sprintf('<span class="badge text-bg-light">"%s"</span>', $arg);
				break;
				case 'object':
					$args[] = sprintf('<span class="badge text-bg-warning">%s</span>', get_class($arg));
				break;
				case 'NULL':
					$args[] = '<span class="badge text-bg-primary">NULL</span>';
				break;
				case 'boolean':
					$args[] = sprintf('<span class="font-monospace text-bold text-primary">%s</span>', $arg ? 'true' : 'false');
				break;
				case 'integer':
				case 'double':
					$args[] = sprintf('<span class="font-monospace text-warning-emphasis">%s</span>', $arg);
				break;
				case 'array':
					$args[] = sprintf('<span class="badge text-bg-danger">[ %s ]</span>', implode(', ', build($arg)));
				break;
				default:
					$args[] = $type;
				break;
			}
		}
		
		return $args;
	}
?>
<div class="border rounded overflow-hidden mt-5 mb-5">
	<table class="table table-borderless table-striped table-hover mb-0">
		<thead>
			<tr>
				<th class="bg-secondary-subtle" scope="col" colspan="2"><?php I18N::__('Module'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Code'); ?></th>
				<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Message'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($errors AS $index => $entry) {
					?>
					<tr>
						<td>
							 <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#entry_<?php print $index; ?>" aria-expanded="false">
								<?php Icon::show('arrow-down'); ?>
							 </button>
						</td>
						<td>
							<?php print $entry->getModule(); ?>
						</td>
						<td>
							<?php print $entry->getCode(); ?>
						</td>
						<td>
							<div class="alert alert-dark" role="alert">
								<?php print $entry->getMessage(); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="p-0">
							<table class="table table-borderless table-striped table-hover mb-0 accordion-body collapse" id="entry_<?php print $index; ?>"> 
								<thead>
									<tr>
										<th class="bg-secondary-subtle" scope="col"><?php I18N::__('Function'); ?></th>
										<th class="bg-secondary-subtle" scope="col"><?php I18N::__('File'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										foreach($entry->getTrace() as $line) {
											?>
												<tr>
													<td>
														<div class="alert alert-dark p-1 m-0" role="alert">
															<?php
																
																if(isset($line['function'])) {
																	printf('%s%s%s(%s)', $line['class'], $line['type'], $line['function'], implode(', ', build($line['args'])));
																}
															?>
														</div>
													</td>
													<td>
														<?php
															if(isset($line['file'])) {
																printf('%s:%s', str_replace(dirname(PATH), '', $line['file']), $line['line']);
															}
														?>
													</td>
												</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>