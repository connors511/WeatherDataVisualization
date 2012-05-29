
<html>
<head>
<title>Cunt</title>
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
<script>
var images = new Image();
var count = 0;
var run = false;

$(function() {
$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
		});
});
</script>
</head>
<body>
<?php
include('sidebar.htm');
?>
<div id="map"></div>
<script src="http://code.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>
<script type="text/javascript">

var map = new L.Map('map');

var cloudmade = new L.TileLayer('http://{s}.tile.cloudmade.com/6d31e7d426dc40368e1b7bd1f07c8aa6/997/256/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery � <a href="http://cloudmade.com">CloudMade</a>',
    maxZoom: 18
});

var pos;


pos = new L.LatLng(56.200, 11.200); 

map.setView(pos, 7).addLayer(cloudmade);
//map.locate({setView: true, maxZoom: 9});


//--------- Windmills
var WindIcon = L.Icon.extend({
    iconUrl: '../windmill.png',
    shadowUrl: null,
    iconSize: new L.Point(64, 64),
    shadowSize: null,
    iconAnchor: new L.Point(32, 32),
    popupAnchor: new L.Point(-3, -76)
});

var icon = new WindIcon();

var markerpos = new L.LatLng(56.2,11.2);
var marker = new L.Marker(markerpos, {icon: icon}), marker2 = new L.Marker(new L.LatLng(56.2,9.2), {icon: icon});

map.addLayer(marker).addLayer(marker2);

marker.on('click', function(e) {
	
    var page = "chart.html?lat=" + this.getLatLng().lat.toFixed(3) + "&lng=" + this.getLatLng().lng.toFixed(3);
    var pagetitle = "Chart (" + this.getLatLng().lat.toFixed(3) + "; " + this.getLatLng().lng.toFixed(3) + ")";
    var $dialog = $( "#dialog-form" )
                .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
                .dialog({
                    autoOpen: false,
                    modal: true,
                    height: screen.height*0.75,
                    width: screen.width*0.9,
                    title: pagetitle,
                    close: function(ev, ui) { $("#map").fadeTo("slow", 1); },
                    dragable: false
                });
                $dialog.dialog('open');
    //alert("test " + '(' + this.getLatLng().lat.toFixed(3) + ', ' + this.getLatLng().lng.toFixed(3) + ')');
});


//---------
//--------- Radars
var RadarIcon = L.Icon.extend({
    iconUrl: '../radar.png',
    shadowUrl: null,
    iconSize: new L.Point(64, 64),
    shadowSize: null,
    iconAnchor: new L.Point(32, 32),
    popupAnchor: new L.Point(-3, -76)
});
icon = new RadarIcon();

RadMarkPos = new L.LatLng(55.5,7.9);
Radmark = new L.Marker(RadMarkPos, {icon: icon});

map.addLayer(Radmark);
images = ["http://l.yimg.com/us.yimg.com/i/mesg/emoticons7/108.gif",  
                        "http://l.yimg.com/us.yimg.com/i/mesg/emoticons7/106.gif",  
                        "http://l.yimg.com/us.yimg.com/i/mesg/emoticons7/102.gif",  
                        "http://l.yimg.com/us.yimg.com/i/mesg/emoticons7/101.gif",  
                        "http://l.yimg.com/us.yimg.com/i/mesg/emoticons7/103.gif"];

Radmark.on('click', function(e) {
		if (!run) {
			run = true;
			SwitchPic();
			run = false;
		}
});	


//---------


/*map.on('click', onMapClick);

function onMapClick(e) {
    var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';
	var popup = new L.Popup();
    popup.setLatLng(e.latlng);
    popup.setContent("You clicked the map at " + latlngStr);

    map.openPopup(popup);
}*/

function SwitchPic() {
    map.removeLayer(Radmark);
	var RadarIcon = L.Icon.extend({
	    iconUrl: images[count++],
	    shadowUrl: null,
	    iconSize: new L.Point(64, 64),
	    shadowSize: null,
	    iconAnchor: new L.Point(32, 32),
	    popupAnchor: new L.Point(-3, -76)
	});
	icon = new RadarIcon();

	RadMarkPos = new L.LatLng(55.5,7.9);
	Radmark = new L.Marker(RadMarkPos, {icon: icon});
	map.addLayer(Radmark);
	
	if (count < images.length) {
		setTimeout('SwitchPic()', 1000);
	} else {
		setTimeout(function () {
			map.removeLayer(Radmark);
			var RadarIcon = L.Icon.extend({
			   iconUrl: '../radar.png',
			   shadowUrl: null,
			   iconSize: new L.Point(64, 64),
			   shadowSize: null,
			   iconAnchor: new L.Point(32, 32),
			   popupAnchor: new L.Point(-3, -76)
			});
			icon = new RadarIcon();
			
			RadMarkPos = new L.LatLng(55.5,7.9);
			Radmark = new L.Marker(RadMarkPos, {icon: icon});
			
			map.addLayer(Radmark);
		}, 2000);
	}
}

</script>
<div class="demo">
	<div id="dialog-form" title="Chart">
		<p>hallo</p>
	</div>
</div>



</body>
</html>