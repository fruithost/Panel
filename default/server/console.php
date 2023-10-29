<?php
	use fruithost\Auth;
	use fruithost\Utils;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong>Access denied!</strong>
				<p class="pb-0 mb-0">You have no permissions for this page.</p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
	<div class="terminal position-fixed">
		<div class="output"></div>
		<input name="command" placeholder="Command..." style="height: 30px; width: 100%;" />
	</div>
	<script type="text/javascript">
		_watcher_console = setInterval(function() {
			if(typeof(jQuery) !== 'undefined') {
				clearInterval(_watcher_console);
				
				(function($) {						
					let output	= $('div.output');
					let command = $('input[name="command"]');
					
					command.focus();
					
					command.keypress(function(e) {
						if(e.which == 13) {
							$.ajax({
								type:	'POST',
								url:	'/server/console',
								data:	{
									action:	'command',
									command: command.val()
								},
								success: function onSuccess(response) {
									output.append('<div>' + response + '</div>');
									command.focus();
									output.scrollTop(output[0].scrollHeight - output.height());
								}
							});
							
							command.val('');
						}
					});
				}(jQuery));
			}
		}, 500);
	</script>
	<?php
	$template->footer();
?>