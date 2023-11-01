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
	<style type="text/css">
		@keyframes blink {
			50% {
				visibility: hidden;
			}
		}
	</style>
	<div class="terminal position-fixed">
		<div class="output"></div>
		<input name="command" placeholder="<?php I18N::__('Command...'); ?>" />
	</div>
	<script type="text/javascript">
		_watcher_console = setInterval(function() {
			if(typeof(jQuery) !== 'undefined' && typeof(Terminal) !== 'undefined') {
				clearInterval(_watcher_console);
				
				(function($) {
					let output	= $('div.output');
					let command = $('input[name="command"]');
					let parser	= new Terminal();
					
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
								
								output.append(parser.parse(response));
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
								position = history.length;
								send(text);
								command.val('');
							break;
							
							/* Up */
							case 38:
								if(position > 0) {
									--position;
									
									if(position < 0) {
										position = history.length - 1;
									}
									
									command.blur();
									command.val(history[position]);
									command.focus();
								}
							break;
							
							/* Down */
							case 40:
								if(position >= history.length) {
									break;
								}
								
								position++;
								
								if(position === history.length) {
									command.val('');
								} else {
									command.blur();
									command.focus();
									command.val(history[position]);
								}
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