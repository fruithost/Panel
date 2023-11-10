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