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
        
var PossiblePower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'PossiblePower','id'=>'0'))); ?>;
var WindSpeed 		= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'WindSpeed','id'=>'0'))); ?>;
var RegimePossible 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010020100','f'=>'2010010100','c'=>'RegimePossible','id'=>'0'))); ?>;
var OutputPower 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010010100','f'=>'2010010100','c'=>'OutputPower','id'=>'0'))); ?>;
var RegimeOutput 	= <?php echo file_get_contents(Uri::create('file/csv/list.json', array(), array('t'=>'2010010100','f'=>'2010010100','c'=>'RegimeOutput','id'=>'0'))); ?>;

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
var now   = new Date(PossiblePower[PossiblePower.length-1][0]);
//var now   = new Date(PossiblePower[PossiblePower.length-1][0]);
var today = now;
	</script>
 </head>
 <body>
	<div id="zoom" style="color: #fff;"></div>
		<div class="button" id="button" style="margin-right: 10px;"><p>Monthly view</p></div>
	    <div id="placeholder"></div>
	    <div id="controlButtons">
		<p>
			<img src="go_backfast.png" id="backfast" alt="Fast back"></img>
			<img src="go_back.png" id="back" alt="Back"></img>
			<img src="go_today.png" id="today" alt="Today"></img>
			<img src="go_forward.png" id="forward" alt="Forward"></img>
			<img src="go_forwardfast.png" id="forwardfast" alt="Fast forward"></img>
		</p>
	  </div>
	<div id="sizer">
		<form action="#" method="get" accept-charset="utf-8">
			<fieldset class="checkboxes">
				<label class="label_check" for="checkbox-01"><input name="sample-checkbox-01" id="checkbox-01" value="1" type="checkbox" checked /> Possible Power</label><br />
				<label class="label_check" for="checkbox-02"><input name="sample-checkbox-02" id="checkbox-02" value="1" type="checkbox"/> Wind Speed</label><br />
				
				<div id="close">
					<img src="slideout2.png"/>
				</div>
				
				<label class="label_check" for="checkbox-03"><input name="sample-checkbox-03" id="checkbox-03" value="1" type="checkbox"/> Regime Possible</label><br />
				<label class="label_check" for="checkbox-04"><input name="sample-checkbox-04" id="checkbox-04" value="1" type="checkbox"/> Output Power</label><br />
				<label class="label_check" for="checkbox-05"><input name="sample-checkbox-05" id="checkbox-05" value="1" type="checkbox"/> Regime Output</label>
			</fieldset>
		</form>
	</div>
	
	<div id="open" style="top: 30%; left: -10px;">
		<a href="javascript:toggle();"><img src="slidein2.png" id="openme"/></a>
	</div>
	
<script type="text/javascript">
$("#close").click(function () {
		$("#sizer").hide("slide", { direction: "left" }, 600);
		$("#open").show();
		$("#openme").show("slide", { direction: "left" }, 600); 
	});

	function toggle() {
		$("#openme").hide("slide", { direction: "left" }, 600);
		$("#sizer").show("slide", { direction: "left" }, 600);
	}

    // Returns the arrays that the user selects.
