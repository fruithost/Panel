(() => {
	'use strict'
	window.addEventListener('DOMContentLoaded', () => {
		document.body.style.overflow = 'hidden';
		let destination	= document.querySelector('input[name="destination"]');
		let output		= document.querySelector('div.output');
		let command 	= document.querySelector('input[name="command"]');
		let parser		= new Terminal();

		function send(cmd) {
			new Ajax(destination.value).onError(function(event, error, response) {
				console.warn(event, error, response);
			}).onSuccess(function(response) {
				if(response === '\u001B[H\u001B[2J\u001B[3J' || response === 'clear') {
					output.innerHTML ='';
					return;
				}
				
				output.innerHTML += parser.parse(response);
				command.focus();
				output.scrollTo(0, output.scrollHeight);
			}).post({	
				action:	'command',
				command: cmd
			});
		}
		
		let history		= [];
		let position	= 0;
		
		command.addEventListener('keydown', (event) => {
			switch(event.keyCode) {
				/* Enter */
				case 13:
					let text = command.value;
					history.push(text);
					position = history.length;
					send(text);
					command.value = '';
				break;
				
				/* Up */
				case 38:
					if(position > 0) {
						--position;
						
						if(position < 0) {
							position = history.length - 1;
						}
						
						command.blur();
						command.value = history[position];
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
						command.value = '';
					} else {
						command.blur();
						command.focus();
						command.value = history[position];
					}
				break;
			}
		});
		
		send('motd');
		command.focus();
	});
})();