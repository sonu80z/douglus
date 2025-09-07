

//authentication check..

$.ajax({url:"/system/authenticate.php", 
	type:"GET",  
	success:function(response){
		var output = response.replace(/[\r\n{}]+/g, "").split(",")[0];
		debug.log("user is authenticated == " + output);
		if(output === "success:false")
			window.location = rootURL;
	}
});
var ImagesRootPath = "dicom/";
//bind to all canvases - creating a new cfxCanvas
$(".viewer-layout canvas").each(function(){
	$(this).data('cfxCanvas', new cfxCanvas(this));
});

$(".viewer-layout canvas").on("imageLoaded.cfx", function(){
	var totalPercent = 24;
	var percent = 24;
	var loaded  = (cfxActiveCanvas.loadedImages == 0) ? 1 : cfxActiveCanvas.loadedImages;
	if(cfxActiveCanvas.images.length > 1)
		percent = loaded / cfxActiveCanvas.images.length * totalPercent;
	
	updateProgress("Added (" + loaded + " of " + cfxActiveCanvas.images.length + ")" , 75 + Math.round(percent));

});
$(".viewer-layout canvas").on("imageReady.cfx", function(){
	updateProgress("Complete", 100);
	$(".knob").val(0);
	setTimeout(function(){
		updateStudyInfo();
		updateHud();
		cfxActiveCanvas.setDisplayedImage(cfxActiveCanvas.imageIndex);
		updateCanvasSize();
		updateZoom();
		
	}, 0);
	
});

$(".viewer-layout canvas").on("imageError.cfx", function(evt, data){
	
	setMessage("Failed to load image. url:" + data.image.src, 0);
});
//don't touch .. resizing is a bit tricky.
updateCanvasSize();
setTimeout(function(){updateCanvasSize();}, 250);

function updateZoom(){
	var cfxImg = cfxActiveCanvas.getCurrentImage();
	$("#zoom-value").text(Math.round(cfxImg.currentZoom * 100) + "%");
	$("#zoom-slider").val(cfxImg.currentZoom * 100);
}
	


//store a reference to the active canvas (we will switch when we get to multiple windows)
var cfxActiveCanvas = $(".viewer-layout canvas.active").data("cfxCanvas");
//api calls
APIService.getStudyDetails(studies, function(patient){
	updateProgress("Processing Study", 25);
	$container = $(".series-info");
	$container.empty();
	data = patient;
	//set name info
	$(".patient-name").empty().append(patient.firstname + " " + patient.lastname);
	$(".patient-id").empty().append(patient.origid);
	$(".patient-dob").empty().append((new Date(patient.birthdate)).toDateFormat("{m}-{d}-{y}"));
	$(".patient-dos").empty().append((new Date(patient.studies[0].studydate)).toDateFormat("{m}-{d}-{y}"));
	var images = [];
	for(var i = 0; i < patient.studies.length;i++){
		var study = patient.studies[i];
		for(var j = 0; j < study.series.length;j++){
			var series = study.series[j];
			var addClass = ""
			if(j == 0)
				addClass = " active";
			$container.append('<li class="series-tab' + addClass + '"><a href="#" data-series-index="' + j + '"><i class="fa fa-picture-o"></i> Series ' + series.seriesnumber + '</a></li>');
			
			for(var k = 0; k < series.images.length;k++){
				var image = series.images[k];
				
				if(j==0){
					images.push(image.uuid);
				}
			}
		}
	}

	$(".series-tab").on("click", function(e){
		var index = $(this).find("[data-series-index]").data("series-index");
		var imageEntities = patient.studies[0].series[index].images;
		var seriesNumber = patient.studies[0].series[index].seriesnumber
		cfxActiveCanvas.seriesNumber = seriesNumber;
		images = [];
		for(var k = 0; k < imageEntities.length;k++){
			var image = imageEntities[k];
			images.push(image.uuid);
		}
		$(".series-tab.active").removeClass("active");
		$(this).addClass("active");
		updateProgress("Loading Images", 40);
		fetchImages(images);
		
	})
	//updateCanvasSize();
	fetchImages(images);
	updateProgress("Loading Images", 40);
	cfxActiveCanvas.seriesNumber = patient.studies[0].series[0].seriesnumber;
});

