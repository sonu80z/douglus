/*
 * CFX plugin (Canvas FX library)
 * Author: Jesse Chrestler
 */

var _debug = true;
function getDateDisplay(){
	var now = new Date();
	var hr = (now.getHours() > 12) ? now.getHours() - 12 : now.getHours();
	hr = (hr < 10) ? "0" + hr : hr;
	var min = (now.getMinutes() < 10) ? "0" + now.getMinutes() : now.getMinutes();
	var sec = (now.getSeconds() < 10) ? "0" + now.getSeconds() : now.getSeconds();
	return "[" + hr + ":" + min + ":" + sec + "]";
}
var debug = {
	coords:function(coords){
		if(_debug){
			//console.log.apply(console, arguments);
			$(".debug-coords").empty();
			var html = "<table>";
			html += "<tr><th style='width:100px;'>field</th><th>value</th></tr>";
			for(i in coords){
				if(i != "stage")
				html += "<tr><td>" + i + "</td><td>" + coords[i] + "</td></tr>";
				else
					for(j in coords[i])
						html += "<tr><td>" + i + "." + j + "</td><td>" + coords[i][j] + "</td></tr>";
			}
			html += "</table>";

			$(".debug-coords").append("<li style='color:white;'>" + getDateDisplay() + html + "</li>");
		}
	},
	center:function(center){
		if(_debug){
			//console.log.apply(console, arguments);
			$(".debug-center-calc").empty();
			var html = "<table>";
			html += "<tr><th style='width:100px;'>field</th><th>value</th></tr>";
			for(i in center){
				html += "<tr><td>" + i + "</td><td>" + center[i] + "</td></tr>";
			}
			html += "</table>";

			$(".debug-center-calc").append("<li style='color:white;'>" + getDateDisplay() + html + "</li>");
		}
	},
	log:function()
	{
		if(_debug){
			//console.log.apply(console, arguments);
			$(".debug-log").prepend("<li style='color:white;'>" + getDateDisplay() + Array.prototype.join.call(arguments, " ") + "</li>");
		}
	},
	mobile:function()
	{
		if(_debug){
			$(".debug-actions").prepend("<li style='color:white;'>"+ getDateDisplay() +  Array.prototype.join.call(arguments, " ") + "</li>");

		}
	}
};
/**
 * Detecting vertical squash in loaded image.
 * Fixes a bug which squash image vertically while drawing into canvas for some images.
 * This is a bug in iOS6 devices. This function from https://github.com/stomita/ios-imagefile-megapixel
 * 
 */
function detectVerticalSquash(img) {
    var iw = img.naturalWidth, ih = img.naturalHeight;
    var canvas = document.createElement('canvas');
    canvas.width = 1;
    canvas.height = ih;
    var ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0);
    var ratio = 0;
	var data = ctx.getImageData(0, 0, 1, ih).data;
	// search image edge pixel position in case it is squashed vertically.
	var sy = 0;
	var ey = ih;
	var py = ih;
	while (py > sy) {
		var alpha = data[(py - 1) * 4 + 3];
		if (alpha === 0) {
			ey = py;
		} else {
			sy = py;
		}
		py = (ey + sy) >> 1;
	}
	ratio = (py / ih);
    return (ratio===0)?1:ratio;
}

/**
 * A replacement for context.drawImage
 * (args are for source and destination).
 */
