const AjaxState = {
	NOT_INITALIZED:				0,
	CONNECTION_ESTABLISHED:		1,
	REQUEST_RECEIVED:			2,
	PROCESSING:					3,
	FINISHED:					4
};

const Ajax = function() {
	let socket;
	let url;
	let callbacks = {
		success:	[],
		error:		[]
	};
	
	this.__construct = function __construct(url) {
		this.socket						= new XMLHttpRequest();
		this.url						= url;
		this.socket.onreadystatechange	= this.handle.bind(this);
	};
	
	this.handle = function handle(event) {
		switch(this.socket.readyState) {
			case AjaxState.NOT_INITALIZED:
			case AjaxState.CONNECTION_ESTABLISHED:
			case AjaxState.REQUEST_RECEIVED:
			case AjaxState.PROCESSING:
				/* Do currently Nohting */
			break;
			case AjaxState.FINISHED:
				if(this.socket.readyState == 4 && this.socket.status == 200) {
					try {
						let json = JSON.parse(event.target.responseText);
						
						callbacks.success.forEach(function onSuccessCallback(callback) {
							callback.apply(this, [ json ]);
						}.bind(this));
					} catch(exception) {
						callbacks.success.forEach(function onSuccessCallback(callback) {
							callback.apply(this, [ event.target.responseText ]);
						}.bind(this));
					}
				} else {
					callbacks.error.forEach(function onErrorCallback(callback) {
						callback.apply(this, [ event, 'Error', event.target.responseText ]);
					}.bind(this));
				}
			break;
		}
	};
	
	this.setHeaders = function setHeaders() {
		this.socket.setRequestHeader('X-Requested-With',	'XMLHttpRequest');
		this.socket.setRequestHeader('Content-type',		'application/x-www-form-urlencoded');
	};
	
	this.onError = function onError(callback) {
		callbacks.success.push(callback);
		
		return this;
	};
	
	this.onSuccess = function onSuccess(callback) {
		callbacks.success.push(callback);
		
		return this;
	};
	
	this.post =  function post(data) {
		this.socket.open('POST', this.url, true);
		this.setHeaders();
		
		this.socket.send((typeof(data) == 'string') ? data : Object.keys(data).map(function(key) {
			return encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
		}).join('&'));
		
		return this;
	};
	
	this.get = function get() {
		this.socket.open('GET', this.url, true);
		this.setHeaders();
		this.socket.send();
		
		return this;
	};
	
	this.__construct.apply(this, arguments);
};