function setActiveCanvas(c){
	$(".viewer-layout canvas.active").removeClass("active");
	//this is when we know which canvas is active.
	$(c).addClass("active");
	cfxActiveCanvas = $(c).data('cfxCanvas');
	updateZoom();

}

//events
$(".study-info").on("click", updateStudyInfo);
var dragKey = -1;
var lastCoords = null;
var startCoords = null;
var effect = "Window/Level"
var events = {
	press : function(e){
		if(isMobile()){
			e.which= 1;
			e = e.originalEvent;
		}
		//debug.mobile("pressed key e.which = " + e.which);
		$(".status-value").text("start dragging");
		dragKey = e.which;
		$self = $(this);
		setActiveCanvas(this);
		startCoords = getCoords(e);

	},
	release : function(e){
		if(isMobile())
			e.which= 1;
		$(".status-value").text("stop dragging");
		dragKey = -1;
		//debug.mobile("released key e.which = " + e.which);
		if(effect == "Measure" && lastCoords && e.which == 1){
			cfxActiveCanvas.addAnnotation(startCoords, lastCoords.stage);
		}
		startCoords = null;
			lastCoords = null;
	},
	move : function(e){
		var self = this;
		var $self = $(this);
		
		if(dragKey != -1){
			
			var evt = e.originalEvent;
			
			var position = $self.position();
			var width = $self.width();
			var height = $self.height();
			if(evt.targetTouches){
				//touch device
				var x = evt.targetTouches[0].pageX - position.left
				var y = evt.targetTouches[0].pageY- position.top;
				var xDiff = 0, yDiff = 0;
				if(lastCoords != null){
					xDiff = x - lastCoords.x;
					yDiff = y - lastCoords.y;
				}
		    	coords = {
		    		which: dragKey,
					x: x,
					y: y,
					xDiff:xDiff,
					yDiff:yDiff,
					xWeight:x/width,
					yWeight:y/height,
					stage:getCoords(evt)
				}

			}else{
				//pc
				var x = evt.pageX - position.left
				var y = evt.pageY - position.top;
				var xDiff = 0, yDiff = 0;
				if(lastCoords != null){
					xDiff = x - lastCoords.x;
					yDiff = y - lastCoords.y;
				}
		    	coords = {
		    		which: dragKey,
					x: x,
					y: y,
					xDiff:xDiff,
					yDiff:yDiff,
					xWeight:x/width,
					yWeight:y/height,
					stage:getCoords(e)

			    };
			    
			}
			
			//debug.mobile("dragging dragkey = " + dragKey);
			if(dragKey != -1){
				debug.coords(coords);
				lastCoords = coords;
				//debug.mobile("dragging key e.which = " + dragKey);
				events.drag.call(this, e, coords);
				if(coords.length)
					coords = coords[0];
				var data = "<br/>";
				for(key in coords)
					data += key + ":" + coords[key] + "<br/>"
				$(".coords-value").html(data);
				$(".status-value").text("dragging");
			}
		}
	},
	drag:function(e, info){
		var self = this
		if(isMobile())
			info.which= 1;

		if(info.which == 2)
			e.preventDefault();
		if(info.which > 0 && info.which < 4){

			//dragging with primary.
			var effectFunction = null;
			var cfxImage = cfxActiveCanvas.getCurrentImage();
			if(effect == "Window/Level" && info.which == 1){
				cfxActiveCanvas.setWindowLevel(info.xDiff, info.yDiff);
			}
	    	if(effect == "Pan" && info.which == 1 || info.which == 2){
				cfxActiveCanvas.setPan(info.xDiff, info.yDiff);
			}
			if(effect == "Measure" && info.which == 1 ){
				cfxActiveCanvas.updateCanvas();
				cfxActiveCanvas.applyAnnotation(startCoords, lastCoords.stage);

			}
			updateHud();
		}
	}
		
};

