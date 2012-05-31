<!DOCTYPE HTML>
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
    <title>Weather chart</title>
    <style type="text/css">
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
}
body {
	line-height: 1;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}

/* remember to define focus styles! */
:focus {
	outline: 0;
}

/* remember to highlight inserts somehow! */
ins {
	text-decoration: none;
}
del {
	text-decoration: line-through;
}

/* tables still need 'cellspacing="0"' in the markup */
table {
	border-collapse: collapse;
	border-spacing: 0;
}

a img {
	border: none;
}

#map {
	width: 100%;
	height: 100%;	
}
body { font-size: 62.5%; }
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
		h1 { font-size: 1.2em; margin: .6em 0; }
		div#users-contain { width: 350px; margin: 20px 0; }
		div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
		div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>
<link rel="stylesheet" href="css/style.css"/>
<link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.ie.css" />
<![endif]-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.19/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
			<script src="http://code.jquery.com/ui/1.8.19/jquery-ui.min.js" type="text/javascript"></script>
	
	<div id="test">
		<script src="test1.js"></script>
	</div>
	
    <style type="text/css">
    html, body {
        height: 100%; /* make the percentage height on placeholder work */
    }
    </style>
    <?php
    	$lat = $_GET['lat'];
    	$lng = $_GET['lng'];
    ?>
	<script type="text/javascript">
function getJson (c, f, t) {
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url': "http://localhost:8888/fagprojekt-wdv/fagprojekt/public/file/csv/list.json?lat=" + <?php echo $lat; ?> + "&lng" + <?php echo $lng; ?> + "&c=" + c + "&f=" + f + "&t=" + t,
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
}
	var PossiblePower = [];
	var OutputPower = [];
	var WindSpeed = [];
	var RegimePossible = [];
	var RegimeOutput = [];
	var today;
	var now;
$(document).ready(function() {
	getArray(new Date(2010, 01, 01), new Date(2010, 02, 01));
	// Sets the 'Now' date (+ 5 days) and today date
	now  = new Date(PossiblePower[PossiblePower.length-1][0]+5);
	today = now;
});
	    // Array of the month, so we can get the month names
var month = new Array();
	month[0] = "January";
	month[1] = "February";
	month[2] = "March";
	month[3] = "April";
	month[4] = "May";
	month[5] = "June";
	month[6] = "July";
	month[7] = "August";
	month[8] = "September";
	month[9] = "October";
	month[10] = "November";
	month[11] = "December";

	</script>
 </head>
 <body>
	<div id="zoom" style="color: #fff;">Today</div>
		<div class="button" id="button" style="margin-right: 10px;"><p>2-week view</p></div>
	    <div id="placeholder"></div>
	    <div id="controlButtons">
			<div id="backfast" class="controlButton"></div>
			<div id="back" class="controlButton"></div>
			<div id="today" class="controlButton"></div>
			<div id="forward" class="controlButton"></div>
			<div id="forwardfast" class="controlButton"></div>
	  </div>
	<div id="sizer" style="max-height: 90%;">
		<div id="closechart"></div>
		<form action="#" method="get" accept-charset="utf-8">
			<fieldset class="checkboxes">
				<ul>
					<label class="label_check" for="checkbox-01"><input name="sample-checkbox-01" id="checkbox-01" value="1" type="checkbox" checked /> Possible Power</label>
					<label class="label_check" for="checkbox-02"><input name="sample-checkbox-02" id="checkbox-02" value="1" type="checkbox"/> Wind Speed</label>
					<label class="label_check" for="checkbox-03"><input name="sample-checkbox-03" id="checkbox-03" value="1" type="checkbox"/> Regime Possible</label>
					<label class="label_check" for="checkbox-04"><input name="sample-checkbox-04" id="checkbox-04" value="1" type="checkbox"/> Output Power</label>
					<label class="label_check" for="checkbox-05"><input name="sample-checkbox-05" id="checkbox-05" value="1" type="checkbox"/> Regime Output</label>
					</ul>
			</fieldset>
		</form>
	</div>
	
	<div id="canvasloader-container" class="wrapper"></div>
	
	<div id="openchart" onClick="javascript:toggle();"></div>
	
<script type="text/javascript">
var cl = new CanvasLoader('canvasloader-container');
		cl.setColor('#ffffff');
		cl.setDiameter(36);
		cl.setDensity(14);
		cl.setRange(0.7);
		cl.setSpeed(1);
