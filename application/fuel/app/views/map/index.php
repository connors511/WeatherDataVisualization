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
<link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.19/themes/base/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.8.19/jquery-ui.min.js" type="text/javascript"></script>
<script src="http://code.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>
<?php echo Asset::js('map.content.js'); ?>
<?php echo Asset::css('bootstrap.css'); ?>
<script>
$(function() {
$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true
		});
});
</script>
</head>
<body>
	<div class="topbar">
	    <div class="fill">
	        <div class="container">
	            <h3><?php echo Html::anchor('', 'WeatherApp'); ?></h3>
	            <ul>
	                <li class="<?php echo Uri::segment(2) == '' ? 'active' : '' ?>">
						<?php echo Html::anchor('', 'Map'); ?>
					</li>
	                
					<?php foreach (glob(APPPATH.'classes/controller/admin/*.php') as $controller): ?>
						
						<?php
						$section_segment = basename($controller, '.php');
						$section_title = Inflector::humanize($section_segment);
						?>
						
	                <li class="<?php echo Uri::segment(2) == $section_segment ? 'active' : '' ?>">
						<?php echo Html::anchor('admin/'.$section_segment, $section_title) ?>
					</li>
					<?php endforeach; ?>
	          </ul>
	        </div>
	    </div>
	</div>
<div id="map"></div>
<script type="text/javascript">

WDV.Settings.Windfarm.positions = [<?php $mills = array(); foreach($windmills as $mill) { $mills[] = "[{$mill['latitude']},{$mill['longitude']},'{$mill['name']}']"; } echo implode(',',$mills); ?>];

WDV.Settings.Radar.positions = [<?php $rads = array(); foreach($radars as $rad) { $rads[] = "[{$rad['latitude']},{$rad['longitude']}]"; } echo implode(',',$rads); ?>];
WDV.Settings.Radar.images = [[<?php $arr = array(); for($i=1;$i<=141;$i++){ $arr[] = "['radar/{$i}.png']"; } echo implode(",",$arr); ?>],
				[<?php $arr = array(); for($i=142;$i<=285;$i++){ $arr[] = "['radar/{$i}.png']"; } echo implode(",",$arr); ?>]];
WDV.Settings.Radar.speed = 200;
WDV.Init();

</script>
<div id="dialog-form" title="Chart">
</div>
</body>
</html>