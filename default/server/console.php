<?php
	use fruithost\Auth;
	use fruithost\Utils;
	use fruithost\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
	<div class="terminal position-fixed">
		<div class="output"></div>
		<input name="command" placeholder="<?php I18N::__('Command...'); ?>" />
	</div>
	<script type="text/javascript">
		_watcher_console = setInterval(function() {
			if(typeof(jQuery) !== 'undefined') {
				clearInterval(_watcher_console);
				
				(function($) {
					let output	= $('div.output');
					let command = $('input[name="command"]');
					
					function send(command) {
						$.ajax({
							type:	'POST',
							url:	'/server/console',
							data:	{
								action:	'command',
								command: command
							},
							success: function onSuccess(response) {
								if(response == '\u001B[H\u001B[2J\u001B[3J' || response == 'clear') {
									output.empty();
									return;
								}
								
								output.append('<div>' + response + '</div>');
								command.focus();
								output.scrollTop(output[0].scrollHeight - output.height());
							}
						});
					}
					
					command.focus();
					
					command.keypress(function(e) {
						if(e.which == 13) {
							send(command.val());
							command.val('');
						}
					});
					
					send('motd');
				}(jQuery));
			}
		}, 500);
	</script>
	<?php
	$template->footer();
?>