$("#closechart").click(function () {
		$("#sizer").hide("slide", { direction: "left" }, 600);
		$("#openchart").show("slide", { direction: "left" }, 1000); 
	});

	function toggle() {
		$("#openchart").hide("slide", { direction: "left" }, 600);
		$("#sizer").show("slide", { direction: "left" }, 600);
	}
	
	function getTimeStamp(date) {
		var d = "0" + (date.getDate()+1);
		var m = "0" + (date.getMonth()+1);
		var y = "" + date.getFullYear();
		return y+m.substring(m.length-2, m.length)+d.substring(d.length-2, d.length)+"0000";
	}

    // Returns the arrays that the user selects.
function getArray(mind, maxd) {
	var array = [];
	var i = 0;
	
	if ($("#checkbox-01").is(':checked')) {
	 	if (PossiblePower.length == 0)
	 		PossiblePower = getJson("PossiblePower", getTimeStamp(mind), getTimeStamp(maxd));
	 	array[i++] = {data: PossiblePower, label: "Possible Power", yaxis: 1};
	}
	if ($("#checkbox-04").is(':checked')) {
	 	if (OutputPower.length == 0)
		 	OutputPower = getJson("OutputPower", getTimeStamp(mind), getTimeStamp(maxd));
	 	array[i++] = {data: OutputPower, label: "Output Power", yaxis: 1};
	}
   	if ($("#checkbox-02").is(':checked')) {
	 	if (WindSpeed.length == 0)
			WindSpeed = getJson("WindSpeed", getTimeStamp(mind), getTimeStamp(maxd));
	 	array[i++] = {data: WindSpeed, label: "Wind Speed", yaxis: 2};
   	}
   	if ($("#checkbox-03").is(':checked')) {
	 	if (RegimePossible.length == 0)
			RegimePossible = getJson("RegimePossible", getTimeStamp(mind), getTimeStamp(maxd));
	 	array[i++] = {data: RegimePossible, label: "Regime Possible", yaxis: 3};
   	}
	if ($("#checkbox-05").is(':checked')) {
	 	if (RegimeOutput.length == 0)
		 		RegimeOutput = getJson("RegimeOutput", getTimeStamp(mind), getTimeStamp(maxd));
	 	array[i++] = {data: RegimeOutput, label: "Regime Output", yaxis: 3};
	}
	
	return 	array;
}

$("#openchart").mouseenter(function () {
		if ($(this).is(":visible")) {
			$( this ).animate({
				left: 0
			}, {
				duration: 100,
			});
		}
	}).mouseout(function(){
		if ($(this).is(":visible")) {
			$( this ).animate({
				left: -10
			}, {
				duration: 100,
			});
		}
	});

function getView() {
	return document.getElementById("button").innerHTML	
}

function plotdata(sDate, eDate) {
	var temps = new Date(sDate.getFullYear(), sDate.getMonth(), sDate.getDate()+1);
	var tempe = new Date(eDate.getFullYear(), eDate.getMonth(), eDate.getDate()+1);
	$("#zoom").text(temps.getDate() + ". " + month[temps.getMonth()] + " " + temps.getFullYear() + " \t - \t " + tempe.getDate() + ". " + month[tempe.getMonth()] + " " + tempe.getFullYear());
	
	if ($("#checkbox-01").is(':checked')) {
		PossiblePower = getJson("PossiblePower", getTimeStamp(sDate), getTimeStamp(eDate));
	}
	if ($("#checkbox-04").is(':checked')) {
		OutputPower = getJson("OutputPower", getTimeStamp(sDate), getTimeStamp(eDate));
	}
	if ($("#checkbox-02").is(':checked')) {
		WindSpeed = getJson("WindSpeed", getTimeStamp(sDate), getTimeStamp(eDate));
	}
	if ($("#checkbox-03").is(':checked')) {
		RegimePossible = getJson("RegimePossible", getTimeStamp(sDate), getTimeStamp(eDate));
	}
	if ($("#checkbox-05").is(':checked')) {
		RegimeOutput = getJson("RegimeOutput", getTimeStamp(sDate), getTimeStamp(eDate));
	}
	plot = $.plot(placeholder, getArray(sDate, eDate), { xaxis: { mode: "time"} });
	plot.setupGrid();	plot.draw();
}

