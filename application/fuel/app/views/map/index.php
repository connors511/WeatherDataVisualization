<div id="map" style="width:100%;height:100%;"></div>
<script type="text/javascript">
$(function() {
	$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true
		});
});

WDV.Settings.Windfarm.positions = [<?php $mills = array(); foreach($windmills as $mill) { $mills[] = "[{$mill['latitude']},{$mill['longitude']},'{$mill['name']}']"; } echo implode(',',$mills); ?>];

WDV.Settings.Radar.positions = [<?php $rads = array(); foreach($radars as $rad) { $rads[] = "[{$rad['latitude']},{$rad['longitude']}]"; } echo implode(',',$rads); ?>];
WDV.Settings.Radar.images = [[<?php $arr = array(); for($i=1;$i<=141;$i++){ $arr[] = "['radar/{$i}']"; } echo implode(",",$arr); ?>],
				[<?php $arr = array(); for($i=142;$i<=285;$i++){ $arr[] = "['radar/{$i}']"; } echo implode(",",$arr); ?>]];
WDV.Settings.Radar.speed = 200;
WDV.Init();

</script>
<div id="dialog-form" title="Chart"></div>
