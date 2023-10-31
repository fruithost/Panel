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
	<style>
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
		const TerminalParser = (function() {
			this.colors = {
				styles: {
					0: 'reset',
					1: 'bold',
					2: 'faint',
					3: 'italic',
					4: 'underline',
					5: 'blink',
					6: 'blink',
					7: 'swap-colors'
				},
				foreground: {
					30: '#444444', 		/* Black */
					31: '#ff8B92', 		/* Red */
					32: '#9BC67C', 		/* Green */
					33: '#E5C07B', 		/* Yellow */
					34: '#6AB0EB', 		/* Blue */
					35: '#DF8FF6', 		/* Magenta */
					36: '#56D7D7', 		/* Cyan */
					37: '#DDDDDD', 		/* White */
					38: 'multicolor',
					90: '#878787', 		/* Bright Black */
					91: '#FC646D', 		/* Bright Red */
					92: '#7AE52D', 		/* Bright Green */
					93: '#FFDB97', 		/* Bright Yellow */
					94: '#32A1FF', 		/* Bright Blue */
					95: '#F0BBFF', 		/* Bright Magenta */
					96: '#A7FFFF', 		/* Bright Cyan */
					97: '#F4F4F4' 		/* Bright White */
				},
				background: {
					40: '#444444', 		/* Black */
					41: '#B13139', 		/* Red */
					42: '#698754', 		/* Green */
					43: '#AA8D57', 		/* Yellow */
					44: '#3A6385', 		/* Blue */
					45: '#8F53A0', 		/* Magenta */
					46: '#339696', 		/* Cyan */
					47: '#828282', 		/* White */
					48: 'multicolor',
					100: '#5D5D5D', 	/* Bright Black */
					101: '#BE000C', 	/* Bright Red */
					102: '#52B90B', 	/* Bright Green */
					103: '#BDCA0A', 	/* Bright Yellow */
					104: '#0E75CA', 	/* Bright Blue */
					105: '#B622E0', 	/* Bright Magenta */
					106: '#00B7B7', 	/* Bright Cyan */
					107: '#CACACA' 		/* Bright White */
				}
			};
			
			this.escape = function escape(html) {
				var text = document.createTextNode(html);
				var p = document.createElement('p');
				p.appendChild(text);
				return p.innerHTML;
			};
			
			this.tokens = function tokens(input) {
				var matchingControl	= null;
				var matchingData	= null;
				var matchingText	= '';
				var ansiState		= [];
				var result			= [];
				var state			= {};
				var eraseChar		= function eraseChar() {
					var index, text;
					
					if(matchingText.length) {
						matchingText = matchingText.substr(0, matchingText.length - 1);
					} else if(result.length) {
						index	= result.length - 1;
						text	= result[index].text;
						
						if(text.length === 1) {
							result.pop();
						} else {
							result[index].text = text.substr(0, text.length - 1);
						}
					}
				};
				
				input.split('').forEach(function(character) {
					if(matchingControl != null) {
						if(matchingControl == '\033' && character == '\[') {
							if(matchingText) {
								state.text 		= matchingText;
								result.push(state);
								state			= {};
								matchingText	= "";
							}

							matchingControl	= null;
							matchingData	= '';
						} else {
							matchingText	+= matchingControl + character;
							matchingControl	= null;
						}
						
						return;
					} else if(matchingData != null) {
						switch(character) {
							case ';':
								ansiState.push(matchingData);
								matchingData = '';
							break;
							case 'm':
								ansiState.push(matchingData);
								matchingData = null;
								matchingText = '';
								
								if(ansiState.length >= 3) {
									console.log(ansiState);
									switch(parseInt(ansiState[0])) {
										case 38:
											switch(parseInt(ansiState[1])) {
												/* RGB Color */
												case 2:
													state.foreground = 'rgb(' + [
														ansiState[2],
														ansiState[3],
														ansiState[4]
													].join(',') + ')';
												break;
											
												/* 8-bit Color */
												case 5:
													var color = parseInt(ansiState[2]);
													
													state.foreground = 'rgb(' + [
														(color >> 5) * 255 / 7,
														((color >> 2) & 0x07) * 255 / 7,
														(color & 0x03) * 255 / 3
													].join(',') + ')';
													
													console.log(state.foreground);
												break;
											}
										break;
										case 48:
											switch(parseInt(ansiState[1])) {
												/* RGB Color */
												case 2:
													state.background = 'rgb(' + [
														ansiState[2],
														ansiState[3],
														ansiState[4]
													].join(',') + ')';
												break;
											
												/* 8-bit Color */
												case 5:
													var color = parseInt(ansiState[2]);
													
													state.background = 'rgb(' + [
														(color >> 5) * 255 / 7,
														((color >> 2) & 0x07) * 255 / 7,
														(color & 0x03) * 255 / 3
													].join(',') + ')';
												break;
											}
										break;
									}
								} else {
									ansiState.forEach(function (ansiCode) {
										if(this.colors.foreground[ansiCode]) {
											state.foreground = this.colors.foreground[ansiCode];
										} else if(this.colors.background[ansiCode]) {
											state.background = this.colors.background[ansiCode];
										} else if(ansiCode == 39) {
											delete state.foreground;
										} else if(ansiCode == 49) {
											delete state.background;
										} else if(this.colors.styles[ansiCode]) {
											state[this.colors.styles[ansiCode]] = true;
										} else if(ansiCode == 22) {
											state.bold = false;
										} else if(ansiCode == 23) {
											state.italic = false;
										} else if(ansiCode == 24) {
											state.underline = false;
										}
									}.bind(this));
								}
								
								ansiState = [];
							break;
							default:
								matchingData += character;
							break;
						}
						
						return;
					}

					switch(character) {
						case '\033':
							matchingControl = character;
						break;
						case '\u0008':
							this.eraseChar();
						break;
						default:
							matchingText += character;
						break;
					}
				}.bind(this));

				if(matchingText) {
					state.text = matchingText + (matchingControl ? matchingControl : '');
					result.push(state);
				}
				
				return result;
			};

			this.parse = function parse(input) {
				var lines	= input.split('\n');
				var output	= "";
					
				lines.forEach(function(entry) {
					let line = '';
					
					this.tokens(entry).forEach(function(token) {
						var style	= {};
						
						if('bold' in token) {
							style.fontWeight = 'bold';
						}
						
						if('italic' in token) {
							style.fontStyle = 'italic';
						}
						
						if('faint' in token) {
							style.opacity  = 0.5;
						}
						
						if('underline' in token) {
							style.textDecoration  = 'underline';
						}
						
						if('blink' in token) {
							style.opacity	= 1;
							style.animation = 'blink 1s step-end infinite';
						}
						
						if('foreground' in token) {
							style.color = token.foreground;
						}
						
						if('background' in token) {
							style.backgroundColor = token.background;
						}
						
						if('swap-colors' in token) {
							let color				= style.color;
							let background			= style.backgroundColor;
							style.color				= background;
							style.backgroundColor	= color;
						}
						
						if('reset' in token) {
							/* Reset Style? */
							style	= {};
						}
						
						if(Object.keys(style).length > 0) {
							let elem = document.createElement('span');
							
							Object.keys(style).forEach(function(name) {
								elem.style[name] = style[name];
							});
							
							elem.innerText = token.text;
							var wrap = document.createElement('div');
							wrap.appendChild(elem.cloneNode(true));
							line += wrap.innerHTML;
						} else {
							line += this.escape(token.text);
						}
					}.bind(this));
					
					output += '<div>' + line + '</div>';
				}.bind(this));

				return output;
			};
		});
		
		_watcher_console = setInterval(function() {
			if(typeof(jQuery) !== 'undefined') {
				clearInterval(_watcher_console);
				
				(function($) {
					let output	= $('div.output');
					let command = $('input[name="command"]');
					let parser	= new TerminalParser();
					
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