$(function () {
	var placeholder = $("#placeholder");
    // Placeholder (the plot)
    plot = $.plot(placeholder, getArray(), { 
		xaxis: { 	
			mode: "time",
			min: new Date(today.getFullYear(), today.getMonth(), today.getDate()-14).getTime(),
			max: today.getTime()
		},
		legend: { position: 'ne' },

                series: {
                         lines: { show: true , shadowSize:0},
                },
                clickable: true,
                hoverable: true
	});
    
    // Check checkbox 1
	$("#checkbox-01,#checkbox-02,#checkbox-03,#checkbox-04,#checkbox-05").change(function() {
		if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		} else if (getView() == "<p>2-week view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-14), today);
		} else {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-1), today);
		}
	});
            
    // the plugin includes a jQuery plugin for adding resize events to
    // any element, let's just add a callback so we can display the
    // placeholder size
	placeholder.resize(function () {
        $(".message").text("Placeholder is now "
                           + $(this).width() + "x" + $(this).height()
                           + " pixels");
	});
	
    // Backfast button (-14 days in 2-week view and -7 days in weekly view)
	$("#backfast").click(function () {
		var day = today.getDate();
		var month = today.getMonth();
		if (getView() == "<p>2-week view</p>") {
			day = today.getDate() - 14;
		} else if (getView() == "<p>Weekly view</p>") {
			day = today.getDate() - 7;
		}
		var year = today.getFullYear();
		today = new Date(year, month, day);
		if (getView() == "<p>2-week view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
		} else if (getView() == "<p>Weekly view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7);
			plotdata(f, today);
		}
	});

    // Back button (-1 day)
	$("#back").click(function () {
		today = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1);
		if (getView() == "<p>2-week view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
		} else if (getView() == "<p>Weekly view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7);
			plotdata(f, today);
		}  else {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-1);
			plotdata(f, today);
		}
	});

    // Today button
	$("#today").click(function () {
		today = now;
		if (getView() == "<p>2-week view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
		} else if ("<p>Weekly view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7)
			plotdata(f, today);
		}  else {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-1);
			plotdata(f, today);
		}
	});

    // Forward button (+1 day)
	$("#forward").click(function () {
		today = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
		if (getView() == "<p>2-week view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
		} else if (getView() == "<p>Weekly view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7);
			plotdata(f, today);
		}  else {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-1);
			plotdata(f, today);
		}
	});

    // Forwardfast button (+1 month forward in 2-week view and +7 days forward in weekly view)
	$("#forwardfast").click(function () {
			var day = today.getDate();
			var month = today.getMonth();
		if (getView() == "<p>2-week view</p>") {
			var day = today.getDate() + 14;
		} else if (getView() == "<p>Weekly view</p>") {
			var day = today.getDate() + 7;
		}
		var year = today.getFullYear();
		today = new Date(year, month, day);
		if (getView() == "<p>2-week view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
		} else if (getView() == "<p>Weekly view</p>") {
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7);
			plotdata(f, today);
		}
	});
	
	// Change view - checks if 2-week or weekly view is activated.  
	$("#button").click(function () {
		
		// Weekly view
		if (getView() == "<p>2-week view</p>") {
			document.getElementById("forwardfast").style.cursor='pointer';
			document.getElementById("backfast").style.cursor='pointer';
			$('#forwardfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			document.getElementById("button").innerHTML = "<p>Weekly view</p>";
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7)
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-7);
			plotdata(f, today);
		
		// 2-week view
		} else if (getView() == "<p>Daily view</p>") {
			document.getElementById("forwardfast").style.cursor='pointer';
			document.getElementById("backfast").style.cursor='pointer';
			$('#forwardfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			document.getElementById("button").innerHTML = "<p>2-week view</p>";
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()+10)
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-14);
			plotdata(f, today);
			
		// Daily view
		} else {
			$('#forwardfast').fadeTo('fast', 0.1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 0.1, function() {
		    	// Animation complete.
		    });
			document.getElementById("forwardfast").style.cursor='default';
			document.getElementById("backfast").style.cursor='default';
			document.getElementById("button").innerHTML = "<p>Daily view</p>";
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()-3)
			var f = new Date(today.getFullYear(), today.getMonth(), today.getDate()-1);
			plotdata(f, today);
		}
	});
});

</script>
 </body>
</html>