//register events
if(isMobile()){
	$(".viewer-layout canvas").on("touchmove", function(e){e.preventDefault();});
	$(".viewer-layout canvas").on("touchstart", events.press);
	$(".viewer-layout canvas").on("touchmove touchenter", events.move);
	$(".viewer-layout canvas").on("touchend touchleave touchcancel", events.release);

}else{
	 $(document).on("mousedown", function(e){e.which == 2 && e.preventDefault();});
	 $(".viewer-layout canvas").on("mousedown", events.press);
	 $(".viewer-layout canvas").on("mousemove mouseover", events.move);
	 $(document).on("mouseup", function(e){
	 	events.release.call($(".viewer-layout canvas.active"), e);
	 });
}


$(".next-image, .prev-image").click(function(){
	var $this = $(this);
	setActiveCanvas($(this).closest(".viewer").find("canvas")[0]);
	if($this.hasClass("next-image")){
		cfxActiveCanvas.nextImage();
	}else{
		cfxActiveCanvas.prevImage();
	}
	updateZoom();
});
$(".btn-zoom-increase, .btn-zoom-decrease").on("click", function(){
	var amt = 5;
	if($(this).hasClass("btn-zoom-decrease")){
		amt = -5
	}
	var zoom = Number($("#zoom-slider").val());

	if((zoom == 600 && amt == 5) || (zoom == 5 && amt == -5))
		return;

	zoom += amt;

	var cfxImg = cfxActiveCanvas.getCurrentImage()
	
	
	$("#zoom-slider").val(zoom);
	cfxActiveCanvas.zoom(zoom/100);
	updateHud();
	debug.log("Adjusting zoom to " + zoom + " percent:" + (cfxImg.currentZoom * 100));
	$("#zoom-value").text(Math.round(cfxImg.currentZoom * 100) + "%");
	setAction("Zoom Set To " + Math.round(cfxImg.currentZoom * 100) + "%" );
})
$(".btn-sharp-increase, .btn-sharp-decrease").on("click", function(){
	var amt = .05;
	if($(this).hasClass("btn-sharp-decrease")){
		amt = -.05
	}
	var sharpness = Number($("#sharpness-slider").val());

	if((sharpness == 5 && amt == .05) || (sharpness == 0 && amt == -.05))
		return;

	sharpness += amt;
	sharpness = Number(parseFloat(sharpness + "").toFixed(2));

	$("#sharpness-value").text(sharpness);
	$("#sharpness-slider").val(sharpness)
	cfxActiveCanvas.setSharpness(sharpness);
	setAction("Sharpness Set To " +sharpness );
})
$(".pan, .window-level, .measure").on("click", function(){
	effect = $(this).attr("title");
	$(".btn-primary").removeClass("btn-primary").addClass("btn-danger");
	$(this).addClass("btn-primary").removeClass("btn-danger");
	setAction(effect);
})

$(".cine").click(function(){
	if(!cfxActiveCanvas.playing){
		var $this = $(this);
		//before we play we have to find out how fast to play.
		vex.dialog.open({
			message: "Enter Cine Speed (Frames a Seconds)",
			input:'<input name="speed" type="text" placeholder="Speed" value="' + cfxActiveCanvas.playSpeed + '" required/>',
			buttons:[
				$.extend({}, vex.dialog.buttons.YES, {text:'Play'}),
				$.extend({}, vex.dialog.buttons.NO, {text:'Cancel'})
			],
			callback: function(response){
				if(response){
					$this.find("i").addClass("fa-stop").removeClass("fa-play");
					cfxActiveCanvas.play(response.speed);

				}
			}
		})
	}else{
		cfxActiveCanvas.stop()
		$(this).find("i").removeClass("fa-stop").addClass("fa-play");
	}
})


