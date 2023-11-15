(() => {
	function show(event) {
		[].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function(icon) {
			icon.classList.remove('bi-caret-up-square-fill');
			icon.classList.remove('bi-caret-down-square-fill');
			icon.classList.add('bi-caret-up-square-fill');
		});
	};

	function hide(event) {
		[].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function(icon) {
			icon.classList.remove('bi-caret-up-square-fill');
			icon.classList.remove('bi-caret-down-square-fill');
			icon.classList.add('bi-caret-down-square-fill');
		});
	};

	[].map.call(document.querySelectorAll('.collapse'), function(collapse) {
		collapse.addEventListener('show.bs.collapse', show);
		collapse.addEventListener('shown.bs.collapse', show);
		collapse.addEventListener('hide.bs.collapse', hide);
		collapse.addEventListener('hidden.bs.collapse', hide);
	});
		
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
	
	const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"], [data-bs-toggle="hover"]')
	const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl, {
		trigger:	'hover',
		html:		true
	}))
	
	function serialize(form) {
		var obj = {};
		var formData = new FormData(form);
		for (var key of formData.keys()) {
			obj[key] = formData.get(key);
		}
		return obj;
	};

	function ajaxSubmit(event) {
		event.preventDefault();
		
		try {
			let form = this;
			
			// @ToDo add onError?
			
			new Ajax(form.action).onSuccess(function(response) {
				if(response.toLowerCase() === 'true' || response.toLowerCase() === '1') {
					//form.closest('.modal').modal('hide');
					window.location.reload();
					return;
				} else if(response.toLowerCase() === 'false') {
					response = 'An unknown error has occurred.';
				}
				
				let content	= form.querySelector('.modal-body');
				let alert	= content.querySelector('.alert');
				
				if(alert) {
					content.removeChild(alert);
				}
				
				alert = document.createElement('div');
				alert.classList.add('alert');
				alert.classList.add('alert-danger');
				alert.setAttribute('role', 'alert');
				alert.innerHTML = response;
				content.prepend(alert);
			}).post(serialize(form));
		} catch(e) {
			
		}
		
		return false;
	};
	
	[].map.call(document.querySelectorAll('.ajax'), function(form) {
		/*form.addEventListener('click', function(event) {
			ajaxSubmit.apply(form, [ event ]);
		});*/
		
		form.addEventListener('submit', function(event) {
			ajaxSubmit.apply(form, [ event ]);
		});
	});
})();


/*

(function($) {
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
}(jQuery));*/