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
		<?php echo Asset::css('bootstrap.css'); ?>
		<?php echo Asset::js('bootstrap.js'); ?>
		<?php echo Asset::js('map.content.js'); ?>
		<?php echo Asset::js('chart.content.js'); ?>

		<style type="text/css">
			html, body {
				height: 100%; /* make the percentage height on placeholder work */
			}
		</style>
		<script type="text/javascript">
<?php
$id = $_GET['id'];
?>
$(document).ready(function() {
	WDV.Chart._url = '<?php echo Uri::create('file/csv/list.json'); ?>';
	WDV.Chart._id = '<?php echo $id; ?>';
	WDV.Chart.Init();
});
<?php 
echo "WDV.Chart._columns = ['".implode("', '",$columns)."'];\n";
foreach($columns as $c)
{
	echo "WDV.Chart._data['{$c}'] = [];\n";
}
?>
		   </script>
	</head>
	<body>

		<div class="navbar navbar-fixed-top">
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<a class="brand" href="<?php Html::anchor('WeatherApp'); ?>">HR1 (56.200, 11.100)</a>

							<ul class="nav">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Data<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<?php
										foreach($columns as $c):
										?>
											<li>
												<label class="label_check" for="checkbox[<?php echo $c; ?>]">
													<i></i>
													<input id="cb_<?php echo $c; ?>" name="checkbox[<?php echo $c; ?>]" value="1" type="checkbox" />
													<?php echo Inflector::humanize(Inflector::underscore($c)); ?>
												</label>
											</li>
										<?php
										endforeach;
										?>
									</ul>
								</li>
							</ul>
								<div id="controlButtons" class="btn-group">
									<button class="btn" id="fast-backward"><i class="icon-fast-backward"></i></button>
									<button class="btn" id="backward"><i class="icon-backward"></i></button>
									<button class="btn" id="play"><i class="icon-play"></i></button>
									<button class="btn" id="forward"><i class="icon-forward"></i></button>
									<button class="btn" id="fast-forward"><i class="icon-fast-forward"></i></button>
								</div>
							<ul class="nav pull-right">
								<li>
									<div id="viewtype" class="btn-group" data-toggle="buttons-radio">
										<button class="btn" value="3">2-week</button>
										<button class="btn" value="2">Weekly</button>
										<button class="btn" value="1">Daily</button>
									</div>
								</li>
								<li>
									<a id="dialog-close-btn" class="close" href="javascript:parent.WDV.CloseDialog();">&times;</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	
	<div id="spacer"></div>
	<div id="zoom">Today</div>
	<!--<div class="button btn btn-primary" id="button">2-week view</div>-->
	<div id="placeholder"></div>
</body>
</html>
