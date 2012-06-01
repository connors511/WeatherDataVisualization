<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Weather</title>
	<?php echo Asset::css('style.css'); ?>

    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="excanvas.min.js"></script><![endif]-->
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.ie.css" />
	<![endif]-->
        <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />      
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
        <?php echo Asset::js('jquery.flot.js'); ?>
        <?php echo Asset::js('jquery.flot.resize.js'); ?>
        
    <style type="text/css">
    html, body {
        height: 100%; /* make the percentage height on placeholder work */
    }
    </style>
	<script type="text/javascript">
    <?php
	$params = array('t'=>'2010020100','f'=>'2010010100','c'=>'PossiblePower','id'=>$id);
	?>
var PossiblePower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), $params)); ?>;
var WindSpeed 		= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), $params)); ?>;
var RegimePossible 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), $params)); ?>;
var OutputPower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), $params)); ?>;
var RegimeOutput 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), $params)); ?>;

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
	
    // Sets the 'Now' date (+ 5 days) and today date
var now   = new Date(WindSpeed[WindSpeed.length-1][0]+5);
var today = now;
	</script>
 </head>
 <body>
	<div id="sizer">
		<div id="close"></div>
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

	<div id="open" onClick="javascript:toggle();"></div>

	<script type="text/javascript">
		function setupLabel() {
			if ($('.label_check input').length) {
				$('.label_check').each(function(){ 
					$(this).removeClass('c_on');
				});
				$('.label_check input:checked').each(function(){ 
					$(this).parent('label').addClass('c_on');
				});                
			};
			if ($('.label_radio input').length) {
				$('.label_radio').each(function(){ 
					$(this).removeClass('r_on');
				});
				$('.label_radio input:checked').each(function(){ 
					$(this).parent('label').addClass('r_on');
				});
			};
		};
		$(document).ready(function(){
			$('body').addClass('has-js');
			$('.label_check, .label_radio').click(function(){
				setupLabel();
			});
			setupLabel();
		});
	
	$("#close").click(function () {
		$("#sizer").hide("slide", { direction: "left" }, 600);
		$("#open").show("slide", { direction: "left" }, 1000); 
	});

	function toggle() {
		$("#open").hide("slide", { direction: "left" }, 600);
		$("#sizer").show("slide", { direction: "left" }, 600);
	}
	
	$("#open").mouseenter(function () {
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
	
	
	</script>
	
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
<script type="text/javascript">	
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

<script type="text/javascript">
	$(function () {
		
		$('#test').html('');
		
		var headID = document.getElementsByTagName("head")[0];         
		var newScript = document.createElement('script');
		newScript.type = 'text/javascript';
		newScript.src = 'test2.js';
		headID.appendChild(newScript);
		
	});
	</script>

		<footer>
			<p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
			<p>
				<a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
				<small>Version: <?php echo e(Fuel::VERSION); ?></small>
			</p>
		</footer>
</body>
</html>
