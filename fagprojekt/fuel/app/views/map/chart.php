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
        
var PossiblePower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'PossiblePower','id'=>'26'))); ?>;
var WindSpeed 		= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'WindSpeed','id'=>'26'))); ?>;
var RegimePossible 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'RegimePossible','id'=>'26'))); ?>;
var OutputPower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'OutputPower','id'=>'26'))); ?>;
var RegimeOutput 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'RegimeOutput','id'=>'26'))); ?>;

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
	<div id="zoom" style="color: #fff;"><script>document.write(today.getDate() + ". " + month[today.getMonth()-1] + " " + today.getFullYear() + " \t - \t " + today.getDate() + ". " + month[today.getMonth()] + " " + today.getFullYear())</script></div>
		<div class="button" id="button" style="margin-right: 10px;"><p>Monthly view</p></div>
	    <div id="placeholder"></div>
	    <div id="controlButtons">
			<div id="backfast" class="controlButton"></div>
			<div id="back" class="controlButton"></div>
			<div id="today" class="controlButton"></div>
			<div id="forward" class="controlButton"></div>
			<div id="forwardfast" class="controlButton"></div>
	  </div>
	<div id="sizer">
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
	
	
	<div id="openchart" onClick="javascript:toggle();"></div>
	
<script type="text/javascript">
var placeholder = $("#placeholder");
$("#closechart").click(function () {
		$("#sizer").hide("slide", { direction: "left" }, 600);
		$("#openchart").show("slide", { direction: "left" }, 1000); 
	});

	function toggle() {
		$("#openchart").hide("slide", { direction: "left" }, 600);
		$("#sizer").show("slide", { direction: "left" }, 600);
	}

    // Returns the arrays that the user selects.
function getArray() {
	var power  = 1;
	var wind   = 2;
	var regime = 3;
	if (!$("#checkbox-01").is(':checked') && !$("#checkbox-04").is(':checked')) {
		if (!$("#checkbox-02").is(':checked')) {
			regime = 1;
		} else {
			wind--;
			regime--;	
		}
	} else {
		if (!$("#checkbox-02").is(':checked')) {
			regime--;	
		}
	}
	return 	[ 
			 (($("#checkbox-01").is(':checked')) ? {data: PossiblePower, label: "Possible Power", yaxis: power} : [[null]]),
			 (($("#checkbox-04").is(':checked')) ? {data: OutputPower, label: "Output Power", yaxis: power} : [[null]]),
    		 (($("#checkbox-02").is(':checked')) ? {data: WindSpeed, label: "Wind Speed", yaxis: wind} : [[null]]),
    		 (($("#checkbox-03").is(':checked')) ? {data: RegimePossible, label: "Regime Possible", yaxis: regime} : [[null]]),
			 (($("#checkbox-05").is(':checked')) ? {data: RegimeOutput, label: "Regime Output", yaxis: regime} : [[null]])  
			];
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

    // Outputs the date range
function outputDate(d) {
	if (getView() == "<p>Monthly view</p>") {
		var temp = new Date(d.getFullYear(), d.getMonth()-1, d.getDate())
	} else if (getView() == "<p>Weekly view</p>") {
		var temp = new Date(d.getFullYear(), d.getMonth(), d.getDate()-7)
	} else {
		var temp = new Date(d.getFullYear(), d.getMonth(), d.getDate()-1)	
	}
	$("#zoom").text(temp.getDate() + ". " + month[temp.getMonth()] + " " + temp.getFullYear() + " \t - \t " + d.getDate() + ". " + month[d.getMonth()] + " " + d.getFullYear())
}

function plotdata(sDate, eDate) {
	outputDate(eDate);
	$.plot($("#placeholder"), getArray(), { 
		xaxis: { 	
			mode: "time",
			min: sDate.getTime(),
			max: eDate.getTime()
		},
		legend: { position: 'sw' },

                series: {
                         lines: { show: true , shadowSize:0},
                },
                clickable: true,
                hoverable: true
	});
	//plot.setupGrid();
	//plot.draw();	
}

$(function () {
    // Placeholder (the plot)
	plotdata((new Date(today.getFullYear(), today.getMonth()-1, today.getDate())), today);
    
    // Check checkbox 1
	$("#checkbox-01,#checkbox-02,#checkbox-03,#checkbox-04,#checkbox-05").change(function() {
		if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		} else if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
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
	
    // Backfast button (-1 month in monthly view and -7 days in weekly view)
	$("#backfast").click(function () {
		if (getView() == "<p>Monthly view</p>") {
			var day = today.getDate();
			var month = today.getMonth() - 1;
		} else if (getView() == "<p>Weekly view</p>") {
			var day = today.getDate() - 7;
			var month = today.getMonth();
		} else {
			var day = today.getDate() - 1;
			var month = today.getMonth();	
		}
		var year = today.getFullYear();

		if (new Date(WindSpeed[0][0]) >= today) {
			day = today.getDate();
			month = today.getMonth();
			year = today.getFullYear();
		}
		today = new Date(year, month, day);		
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		}
	});

    // Back button (-1 day)
	$("#back").click(function () {
		var day = today.getDate() - 1;
		var month = today.getMonth();
		var year = today.getFullYear();
		if (new Date(WindSpeed[0][0]) >= today) {
			day = today.getDate();
			month = today.getMonth();
			year = today.getFullYear();
		}
		today = new Date(year, month, day);	
		outputDate(today);
		
		if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		}  else {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-1), today);
		}
	});

    // Today button
	$("#today").click(function () {
		today = now;
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else if ("<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		}  else {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-1), today);
		}
	});

    // Forward button (+1 day)
	$("#forward").click(function () {
		var day = today.getDate() + 1;
		today = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		}  else {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-1), today);
		}
	});

    // Forwardfast button (+1 month forward in monthly view and +7 days forward in weekly view)
	$("#forwardfast").click(function () {
		if (getView() == "<p>Monthly view</p>") {
			var day = today.getDate();
			var month = today.getMonth() + 1;
		} else if (getView() == "<p>Weekly view</p>") {
			var day = today.getDate() + 7;
			var month = today.getMonth();
		} else {
			var day = today.getDate() + 1;
			var month = today.getMonth();	
		}
		var year = today.getFullYear();
		today = new Date(year, month, day);
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else if (getView() == "<p>Weekly view</p>") {
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		}
	});
	
	// Change view - checks if monthly or weekly view is activated.  
	$("#button").click(function () {
		if (getView() == "<p>Monthly view</p>") {
			document.getElementById("forwardfast").style.cursor='pointer';
			document.getElementById("backfast").style.cursor='pointer';
			$('#forwardfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			document.getElementById("button").innerHTML = "<p>Weekly view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()-10)
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-7), today);
		} else if (getView() == "<p>Daily view</p>") {
			document.getElementById("forwardfast").style.cursor='pointer';
			document.getElementById("backfast").style.cursor='pointer';
			$('#forwardfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 1, function() {
		    	// Animation complete.
		    });
			document.getElementById("button").innerHTML = "<p>Monthly view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()+10)
			plotdata(new Date(today.getFullYear(), today.getMonth()-1, today.getDate()), today);
		} else {
			$('#forwardfast').fadeTo('fast', 0.1, function() {
		    	// Animation complete.
		    });
			$('#backfast').fadeTo('fast', 0.1, function() {
		    	// Animation complete.
		    });
			document.getElementById("forwardfast").style.cursor='default';
			document.getElementById("backfast").style.cursor='default';
			document.getElementById("button").innerHTML = "<p>Daily view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate())
			plotdata(new Date(today.getFullYear(), today.getMonth(), today.getDate()-1), today);
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
