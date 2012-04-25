<html>
<head>
<title>Cunt</title>
<style type="text/css">
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.ie.css" />
<![endif]-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.19/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
			<script src="http://code.jquery.com/ui/1.8.19/jquery-ui.min.js" type="text/javascript"></script>
<script>
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

var MyIcon = L.Icon.extend({
    iconUrl: 'img/windmill.png',
    shadowUrl: null,
    iconSize: new L.Point(64, 64),
    shadowSize: null,
    iconAnchor: new L.Point(32, 32),
    popupAnchor: new L.Point(-3, -76)
});

var icon = new MyIcon();

var markerpos = new L.LatLng(56.2,11.2);
var marker = new L.Marker(markerpos, {icon: icon}), marker2 = new L.Marker(new L.LatLng(56.2,9.2), {icon: icon});

map.addLayer(marker).addLayer(marker2);

marker.on('click', function(e) {
	
    var page = "chart.php"
    var pagetitle = "Chart " + '(' + this.getLatLng().lat.toFixed(3) + ', ' + this.getLatLng().lng.toFixed(3) + ')';
    var $dialog = $( "#dialog-form" )
                .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
                .dialog({
                    autoOpen: false,
                    modal: true,
                    height: screen.height*0.7,
                    width: screen.width*0.8,
                    title: pagetitle,
                    close: function(ev, ui) { $("#map").fadeTo("slow", 1); }
                });
                $("#map").fadeTo("slow", 0.3);
                $dialog.dialog('open');
    //alert("test " + '(' + this.getLatLng().lat.toFixed(3) + ', ' + this.getLatLng().lng.toFixed(3) + ')');
});

map.on('click', onMapClick);

function onMapClick(e) {
    var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';
	var popup = new L.Popup();
    popup.setLatLng(e.latlng);
    popup.setContent("You clicked the map at " + latlngStr);

    map.openPopup(popup);
}

</script>
<div class="demo">
	<div id="dialog-form" title="Chart">
		<p>hallo</p>
	</div>
</div>



</body>
</html>