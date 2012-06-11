<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WeatherApp Map</title>
<?php
echo Asset::css(array(
    'bootstrap.min.css',
    'bootstrap-responsive.min.css',
    'leaflet.css',
    'style.css'
));
?>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.19/themes/base/jquery-ui.css" type="text/css" media="all" />
<style type="text/css">
	html, body, #map { /* html,body needs this for the map */
		width: 100%; 
		height: 100%;
		margin:0;
		padding:0;
	}

	fieldset { padding:0; border:0; margin-top:25px; }
	h1 { font-size: 1.2em; margin: .6em 0; }
	div#users-contain { width: 350px; margin: 20px 0; }
	div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
	div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	.ui-dialog .ui-state-error { padding: .3em; }
	.validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>
<?php
echo Asset::js(array(
    'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.19/jquery-ui.min.js',
    'bootstrap.min.js',
    'leaflet.js',
    'map.content.js',
    'jquery.ui.timepicker.js'
));
?>
</head>
<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<?php echo Html::anchor(Uri::base(false), 'WeatherApp', array('class' => 'brand')) ?>
			<ul id="loading" class="nav">
				<li>
					<a href="javascript:void(0)">
						<img src="assets/img/loading.png" width="24px" height="24px" />
						Parsing data <span id="jobCount"></span>
					</a>
				</li>
			</ul>
			<div class="btn-group pull-right">
				<button class="btn small" onClick="javascript:WDV._map.zoomIn();"><i class="icon-plus"></i></button>
				<button class="btn small" onClick="javascript:WDV._map.zoomOut();"><i class="icon-minus"></i></button>
			</div>
			<div class="nav-collapse">
				<ul class="nav">
					<li class="<?php echo Uri::segment(2) == '' ? 'active' : '' ?>">
						<?php echo Html::anchor('', 'Map'); ?>
					</li>
					
						<?php if($current_user): ?>
					<li><?php echo Html::anchor('admin/index', 'Admin'); ?></li>
					<?php else: ?>
					<li><?php echo Html::anchor('admin/login','Login'); ?></li>
					<?php endif; ?>
				</ul>		
				<div class="pull-right navbar-search">
					<input type="text" class="span2 search-query" id="intervalfrom" placeholder="From" value="" />
				</div>
			</div>
			
		</div>
	</div>
</div>
<div id="map" style="width:100%;height:100%;"></div>
<script type="text/javascript">
	WDV.Settings.Windfarm.positions = [<?php
		$mills = array();
		foreach ($windmills as $mill) {
			$mills[] = "[{$mill['latitude']},{$mill['longitude']},'{$mill['name']}','{$mill['id']}']";
		} echo implode(',', $mills);
	?>];

	WDV.Settings.Radar.positions = [<?php
		$rads = array();
		foreach ($radars as $rad) {
			$rads[] = "[{$rad['latitude']},{$rad['longitude']},{$rad['_range']}]";
		} echo implode(',', $rads);
	?>];

	WDV.Settings.Radar.images = [[<?php
		$arr = array();
		for ($i = 1; $i <= 141; $i++) {
			$arr[] = "['radar/{$i}.png']";
		} echo implode(",", $arr);
	?>],
	[<?php
		$arr = array();
		for ($i = 142; $i <= 285; $i++) {
			$arr[] = "['radar/{$i}']";
		} echo implode(",", $arr);
	?>]];
WDV.Settings.Radar.url = '<?php echo Uri::create('rest/radar/list.json'); ?>';
WDV.Settings.Jobs.url = '<?php echo Uri::create('rest/jobs/list.json'); ?>';
WDV.Settings.Radar.speed = 200;
WDV.Init();

	$(function() {
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true
		});
	});
</script>
<div id="dialog-form" title="Chart"></div>
</body>
</html>