function getArray() {
	return 	[ 
			 (($("#checkbox-01").is(':checked')) ? {data: PossiblePower, label: "Possible Power", yaxis: 1} : [[null]]),
			 (($("#checkbox-04").is(':checked')) ? {data: OutputPower, label: "Output Power", yaxis: 1} : [[null]]),
    		 (($("#checkbox-02").is(':checked')) ? {data: WindSpeed, label: "Wind Speed", yaxis: 2} : [[null]]),
    		 (($("#checkbox-03").is(':checked')) ? {data: RegimePossible, label: "Regime Possible", yaxis: 3} : [[null]]),
			 (($("#checkbox-05").is(':checked')) ? {data: RegimeOutput, label: "Regime Output", yaxis: 3} : [[null]])  
			];
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

$(function () {
    
    outputDate(today);
    
    // Placeholder (the plot)
	var placeholder = $("#placeholder");
	$.plot($("#placeholder"), getArray(), { 
		xaxis: { 	
			mode: "time",
			min: (new Date(today.getFullYear(), today.getMonth()-1, today.getDate())).getTime(),
			max: (today).getTime()
		},
                yaxis: {
                    min:0,
                    max:20
                },
		legend: { position: 'sw' } 
	});
    
    // Check checkbox 1
	$("#checkbox-01,#checkbox-02,#checkbox-03,#checkbox-04,#checkbox-05").change(function() {
		if (getView() == "<p>Weekly view</p>") {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-7)).getTime(),
					max: (today).getTime()
				},
		legend: { position: 'sw' }
			});

		} else if (getView() == "<p>Monthly view</p>") {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth()-1, today.getDate())).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
		} else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
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
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month-1, day)).getTime(),
					max: (new Date(year, month, day)).getTime()
				},
			legend: { position: 'sw' }
			});
		} else if (getView() == "<p>Weekly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month, day-7)).getTime(),
					max: (new Date(year, month, day)).getTime()
			},
			legend: { position: 'sw' }
			});
		} else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
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
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month-1, day)).getTime(),
					max: (new Date(year, month, day)).getTime()
				},
			legend: { position: 'sw' }
			});
		} else if (getView == "<p>Weekly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month, day-7)).getTime(),
					max: (new Date(year, month, day)).getTime()
			},
			legend: { position: 'sw' }
			});
		}  else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
		}
	});

    // Today button
	$("#today").click(function () {
		today = now;
		var day = today.getDate();
		var month = today.getMonth();
		var year = today.getFullYear();
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month-1, day)).getTime(),
					max: (new Date(year, month, day)).getTime()
				},
			legend: { position: 'sw' }
			});
		} else if ("<p>Weekly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month, day-7)).getTime(),
					max: (new Date(year, month, day)).getTime()
			},
			legend: { position: 'sw' }
			});
		}  else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
		}
	});

    // Forward button (+1 day)
	$("#forward").click(function () {
		var day = today.getDate() + 1;
		var month = today.getMonth();
		var year = today.getFullYear();
		today = new Date(year, month, day);
		outputDate(today);
		if (getView() == "<p>Monthly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month-1, day)).getTime(),
					max: (new Date(year, month, day)).getTime()
				},
			legend: { position: 'sw' }
			});
		} else if (getView() == "<p>Weekly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month, day-7)).getTime(),
					max: (new Date(year, month, day)).getTime()
			},
			legend: { position: 'sw' }
			});
		}  else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
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
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month-1, day)).getTime(),
					max: (new Date(year, month, day)).getTime()
				},
			legend: { position: 'sw' }
			});
		} else if (getView() == "<p>Weekly view</p>") {
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(year, month, day-7)).getTime(),
					max: (new Date(year, month, day)).getTime()
			},
			legend: { position: 'sw' }
			});
		}  else {
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
		}
	});
	
	// Change view - checks if monthly or weekly view is activated.  
	$("#button").click(function () {
		if (getView() == "<p>Monthly view</p>") {
			document.getElementById("button").innerHTML = "<p>Weekly view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()-10)
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-7)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});

		} else if (getView() == "<p>Daily view</p>") {
			document.getElementById("button").innerHTML = "<p>Monthly view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate()+10)
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth()-1, today.getDate())).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});
		} else {
			document.getElementById("button").innerHTML = "<p>Daily view</p>"
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate())
			outputDate(today);
			$.plot($("#placeholder"), getArray(), {
				xaxis: {
					mode: "time",
					min: (new Date(today.getFullYear(), today.getMonth(), today.getDate()-1)).getTime(),
					max: (today).getTime()
				},
			legend: { position: 'sw' }
			});	
		}
	});
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