$(".reset").click(function(){
	cfxActiveCanvas.reset();
	setAction("Reset");
});
$(".rotate-left, .rotate-right").click(function(){
	$this = $(this);
	var dir = 1;
	var action = "Rotate Right"
	if(!$this.hasClass("rotate-right")){
		action = "Rotate Left";
		dir = -1;
	}
	cfxActiveCanvas.rotate(90*dir);
	setAction(action);
});
$(".flip-vertical").click(function(){
	cfxActiveCanvas.vflip();
	setAction("Flip Vertically");
});
   
$(".flip-horizontal").click(function(){
	cfxActiveCanvas.hflip();
	setAction("Flip Horizontal");
});
$(".clear-measurements").click(function(){
	cfxActiveCanvas.resetAnnotations();
	setAction("Clear Measurement");
});
var windowLayout = 1;
$(".configure-windows").click(function(){
	vex.dialog.open({
			message: "Select Window Layout",
			input:'<select name="layout" placeholder="Speed" value="' + cfxActiveCanvas.playSpeed + '" required>' + 
			'<option value="1" ' + ((windowLayout == 1) ? "selected": "") + '>1</option>'+
			'<option value="2" ' + ((windowLayout == 2) ? "selected": "") + '>1x2</option>'+
			'<option value="4" ' + ((windowLayout == 4) ? "selected": "") + '>2x2</option></select>',
			buttons:[
				$.extend({}, vex.dialog.buttons.YES, {text:'Set'}),
				$.extend({}, vex.dialog.buttons.NO, {text:'Cancel'})
			],
			callback: function(response){
				if(response){
					setWindowLayout(response.layout);
					windowLayout = response.layout;
					updateCanvasSize();
				}
			}
		})
});
//


function updateCanvasSize(){
	var canvasTotal = $(".viewer").not(".hidden").length;
	//calculate progress bar location
	var mainCanvas = $(".viewer-layout").find(".viewer:eq(0)");
	var secondCanvas = $(".viewer-layout").find(".viewer:eq(1)");
	var thirdCanvas = $(".viewer-layout").find(".viewer:eq(2)");
	var canvasWidth = mainCanvas.width();
	var canvasHeight = mainCanvas.height();
	var sideBar = $(".sidebar");
	var sideBarWidth = sideBar.width();
	var knob = $(".knob-container");
	var knobWidth = knob.width();
	var knobHeight = knob.height();
	var navBar = $(".navbar");
	var navBarHeight = navBar.height();

	if(secondCanvas.is(":visible"))
		canvasWidth += secondCanvas.width();
	if(thirdCanvas.is(":visible"))
		canvasHeight += thirdCanvas.height();
	var top = navBarHeight + (canvasHeight/2) - (knobHeight/2);
	
	var left = (canvasWidth/2) - (knobWidth/2);
	if($(window).width() > 992)
	left += sideBarWidth;

	$(".knob-container").css({top:top, left:left});
	

	$(".viewer-layout canvas").each(function(){
		var cfxCurrentCanvas = $(this).data('cfxCanvas');
		var container = $(cfxCurrentCanvas.canvas).closest(".viewer-layout");

		var maxWidth = $(window).width();
		var maxHeight = $(window).height() - navBarHeight;
		$(".sidebar").css("height", maxHeight + "px");

		

		if(maxWidth > 960){
			maxWidth = maxWidth - sideBarWidth;
		}

		if(canvasTotal > 1){
			maxWidth /= 2;
			if(canvasTotal > 2){
				maxHeight /= 2;
			}
		}

		maxWidth -= 10;
		maxHeight -= 20;

		cfxCurrentCanvas.canvas.width = maxWidth;
		cfxCurrentCanvas.canvas.height = maxHeight;

		var mod = 50;
	    if(this.width < 992)
	    	mod = 65;
	    if(this.width < 768)
	    	mod = 65;
	    if(this.width < 400)
	    	mod = 55;

	    $(this).parent().find(".next-image").css(
			{
				marginTop: this.height/2-50, 
				marginLeft: this.width-mod
			}
		);
	     $(this).parent().find(".prev-image").css(
			{
				marginTop: this.height/2-50, 
				marginLeft: 25
			}
		);


		$(this).data('cfxCanvas').updateCanvasSize();
		$(this).data('cfxCanvas').updateCanvas();
		updateHud();
	});
}
window.addEventListener('resize', function(){

	setTimeout(function(){updateCanvasSize();}, 50);

}, false);


