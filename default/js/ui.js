(() => {
	'use strict'

	const getStoredTheme = () => localStorage.getItem('theme')
	const setStoredTheme = theme => localStorage.setItem('theme', theme)

	const getPreferredTheme = () => {
		const storedTheme = getStoredTheme()
		
		if (storedTheme) {
			return storedTheme
		}

		return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
	}

	const setTheme = theme => {
		if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
			document.documentElement.setAttribute('data-bs-theme', 'dark')
		} else {
			document.documentElement.setAttribute('data-bs-theme', theme)
		}
	}

	setTheme(getPreferredTheme())

	const showActiveTheme = (theme, focus = false) => {
		const themeSwitcher = document.querySelector('#bd-theme')

		if (!themeSwitcher) {
			return
		}

		const themeSwitcherText = document.querySelector('#bd-theme-text')
		const activeThemeIcon = document.querySelector('.theme-icon-active')
		const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
		const svgOfActiveBtn = btnToActive.querySelector('i')

		document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
			element.classList.remove('active')
			element.setAttribute('aria-pressed', 'false')
		})
		
		btnToActive.classList.add('active')
		btnToActive.setAttribute('aria-pressed', 'true')
		activeThemeIcon.classList.remove(...activeThemeIcon.classList);
		activeThemeIcon.classList.add(svgOfActiveBtn.classList[0]);
		activeThemeIcon.classList.add('theme-icon-active');
		const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
		themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

		if (focus) {
			themeSwitcher.focus()
		}
	}

	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
		const storedTheme = getStoredTheme()
		
		if (storedTheme !== 'light' && storedTheme !== 'dark') {
			setTheme(getPreferredTheme())
		}
	})

	window.addEventListener('DOMContentLoaded', () => {
		showActiveTheme(getPreferredTheme())

		document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
			toggle.addEventListener('click', () => {
				const theme = toggle.getAttribute('data-bs-theme-value')
				setStoredTheme(theme)
				setTheme(theme)
				showActiveTheme(theme, true)
			})
		})
	})
})()



(function($) {
	$('.collapse').on('show.bs.collapse, shown.bs.collapse', function(event) {
		$('.material-icons[data-toggle="collapse"][data-target="#' + $(this).attr('id') + '"], [data-toggle="collapse"][data-target="#' + $(this).attr('id') + '"] .material-icons').text('arrow_drop_up');
	});
	
	$('.collapse').on('hide.bs.collapse, hidden.bs.collapse', function(event) {
		$('.material-icons[data-toggle="collapse"][data-target="#' + $(this).attr('id') + '"], [data-toggle="collapse"][data-target="#' + $(this).attr('id') + '"] .material-icons').text('arrow_drop_down');
	});
	
	$('.ajax').submit(function(event) {
		event.preventDefault();
		
		let form = $(this);
		
		$.ajax({
			type:	'POST',
			url:	form.attr('action'),
			data:	form.serialize(),
			success: function onSuccess(response) {
				if(response.toLowerCase() === 'true') {
					form.closest('.modal').modal('hide');
					window.location.reload();
					return;
				} else if(response.toLowerCase() === 'false') {
					response = 'An unknown error has occurred.';
				}
				
				let content = form.closest('.modal').find('.modal-body');
				content.find('.alert').remove();
				content.prepend('<div class="alert alert-danger" role="alert">' + response + '</div>');
			}
		});
		
		return false;
	});
	
	$('[data-toggle="tooltip"]').tooltip();
	
	$('[data-toggle="hover"]').popover({
		trigger:	'hover',
		html:		true
	});
	
	$('[data-confirm]').on('mousedown', function(event) {
		let target	= $(this);
		let popup	= $('#confirmation');
		let prevent	= true;
		
		$('.modal-body, p', popup).remove();
		$('.modal-footer', popup).addClass('text-center');
		$('.modal-footer', popup).css('justify-content', 'center');
		$('<p class="m-0 text-center p-3">' + target.data('confirm') + '</p>').insertAfter($('.modal-header', popup));
		$('<p class="modal-body d-none">' + target.data('confirm') + '</p>').insertAfter($('.modal-header', popup));
		popup.modal('show');
		
		let _watcher = setInterval(function() {
			var state = $('.alert', popup).text();
			
			if(state === 'CONFIRMED') {
				clearInterval(_watcher);
				
				if(target.prop('tagName') == 'A') {
					window.location.href = target.attr('href');
				} else {
					target.trigger('click');
				}
			}
		}, 500);
		
		popup.on('hide.bs.modal', function(event) {
			clearInterval(_watcher);
		});
		
		if(prevent) {
			event.preventDefault();
			return false;
		}
	});

	$('.sortable').sortable({
		appendTo:				'parent',
		cursor:					'move',
		handle:					'.moveable',
		dropOnEmpty:			false,
		forceHelperSize:		false,
		forcePlaceholderSize:	false,
		helper:					'original',
		items:					'> li',
		placeholder: {
			element: function(currentItem) {
				return $('<div class="sortable-placeholder"><div class="card m-2">Drop here</div></div>')[0];
			},
			update: function(container, p) {
				return;
			}
		},
		scroll:					false,
		beforeStop:				function onBeforeClose(event, ui) {
			$('body').removeClass('sortable');
		},
		start:					function onStart(event, ui) {
			$('body').addClass('sortable');
			ui.placeholder.addClass('col-6');
		}
	});
}(jQuery));