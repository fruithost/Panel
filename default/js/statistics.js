const Statistics = (function Statistics() {
	this.element	= null;
	this.canvas		= null;
	this.context	= null;
	this.stopped	= false;
	this.interval	= null;
	this.position	= 0;
	this.data		= [ 0.00 ];
	this.grid		= [ 0, 0, 0, 0.5 ];
	this.colors		= [ [ 0, 0, 0, 0] ];
	
	this.__constructor = function __constructor(element) {
		this.element	= document.querySelector(element);
		this.canvas		= document.createElement('canvas');
		this.context	= this.canvas.getContext('2d');
		
		window.addEventListener("resize", this.resize.bind(this));
		this.resize();
	};
	
	this.setColor = function setColor(color) {
		this.grid = color;
	};
	
	this.start = function start() {
		this.stopped = false;
	};
	
	this.stop = function stop() {
		this.stopped = true;
	};
	
	this.resize = function resize() {
		this.canvas.width	= (this.element.parentNode.innerWidth || this.element.parentNode.clientWidth || this.element.parentNode.clientWidth);
		this.canvas.height	= 100;
		
		this.draw();
	};
	
	this.add = function add(value, color) {
		this.colors.push(color);
		this.data.push(value);
	};
	
	this.draw = function draw() {
		this.context.save();
		this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
		this.context.restore();
		
		let bw	= this.canvas.width - this.position;
		let bh	= this.canvas.height;
		let c	= 4;
		let d	= 1;
		
		/* Vertical Lines */
		this.context.save();
		this.context.fillStyle = 'rgba(' + this.grid.join(',') + ')';
		this.context.strokeStyle = 'rgba(' + this.grid.join(',') + ')';
		this.context.beginPath();
		
		for(let x = 0; x <= bw; x += 40) {
			this.context.moveTo(x + this.position, c);
			this.context.lineTo(x + this.position, bh + c);
		}
		
		this.context.closePath();
		this.context.stroke();
		this.context.fill();
		this.context.restore();
		
		/* Horizontal Lines */
		this.context.save();
		this.context.fillStyle = 'rgba(' + this.grid.join(',') + ')';
		this.context.strokeStyle = 'rgba(' + this.grid.join(',') + ')';
		this.context.beginPath();
		
		for(let x = 0; x <= bh + d; x += (15 + d)) {
			this.context.moveTo(this.position, x + c);
			this.context.lineTo(bw + this.position, + x + c);
		}
		this.context.closePath();
		this.context.fill();
		this.context.stroke();
		this.context.restore();
		
		/* Data */
		this.context.save();
		this.context.beginPath();
		
		for(let i = 1; i < this.data.length; i++) {
			const height	= (this.canvas.height * this.data[i]) / 100;
			const width 	= 40 + c + d;
			const x			= this.canvas.width - (i * width);
			const y			= this.canvas.height - height;
			
			this.context.fillStyle		= 'rgba(' + this.colors[i].join(',') + ')';
			this.context.strokeStyle	= 'rgba(' + this.colors[i].join(',') + ')';
			this.context.fillRect(x, y, width, height);
		}
		
		this.context.closePath();
		this.context.restore();
		
		window.requestAnimationFrame(this.draw.bind(this));
	};
	
	this.render = function render(interval) {
		this.element.appendChild(this.canvas);
		
		setInterval(function() {
			if(this.stopped) {
				return;
			}
			
			this.position -= 5;			
		}.bind(this), interval);
		
		this.draw();
	};
	
	this.__constructor.apply(this, arguments);
});