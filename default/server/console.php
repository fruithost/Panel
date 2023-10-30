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
					
					function send(cmd) {
						$.ajax({
							type:	'POST',
							url:	'/server/console',
							data:	{
								action:	'command',
								command: cmd
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
					
					let history		= [];
					let position	= 0;
					command.keydown(function(e) {
						switch(e.which) {
							/* Enter */
							case 13:
								let text = command.val();
								history.push(text);
								position = history.length - 1;
								send(text);
								command.val('');
							break;
							
							/* Up */
							case 38:
								--position;
								
								if(position < 0) {
									position = history.length - 1;
								}
								
								command.val(history[position]);
							break;
							
							/* Down */
							case 40:
								++position;
								
								if(position > history.length - 1) {
									position = 0;
								}
								
								command.val(history[position]);
							break;
						}
					});
					
					send('motd');
					command.focus();
				}(jQuery));
			}
		}, 500);
	</script>
	<?php
	$template->footer();
?>