$("#zoom-slider").noUiSlider({
	 range: [5, 600]
	,start: 100
	,step: 5
	,handles: 1
	,serialization: {
		to: [ $("#low"), 'html' ]
	}
	,slide:function(){
		
		var cfxImg = cfxActiveCanvas.getCurrentImage();
		var val = $(this).val();
		
		cfxActiveCanvas.zoom($(this).val()/100);
		$("#zoom-value").text(Math.round(cfxImg.currentZoom * 100) + "%");
		updateHud();
	}
});
$("#sharpness-slider").noUiSlider({
	 range: [0, 5]
	,start: 0
	,step: .05
	,handles: 1
	,serialization: {
		to: [ $("#low"), 'html' ]
	}
	,slide:function(){
		
		var val = $(this).val();
		$("#sharpness-value").text(val);
		cfxActiveCanvas.setSharpness(val);
	}
});

function getCoords(e){
	var container = $(cfxActiveCanvas.canvas).closest(".viewer");
	var position = container.position()
	position.left += Number(container.css("padding-left").replace("px", ""));
	var cfxImg = cfxActiveCanvas.getCurrentImage();
	return {
		x:(e.pageX - position.left - cfxActiveCanvas.canvas.width/2) / cfxImg.scale.x,
		y:(e.pageY - position.top - cfxActiveCanvas.canvas.height/2) / cfxImg.scale.y
	}
}
//global functions
function fetchImages(images){
	debug.log("calling api convertDicomToJPG");
	
	APIService.convertDicomToJPG(images.join(","), function(response){
		debug.log("finished creating jpg images for uuids ", images);
		cfxActiveCanvas.clearImages();
		var imagesPath = [];
		for(var i = 0; i < images.length; i++){
			imagesPath.push(ImagesRootPath + images[i]+".jpg")
		}
		debug.log("calling active canvas to load new images ", imagesPath);
		cfxActiveCanvas.addImages(imagesPath);
		updateProgress("Updating Canvas", 75);
	});
}
function updateStudyInfo(){
	var studyInfo = data.studies[0].series[0].images[cfxActiveCanvas.imageIndex].info;
	$("[data-bind^='ImageInfo.']").each(function(){
		var binding = $(this).data("bind");
		var field = binding.replace("ImageInfo.", "");
		$(this).text(studyInfo[field]);
	})
	$("[data-bind='Study.UID']").text(data.studies[0].uuid);
	$("[data-bind='Series.UID']").text(data.studies[0].series[0].uuid);
}
function updateHud(){
	
	$(".viewer-layout canvas").each(function(){
		var cfx = $(this).data("cfxCanvas");
		var cfxImg = cfx.getCurrentImage();
		if(cfxImg){
			if(cfxImg.zoom !== undefined){
				$(this).closest(".viewer").find(".zoom-value").text(Math.round(cfxImg.currentZoom * 100));
			}
			if(cfxImg.window !== undefined){
				$(this).closest(".viewer").find(".level-value").text(Math.round(cfxImg.level));
				$(this).closest(".viewer").find(".window-value").text(Math.round(cfxImg.window ));
			}
			if(cfx.seriesNumber !== undefined){
				$(this).closest(".viewer").find(".series-value").text(Math.round(cfx.seriesNumber));
			}
		} 
	});
	
}
function setWindowLayout(canvasCount){
	var container = $(".viewer-layout");
	$(".viewer").addClass("hidden");
	for(var i = 0; i < canvasCount;i++){
		$(".viewer").eq(i).removeClass("hidden");
	}
	updateCanvasSize()
}
/**
* Generic Date Function (should be supported in javascript by default)
**/
Date.prototype.toDateFormat = function(str){
	var days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
	var wkdy = days[this.getUTCDay()];
	var d = this.getUTCDate();
	var m = this.getUTCMonth() + 1;
	var y = this.getUTCFullYear();
	var ampm = "AM";
	var h24 = this.getUTCHours();
	var h = ((h24 + 11) % 12 + 1);
	var min = this.getUTCMinutes();
	var secs = this.getUTCSeconds();
	if(h24 > 12)
		ampm = "PM";
	var t = (h <= 9 ? "0" + h : h)  + ":" + (min <= 9 ? "0" + min : min) + ":" + (secs <= 9 ? "0" + secs : secs);
	m = (m <= 9 ? "0" + m : m);
	d = (d <= 9 ? "0" + d : d);

	str = str.replace("{wkdy}", wkdy);
	str = str.replace("{d}", d);
	str = str.replace("{m}", m);
	str = str.replace("{y}", y);
	str = str.replace("{t}", t);
	str = str.replace("{ampm}", ampm);
	return str;
}