function drawImageIOSFix(ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) {
    var vertSquashRatio = detectVerticalSquash(img);
 // Works only if whole image is displayed:
 // ctx.drawImage(img, sx, sy, sw, sh, dx, dy, dw, dh / vertSquashRatio);
 // The following works correct also when only a part of the image is displayed:
    ctx.drawImage(img, sx * vertSquashRatio, sy * vertSquashRatio, 
                       sw * vertSquashRatio, sh * vertSquashRatio, 
                       dx, dy, dw, dh );
}	
(function($, window){


	//identify if device is mobile or not.
	function isMobile(){
		return /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
	}
	
	var cfxEvents = {
		imageSet : "imageset.cfx",
		imageLoaded : "imageLoaded.cfx",
		imageCentered : "imageCentered.cfx",
		imageReady : "imageReady.cfx",
		imageReset : "imageReset.cfx",
		imageError : "imageError.cfx"
	}
	var cfxObject =  function(name){
		this.name = name;
		this.reset();
		this.children = [];
	}
	$.extend(cfxObject.prototype, {
		addChild:function(obj){
			this.children.push(obj);
		},
		getChildren:function(){
			return this.children;
		},
		reset:function(){
			this.position = { x:0, y:0 };
			this.offset = { x:0, y:0 };
			this.flip = { v:1, h:1 };
			this.scale = { x:1, y:1 };
			this.width = 0;
			this.height = 0;
			this.style = {strokeStyle:"#FFFFFF", fillStyle:"#FFFFFF", lineWidth:"1px", font:"normal 10px Arial"}
			this.zoom = 0;
			this.rotation = 0;
			this.visible = true;
			this.children = [];
			this.effects = {};
		},
		draw:function(context){
			//left blank intentionally
		},
		render:function(context){
			//left blank intentionally
		},
		applyPosition:function(context){
			context.translate(this.position.x, this.position.y);
		},
		applyOffset:function(context){
			context.translate(this.offset.x, this.offset.y);
		},
		applyFlip:function(context){
			context.scale(this.flip.h, this.flip.v);
		},
		applyScale:function(context){
			context.scale(this.scale.x, this.scale.y);
		},
		applyRotation:function(context){
			context.rotate(this.rotation * (Math.PI/180));
		},
		applyTranslation:function(context){
			//set position
			this.applyPosition(context);
			//apply offset
			this.applyOffset(context);
		},
		applyTransformation:function(context){
			this.applyTranslation(context);
			//apply rotation
			this.applyRotation(context)
			//apply scaling / flipping
			this.applyScale(context);
			this.applyFlip(context);
		}
	});

	var cfxContainer = function(name){
		this.name = name;
		cfxObject.call(this, name);
	}
	cfxContainer.prototype = Object.create(cfxObject.prototype);
	$.extend(cfxContainer.prototype, {
		render:function(context){
			this.applyTransformation(context);
			context.beginPath();
			context.strokeStyle = this.style.strokeStyle;
			context.lineWidth=this.style.lineWidth;
			context.rect(0, 0,this.width,this.height);
			context.stroke();
		}
	});

	var cfxAnnotation = function(cfxImg, start, end){
		this.cfxImg = cfxImg;
		this.start = start;
		this.end = end;
		cfxObject.call(this, "Annotation");
		this.style.font = "bold 10px Arial";
		this.style.fillStyle = "#FFDD00";
		this.style.strokeStyle = "#FFDD00";
	}
	cfxAnnotation.prototype = Object.create(cfxObject.prototype);

	$.extend(cfxAnnotation.prototype, {
		text:function(context, x, y, font, style, text){
			context.font = font;
			context.fillStyle = style;
			context.fillText(text, x, y);
			return this;
		},
		getTextPosition:function(){
			var diffX = Math.abs(this.start.x-this.end.x)/2;
			var diffY = Math.abs(this.start.y-this.end.y)/2;
			var posX = this.start.x;
			var posY = this.start.y;
			var aboveX = true;
			var aboveY = true;
			if(this.start.x > this.end.x){
				posX = this.end.x;
				aboveX = false;
			}
			if(this.start.y > this.end.y){
				posY =this.end.y;
				aboveY = false;
			}
			//fine tuning the text around the line.
			switch(Math.abs(this.cfxImg.rotation)){
				case 0:{
					if(aboveX){
						posX += 2;
					}else{
						posX -= 35;
					}
					if(aboveY){
						posY -= 5
					}else{
						posY += 10
					}
				break;
				}
				case 90:{

				break;
				}
				case 180:{
				break;
				}
				case 270:{
				break;
				}

			}
			return {pos:{x:posX, y:posY}, diff:{x:diffX, y:diffY}};
		},
		getMeasurement:function(amt){
			return Math.round((amt*.026458) / this.cfxImg.zoom * 100) / 100;
		},
		annotate:function(context){
			context.beginPath();
			context.moveTo(this.start.x, this.start.y);
			context.strokeStyle=this.style.strokeStyle;
			context.lineTo(this.end.x, this.end.y);
			context.stroke();
			return this;
		},
		draw:function(context){
			context.save();
			this.annotate(context);
			var loc = this.getTextPosition();
			this.text(context,loc.pos.x + loc.diff.x, loc.pos.y + loc.diff.y,this.style.font,this.style.fillStyle,(this.getMeasurement(loc.diff.x+loc.diff.y))+"cm");
			context.restore();
			return this;
		},
		render:function(context){
			this.applyTransformation(context);
			this.annotate(context);
			context.save();
			var loc = this.getTextPosition();
			context.translate(loc.pos.x + loc.diff.x, loc.pos.y + loc.diff.y);
			//inverse the current flip
			var rotation = Math.abs(this.rotation);
			var imgRotation = Math.abs(this.cfxImg.rotation);
			var v = this.flip.v;
			var h = this.flip.h;
			var imgHasRotation = (imgRotation == 90 || imgRotation == 270) 
			var imgIsFlipped = ((this.cfxImg.flip.h == -1 && this.cfxImg.flip.v == 1) || (this.cfxImg.flip.h == 1 && this.cfxImg.flip.v == -1));
			var annotationHasRotation = (rotation == 90 || rotation == 270);
			var annotationIsFlipped = ((this.flip.h == -1 && this.flip.v == 1) || (this.flip.h == 1 && this.flip.v == -1));
			if(imgHasRotation && imgIsFlipped){
				if(annotationHasRotation){
					console.log("fired1")
					v = -this.flip.v ;		
					h = -this.flip.h ;
					if(annotationIsFlipped){
						console.log("fired2")
						v = this.flip.h;
						h = this.flip.v;
					}
				}
			}else{
				if(annotationHasRotation && imgIsFlipped){
					console.log("fired")
						v = -this.flip.v ;		
						h = -this.flip.h ;
				}
			}
			
			
			context.scale(this.cfxImg.flip.h*h, this.cfxImg.flip.v*v);
			//inverse the current rotation
			context.rotate((-this.cfxImg.rotation-this.rotation) * (Math.PI/180));
			//now the tricky part. how do we keep the text upside right. :)
			this.text(context, 0, 0,this.style.font,this.style.fillStyle,(this.getMeasurement(loc.diff.x+loc.diff.y))+"cm");
			context.restore();
			return this;
		}
	});
	//defines the default cfxData model
	var cfxImage = function(img, canvas){
		debug.log("fetching image " + img);
		if(img.nodeName !== "IMG"){
			this.load(img);//we assume we are given a string for the url.
		}else{
			this.img = img;
			$(this.canvas).trigger(cfxEvents.imageLoaded, {cfxImg:this, image:img});
		}

		this.canvas = canvas;
		cfxObject.call(this, "Image");
	}
	cfxImage.prototype = Object.create(cfxObject.prototype);
	//cfxImage methods
	$.extend(cfxImage.prototype, {
		applyScale:function(context){
			if(this.zoom != this.currentZoom){
				this.scale.x = this.currentZoom / this.zoom;
				this.scale.y = this.currentZoom / this.zoom;
			}

			context.scale(this.scale.x, this.scale.y);
		},
		getCenter: function(){
			var canvasWidth = this.canvas.width;
			var canvasHeight = this.canvas.height;
			var wc = 0;
			var hc = 0;
			var newWidth = 0;
			var newHeight = 0;
			var wratio = canvasWidth / this.img.width;
			var hratio = canvasHeight / this.img.height;


			var centerdata = {
				canvasWidth:canvasWidth,
				canvasHeight:canvasHeight,
				wratio:wratio,
				hratio:hratio
			};


			if(wratio < hratio){
				if(wratio > 1)  wratio = 1;
				newHeight = this.img.height * wratio;
				newWidth = this.img.width;
				if(newHeight == 0) newHeight = this.img.height;
				hc = (this.canvas.height - newHeight) / 2;
				percent = wratio;
				//debug.log("wdiff wratio " + percent, w, h, this.img.width, this.img.height);

			}else{
				if(hratio > 1) hratio = 1;
				newWidth = this.img.width * hratio;
				newHeight = this.img.height;
				if(newWidth == 0) newWidth = this.img.width;
				wc = (this.canvas.width - newWidth) / 2;
				percent = hratio;
				//debug.log("hdiff hratio " + percent, w, h, this.img.width, this.img.height);
			}
			
			this.width = this.img.width * percent;
			this.height = this.img.height * percent;

			centerdata.imgWidth = this.img.width;
			centerdata.imgHeight = this.img.height;
			centerdata.imgAdjustedWidth = this.width;
			centerdata.imgAdjustedHeight = this.height;
			centerdata.imgCenterHeight = hc;
			centerdata.imgCenterWidth = wc;
			debug.center(centerdata);
			return {
				start : {
					width: wc,
					height: hc
				},
				end : {
					width: newWidth,
					height: newHeight
				},
				percent: percent * 100
			};
		},
		load:function(url){
			var $this = this;
			var img = new Image();
			debug.log("trying to load image " + url);
			img.onerror = function(e){
				$($this.canvas).trigger(cfxEvents.imageError, {eventData:e, cfxImg:$this, image:img});
				debug.log('img.onerror', e)
			}
	 		img.onload = function(e){
	 			debug.log('img.onload - finished loading ' + img.src)
	 			$($this.canvas).trigger(cfxEvents.imageLoaded, {eventData:e, cfxImg:$this, image:img});
	 		}
	 		img.src = url;
	 		//assign the new image
	 		this.img = img;
		},
		reset:function(){
			cfxObject.prototype.reset.call(this);
			this.zoom = this.getCenter().percent/100;
			this.window = 250;
			this.level = 250;
			this.effects = {"applyWindowLevel":[this.window ,this.level]};
			this.position = {x:this.canvas.width/2, y:this.canvas.height/2};
			this.currentZoom = this.zoom;

		},
		render:function(context){
			this.applyTransformation(context);
			this.zoom = this.getCenter().percent/100;
			var w = this.img.width * this.zoom;
			var h = this.img.height * this.zoom;
			var diffX = this.canvas.width - w;
			var diffY = this.canvas.height - h;
			debug.log("rendering image w:" + w + " h:" + h  + " src:" + this.img.src);
			//context.drawImage(this.img, -(w/2), -(h/2), w, h);
			//ios has a bug so we must use this.. gotta love apple.. sigh.
			drawImageIOSFix(context, this.img, 0, 0, this.img.width, this.img.height, -(w/2), -(h/2), w, h);
		}
	});
	


	//defines the main class to interact with canvas
	var cfxCanvas = function(canvas){
		this.canvas = canvas;
		this.loadedImages = 0;
		this.stage = new cfxContainer("Stage Object");
		this.stage.width = this.canvas.width;
		this.stage.height = this.canvas.height;
		this.images = [];
		this.imageIndex = -1;
		this.playing = false;
		this.playSpeed = 30;

		var $this = this;
		$(canvas).on(cfxEvents.imageLoaded, function(e, params){

			$this.loadedImages++;
			//whenever an image is loaded.. add the proper data to that image.
			var cfxImg = params.cfxImg;
			var pos = cfxImg.getCenter();
			cfxImg.zoom = pos.percent / 100;
 			cfxImg.width = cfxImg.zoom * cfxImg.img.width;
 			cfxImg.height = cfxImg.zoom * cfxImg.img.height;
 			var hw = cfxImg.width/2;
			var hh = cfxImg.height/2;

	 		if($this.loadedImages == $this.images.length){
	 			$(canvas).trigger(cfxEvents.imageReady, params);
	 			if($this.imageIndex == -1)
	 				$this.setDisplayedImage(0);
 			}
		});
	}
	//cfx methods
	$.extend(cfxCanvas.prototype, {
		context:function(createNew){
			if(this._context == undefined || createNew)
				this._context = this.canvas.getContext("2d");
			return this._context;
		},
		createImageData: function(w, h){
			w = w || this.canvas.width;
			h= h || this.canvas.height;
			return this.context().createImageData(w, h);
		},
		setDimension: function(w, h){
			this.canvas.width = w;
			this.canvas.height = h;
			return this;
		},
		getImageData: function(w1, h1, w2, h2){
			w1 = w1 || 0;
			h1 = h1 || 0;
			w2 = w2 || this.canvas.width;
			h2 = h2 || this.canvas.height;
			return this.context().getImageData(w1, h1, w2, h2);
		},
		setImageData: function(imageData){
			this.context().putImageData(imageData, 0, 0);
			return this;
		},
		drawImage:function(){
			this.context().drawImage.apply(this.context(), arguments);
			return this;
		},
		addImage:function(image){
			debug.log("adding image to stage " + image);
			var cfxImg = new cfxImage(image, this.canvas);
			this.images.push(cfxImg);
			cfxImg.visible = false;
			var center  = new cfxContainer("Center");
			center.height = 20;
			center.width = 20;
			center.style.strokeStyle = "#FF0000";
			center.offset.x = -10;
			center.offset.y = -10;
			center.visible = false;
			cfxImg.addChild(center);
			this.stage.addChild(cfxImg);
			return this;
		},
		addImages:function(images){
			for(var i = 0; i < images.length; i++){
				this.addImage(images[i]);
			}
			return this;
		},
		setDisplayedImage:function(index){
			if(this.imageIndex != -1)
			this.images[this.imageIndex].visible = false;
			this.imageIndex = index;
			this.images[this.imageIndex].visible = true;
			this.updateCanvas();
			return this;
		},
		nextImage:function(){
			var index = this.imageIndex + 1;
			if(index > this.images.length-1)
				index = 0;
			this.setDisplayedImage(index);
		},
		prevImage:function(){
			var index = this.imageIndex - 1;
			if(index < 0)
				index = this.images.length-1;
			this.setDisplayedImage(index);
		},
		clear:function(){
			this.context().rect(0, 0, this.canvas.width, this.canvas.height);
			this.context().fillStyle = "black";
			this.context().fill();
			return this;
		},
		clearImages:function(){
			this.images = [];
			this.loadedImages = 0;
			this.stage.children = [];
			return this;
		},
		getCurrentImage: function(){
			return this.images[this.imageIndex];
		},
		rotate:function(angle){
			var cfxImg = this.getCurrentImage();
			cfxImg.rotation += angle;
			cfxImg.rotation = cfxImg.rotation % 360;
			this.updateCanvas();
			return this;
		},
		flip:function(v, h){
			var cfxImg = this.getCurrentImage();
			cfxImg.flip = {v:v, h:h};
			this.updateCanvas();
			return this;
		},
		vflip:function(){
			var cfxImg = this.getCurrentImage();
			if(cfxImg.flip.v > 0)
				this.flip(-1, cfxImg.flip.h);
			else
				this.flip(1, cfxImg.flip.h);	
			return this;	
		},
		hflip:function(){
			var cfxImg = this.getCurrentImage();
			if(cfxImg.flip.h > 0 )
				this.flip(cfxImg.flip.v, -1);	
			else
				this.flip(cfxImg.flip.v, 1);
			return this;
		},
		setPan:function(x, y){
			var cfxImg = this.getCurrentImage();
			cfxImg.offset.x += x * cfxImg.scale.x;
			cfxImg.offset.y += y * cfxImg.scale.y;
			this.updateCanvas();
			return this;
		},
		setWindowLevel:function(w, l){
			var cfxImg = this.getCurrentImage();
			cfxImg.window += w;
			cfxImg.level += l;
			if(cfxImg.window < 1)
				cfxImg.window = 1;
			cfxImg.effects["applyWindowLevel"] = [cfxImg.window, cfxImg.level];
			this.updateCanvas();
		},
		applyAnnotation:function(start, end){
			var cfxImg = this.getCurrentImage();
			var annotation = new cfxAnnotation(cfxImg, start,end);
			this.context().save()
			cfxImg.applyPosition(this.context());
			cfxImg.applyScale(this.context());
			annotation.draw(this.context());
			this.context().restore();
		},
		applyWindowLevel:function(w, l){
			var cfxImg = this.getCurrentImage();
			var original = this.getImageData();
			var src = original.data;

			cfxImg.modified = this.createImageData();
			var dest = cfxImg.modified.data;
			var lower_bound = (l - w)/2.0;
			var upper_bound = (l + w)/2.0;
			
			for (var i=0; i<dest.length; i+=4) {
				var intensity = (src[i] - lower_bound)/(upper_bound - lower_bound);
				 if(intensity < 0.0)
			        intensity = 0.0;
			    if(intensity > 1.0)
			        intensity = 1.0;
			    intensity *= 255.0;
			     var rounded_intensity = Math.round(intensity);
			    dest[i] = rounded_intensity;
			    dest[i+1] = rounded_intensity;
			    dest[i+2] = rounded_intensity;
			    dest[i+3] = 0xFF;
			}
			this.setImageData(cfxImg.modified);
		},
		setSharpness:function(a){
			var cfxImg = this.getCurrentImage();
			cfxImg.effects["applySharpness"] = arguments;
			this.updateCanvas();
		},
		applySharpness:function(a){
			var matrix = [
					   0, -a,  0,
					  -a,  a*4+1, -a,
					   0, -a,  0
					   ];

			this.convolute(matrix);
			
		},
		//convolute image
	   convolute: function(matrix){
			//fill new empty image with new values.
			var newImage = this.createImageData(); //add call to create blank image
			var image = this.getImageData();
			var src = image.data;
			var dest = newImage.data; 
			var side = Math.round(Math.sqrt(matrix.length));
			var halfSide = Math.floor(side/2);
			var h = image.height;
			var w = image.width;
			var alphaFac = 1;
			for (var y=0; y<h; y++) {
				for (var x=0; x<w; x++) {
					var sy = y;
					var sx = x;
					var dstOff = (y*w+x)*4;
					var r=0, g=0, b=0, a=0;
					for (var cy=0; cy<side; cy++) {
						for (var cx=0; cx<side; cx++) {
							var scy = sy + cy - halfSide;
							var scx = sx + cx - halfSide;
							if (scy >= 0 && scy < h && scx >= 0 && scx < w) {
								var srcOff = (scy*w+scx)*4;
								var wt = matrix[cy*side+cx];
								r += src[srcOff] * wt;
								g += src[srcOff+1] * wt;
								b += src[srcOff+2] * wt;
								a += src[srcOff+3] * wt;
							}
						}
					}
					dest[dstOff] = r;
					dest[dstOff+1] = g;
					dest[dstOff+2] = b;
					dest[dstOff+3] = a + alphaFac*(255-a);
				}
			}
			return this.setImageData(newImage);
		},
		addAnnotation:function(start, end){
			if(start && end){
				debug.log("adding annotation start.x:" + start.x + " start.y:" + start.y + " end.y:" + end.y + " end.x:" + end.x)
				var cfxImg = this.getCurrentImage();
				var annotation = new cfxAnnotation(cfxImg, start, end);
				annotation.rotation = 360-cfxImg.rotation;
				annotation.flip.v = cfxImg.flip.v;
				annotation.flip.h = cfxImg.flip.h;
				annotation.offset.x = -cfxImg.offset.x / cfxImg.scale.x * cfxImg.flip.h; 
				annotation.offset.y = -cfxImg.offset.y / cfxImg.scale.y * cfxImg.flip.v;
				switch(Math.abs(cfxImg.rotation)){
					case 90:{
						var v = annotation.flip.v;
						annotation.flip.v = annotation.flip.h;
						annotation.flip.h = v;
						var x = annotation.offset.x;
						annotation.offset.x = annotation.offset.y * annotation.flip.v * annotation.flip.h;
						annotation.offset.y = -x * annotation.flip.v * annotation.flip.h;
						break;
					}
					case 180:{
						annotation.offset.y = -annotation.offset.y;
						annotation.offset.x = -annotation.offset.x;
						break;
					}
					case 270:{
						var v = annotation.flip.v;
						annotation.flip.v = annotation.flip.h;
						annotation.flip.h = v;
						var x = annotation.offset.x;
						annotation.offset.x = -annotation.offset.y * annotation.flip.v * annotation.flip.h;
						annotation.offset.y = x * annotation.flip.v * annotation.flip.h;
						break;
					}
				}
				cfxImg.addChild(annotation);
				this.updateCanvas();
			}
		},
		zoom:function(amt){
			var cfxImg = this.getCurrentImage();
			
			var ratio = (1.0-cfxImg.zoom);
			var scale = amt / cfxImg.zoom;

			cfxImg.scale.x = scale;
			cfxImg.scale.y = scale;

			debug.log("applying zoom to image  [scale:" + scale +", amt:"+amt+", imgZoom:"+cfxImg.zoom+"] (zoom:" + cfxImg.zoom + ") * (scale:" + cfxImg.scale.x + ")"); 
			cfxImg.currentZoom = cfxImg.zoom * cfxImg.scale.x;
			
			this.updateCanvas();
			return this;
		},
		reset:function(){
			var cfxImg = this.getCurrentImage();
			cfxImg.reset();
			this.updateCanvas();
			$(this.canvas).trigger(cfxEvents.imageReset);
		},
		resetAnnotations:function(){
			var cfxImg = this.getCurrentImage();
			cfxImg.children = [];
			this.updateCanvas();
		},
		renderObject:function(obj){
			if(obj.visible){
				//saves current state
				this.context().save();

				obj.render(this.context())
				
				for(var name in obj.effects){
					this[name].apply(this, obj.effects[name]);
				}
				var children = obj.getChildren();
				//render all children.
				for(var i = 0; i < children.length;i++){
					this.renderObject(children[i]);
				}
				//restores state before anything was moved/scaled/rotated (matrix is back to normal)
				this.context().restore();
			}
			return this;
		},
		updateCanvas:function(){
			var cfxImg = this.getCurrentImage();
			//draws black to the screen (blanks it out)
			this.clear();

			this.renderObject(this.stage);

			return this;
		},
		updateCanvasSize:function(){
			for(var i = 0; i < this.images.length;i++){
				this.images[i].reset();
				if(this.imageIndex == i)
					this.images[i].visible = true;
				else{
					this.images[i].visible = false;
				}

			}
			this.stage.width = this.canvas.width;
			this.stage.height = this.canvas.height;
		},
		play:function(speed){
			this.playSpeed = speed;
			if(!this.playing){
				this.playing = true;
				this.continue();
			}
			return this;
		},
		continue:function(){
			var $this = this;
			setTimeout(function(){
				if($this.playing){
					$this.nextImage.call($this);
					$this.continue.call($this, this.playSpeed);
				}
			}, 1000/this.playSpeed);
		},
		stop:function(speed){
			this.playing = false;
			return this;
		},

	});

	//expose publically
	window.cfxCanvas = cfxCanvas;
})(jQuery, window);