function isMobile(){
	return /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)
}
vex.defaultOptions.className = 'vex-theme-default';

$(".debug-window a").on("click", function(){
	var child = $(this).next(".nav-list");
	if(child.hasClass("hidden"))
		child.removeClass("hidden");
	else
		child.addClass("hidden");
});
$(".debug-action-calc-center").on("click", function(){
	updateCanvasSize();
});
var timeoutID = -1;
function setMessage(msg, timeout){
	if(timeout == undefined)
		timeout = 2000;
	var actionsContainer = $("#actions");
	actionsContainer.empty().append(msg);
	actionsContainer.slideDown();
	if(timeoutID != -1)
		clearTimeout(timeoutID);
	if(timeout != 0){
		timeoutID = setTimeout(function(){
			actionsContainer.slideUp();
		}, timeout);
	}
}
function setAction(msg, timeout){
	setMessage(msg + " successfully applied", timeout)
}

var displayText = "{{value}}%";
$(".knob").knob({
    format : function (value) {
        return displayText.replace("{{value}}", value);
    }
});

$(".study-report").on("click", function(){
	if(!studyReportExists){
		setMessage("Report not yet available,  please check back later.");
	}
});

var progressStack = [];
var progressIsAnimating = false;
var animation = null;
function updateProgress(msg, val){
	
	if(val == 100){
		progressIsAnimating = false;
		progressStack = [];
		animation.finish();
	}
	if(!progressIsAnimating){
		progressIsAnimating = true;
		debug.log(msg, val);
		var knobContainer = $(".knob-container");
		var knob = $(".knob");
		var display = $(".knob-container span");
		var currentValue = Number(knob.val().replace("%", ""));
		if(val != 100)
			display.text(msg);

		if(currentValue >= 100)
			currentValue = 0;

		if(!knobContainer.is("visible"))
			knobContainer.fadeIn();
	    animation = $({value:currentValue}).animate({value: val}, {
		    duration: 250,
		    easing:'swing',
		    step: function() 
		    {
		    	var val = Math.ceil(this.value);
		        knob.val(val).trigger('change');
		    },
		    complete:function(){
		    	knob.val(val).trigger('change');
		    	if(knob.val() == "100%"){
		    		display.text(msg);
		    		setTimeout(function(){
		    			knobContainer.fadeOut("slow");
		    		}, 250);
		    	}
		    	if(progressStack.length > 0){

	    			var progress = Array.prototype.splice.call(progressStack, 0, 1)[0];
	    			updateProgress(progress.msg, progress.val);
    			}
				progressIsAnimating = false;
		    	
		    }
		});
	}else{
		progressStack.push({msg:msg, val:val});
	}

}