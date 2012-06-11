/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


WDV = {
	_map: null,
	_pos: null,
	_cloudmade: null,
	_initialized: false,
	_windfarms: [],
	_radars: [],
	_iconTemplateWM: null,
	_iconTemplateRD: null,
	_iconTemplateRDimg: null,
		
	Init: function() {
		if (!this._initialized) {
			WDV._map = new L.Map('map');
			WDV._cloudmade = new L.TileLayer('http://{s}.tile.cloudmade.com/6d31e7d426dc40368e1b7bd1f07c8aa6/997/256/{z}/{x}/{y}.png', {
				attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery ? <a href="http://cloudmade.com">CloudMade</a>',
				maxZoom: 18
			});
			
			// Our view position
			WDV._pos = new L.LatLng(56.200, 11.200); 
			// Set the view
			WDV._map.setView(WDV._pos, 7).addLayer(WDV._cloudmade);
			
			WDV._iconTemplateWM = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.windmill.iconUrl,
				shadowUrl: WDV.Settings.Icon.windmill.shadowUrl,
				iconSize: WDV.Settings.Icon.windmill.iconSize,
				shadowSize: WDV.Settings.Icon.windmill.shadowSize,
				iconAnchor: WDV.Settings.Icon.windmill.iconAnchor,
				popupAnchor: WDV.Settings.Icon.windmill.popupAnchor
			});
			WDV._iconTemplateRD = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.radar.iconUrl,
				shadowUrl: WDV.Settings.Icon.radar.shadowUrl,
				shadowSize: WDV.Settings.Icon.radar.shadowSize,
				iconAnchor: WDV.Settings.Icon.radar.iconAnchor,
				popupAnchor: WDV.Settings.Icon.radar.popupAnchor
			});
			WDV._iconTemplateRDimg = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.radar.iconUrl,
				shadowUrl: WDV.Settings.Icon.radar.shadowUrl,
				iconSize: WDV.Settings.Icon.radar.iconSize,
				shadowSize: WDV.Settings.Icon.radar.shadowSize,
				iconAnchor: WDV.Settings.Icon.radar.iconAnchor,
				popupAnchor: WDV.Settings.Icon.radar.popupAnchor
			});
			
			WDV._map.on('viewreset', function() {
				WDV.UpdateRadarSizes();
			});
			WDV.InitWindfarms();
			WDV.InitRadars();
			WDV.UpdateRadarSizes();
			// Remove the zoom control
			$('.leaflet-control-zoom').css('display','none');
			
			// Init time pickers
			$('#intervalfrom').datetimepicker({
				onSelect: function() {
					WDV.UpdateRadarData();
				}
			});
			var prevWeek = new Date();
			prevWeek.setDate(prevWeek.getDate() - 7);
			$('#intervalfrom').datetimepicker('setDate', prevWeek);
			$('#intervalto').datetimepicker({
				onSelect: function() {
					WDV.UpdateRadarData();
				}
			});
			$('#intervalto').datetimepicker('setDate', new Date());
			
			WDV.UpdateRadarData();
		}
	},
	InitWindfarms: function() {
		this._windfarms = [];
		for (i = 0; i < WDV.Settings.Windfarm.positions.length; i++) {
			// Create the marker
			this._windfarms[i] = new L.Marker(new L.LatLng(WDV.Settings.Windfarm.positions[i][0], WDV.Settings.Windfarm.positions[i][1]), {
				icon: new this._iconTemplateWM()
			});
			this._windfarms[i].name = WDV.Settings.Windfarm.positions[i][2];
			this._windfarms[i].id = WDV.Settings.Windfarm.positions[i][3];
			this._windfarms[i].setZIndexOffset(WDV.Settings.Marker.zIndexOffset);
			
			// Click event
			this._windfarms[i].on('click', function(e) {
				var page = "chart/?id=" + this.id;
				var $dialog = $( "#dialog-form" )
				.html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: WDV.Settings.Marker.autoOpen,
					modal: WDV.Settings.Marker.modal,
					height: WDV.Settings.Marker.height,
					width: WDV.Settings.Marker.width,
					title: WDV.Settings.Marker.title.replace('LAT', this.getLatLng().lat.toFixed(3)).replace('LNG', this.getLatLng().lng.toFixed(3)).replace('NAME',this.name),
					close: WDV.Settings.Marker.close,
					zIndex: 3000,
					resizable: false,
					position: 'bottom',
					dialogClass: 'popup'
				});
				$("#dialog-form").css('padding','0'); // minimize white border
				$(".ui-dialog-titlebar.ui-widget-header").remove(); // Remove the jquery UI header
				$dialog.dialog('open');
			});
			
			// Put it on the map
			WDV._map.addLayer(this._windfarms[i]);
		}
	},
	CloseDialog: function() {
		$("#dialog-form").dialog('close');
	},
	InitRadars: function() {
		this._radars = [];
		for (i = 0; i < WDV.Settings.Radar.positions.length; i++) {
			// Create the marker
			this._radars[i] = new L.Marker(new L.LatLng(WDV.Settings.Radar.positions[i][0], WDV.Settings.Radar.positions[i][1]), {
				icon: new this._iconTemplateRDimg()
			});
			this._radars[i].images = WDV.Settings.Radar.images[i];
			this._radars[i].current = 0;
			this._radars[i].index = i;
			this._radars[i].range = WDV.Settings.Radar.positions[i][2];
			this._radars[i].animate = function() {
				WDV.RotateRadar(this.index);
				this.intval = setInterval("WDV.RotateRadar('"+this.index+"')", WDV.Settings.Radar.speed);
			};
			this._radars[i].hiding = [];
			
			// Click event
			this._radars[i].on('click', function(e) {
				// rotate images
				if (this.images == null)
				{
					WDV.UpdateRadarData();
				}
				if (this.images == null)
				{
					alert("No radar images available for the selected from date.\nPlease try again later.");
					return;
				}
				if (this.intval == 0 || this.intval == undefined)
				{
					this.animate();
				} 
				else
				{
					for(i = 0; i < this.hiding.length; i++)
					{
						$(WDV._windfarms[this.hiding[i]]._icon).fadeIn('slow');
						
					}
					this.hiding = [];
					clearTimeout(this.intval);
					this.intval = 0;
				}
			});
			this._radars[i].on('dblclick',function() {
				// Stop animation
				clearTimeout(this.intval);
				this.intval = 0;
				this.current = 0;
				this._icon.src = WDV.Settings.Icon.radar.iconUrl;
				// Restore windfarms
				if (this.hiding != undefined && this.hiding.length > 0)
				{

					for(i = 0; i < this.hiding.length; i++)
					{
						$(WDV._windfarms[this.hiding[i]]._icon).fadeIn('slow');

					}
				}
				this.hiding = [];
			});
			
			// Put it on the map
			WDV._map.addLayer(this._radars[i]);
		}
	},
	RotateRadar: function(radar) {
		//console.log("Rotating " + radar + " with current = " + this._radars[radar].current);
		//console.log(this._radars[radar]);
		if (this._radars[radar].current < this._radars[radar].images.length)
		{
			this._radars[radar]._icon.src = this._radars[radar].images[this._radars[radar].current++];
			if (this._radars[radar].hiding == undefined || this._radars[radar].hiding.length == 0)
			{
				for(i = 0; i < WDV._windfarms.length; i++)
				{
					if (this._radars[radar].getLatLng().distanceTo(WDV._windfarms[i].getLatLng()) < (WDV.Settings.Radar.range / 2))
					{
						this._radars[radar].hiding.push(i);
						$(WDV._windfarms[i]._icon).fadeOut('slow');
					}
				}
			}
		}
		else
		{
			clearTimeout(this._radars[radar].intval);
			this._radars[radar].intval = 0;
			this._radars[radar].current = 0;
			this._radars[radar]._icon.src = WDV.Settings.Icon.radar.iconUrl;
			// Restore windfarms
			if (this._radars[radar].hiding != undefined && this._radars[radar].hiding.length > 0)
			{
				
				for(i = 0; i < this._radars[radar].hiding.length; i++)
				{
					$(WDV._windfarms[this._radars[radar].hiding[i]]._icon).fadeIn('slow');

				}
			}
			this._radars[radar].hiding = [];
		}
	},
	UpdateRadarSizes: function() {
		var opt_px = 200;
		var bounds = WDV._map.getBounds();
		centerLat = bounds.getCenter().lat;

		left = new L.LatLng(centerLat, bounds.getSouthWest().lng);
		right = new L.LatLng(centerLat, bounds.getNorthEast().lng);

		size = WDV._map.getSize();

		maxMeters = left.distanceTo(right) * (opt_px / size.x);
				
		var pow10 = Math.pow(10, (Math.floor(maxMeters) + '').length - 1),
		d = maxMeters / pow10;

		d = d >= 10 ? 10 : d >= 5 ? 5 : d >= 2 ? 2 : 1;

		meters = pow10 * d;

		for(var i = 0; i < WDV._radars.length; i++)
		{
			var _width = ((Math.round(opt_px * (meters / maxMeters))-1) / meters) * WDV._radars[i].range;
			WDV._radars[i]._icon.style.width = _width + 'px';
			WDV._radars[i]._icon.style.height = _width + 'px';
			WDV._radars[i]._icon.style.marginTop = (-1*(_width/2)) + 'px';
			WDV._radars[i]._icon.style.marginLeft = (-1*(_width/2)) + 'px';
		}
		console.log("changed size to " + _width);
	},
	UpdateRadarData: function() {
		for(var i = 0; i < WDV._radars.length; i++)
		{
			var from = $('#intervalfrom').datetimepicker('getDate');
			if (from != null)
			{
				from = WDV.getTimeStamp(from);
			}
			
			WDV._radars[i].images = WDV.loadData(WDV._radars[i].getLatLng(), from)
		}
	},
	loadData: function (pos, from) {
		var json = null;
		var _url = WDV.Settings.Radar.url + '?&lat=' + pos.lat.toFixed(3) + '&lng=' + pos.lng.toFixed(3);
		if (from != undefined && from != null) {
			_url = _url + '&f='+from;
		}
		$.ajax({
			'async': false,
			'global': false,
			'url': _url,
			'dataType': "json",
			'success': function (data) {
				json = data;
			}
		});
		return json;  
	},
	getTimeStamp: function(date) {
		var d = "0" + (date.getDate()+1);
		var m = "0" + (date.getMonth()+1);
		var y = "" + date.getFullYear();
		var h = "0" + date.getHours();
		var min = "0" + date.getMinutes();
		return y+m.substring(m.length-2, m.length)+d.substring(d.length-2, d.length)+h.substring(h.length-2, h.length)+min.substring(min.length-2, min.length);
	}
};

WDV.Settings = {
	Icon: {
		windmill: {
			iconUrl: 'assets/img/windmill.png',
			shadowUrl: null,
			iconSize: new L.Point(64, 64),
			shadowSize: null,
			iconAnchor: new L.Point(32, 32),
			popupAnchor: new L.Point(-3, -75)
		},
		radar: {
			iconUrl: 'assets/img/radar.png',
			shadowUrl: null,
			iconSize: new L.Point(480, 480),
			shadowSize: null,
			iconAnchor: new L.Point(240, 240),
			popupAnchor: new L.Point(-3, -75)
		}
	},
	Marker: {
		autoOpen: false,
		modal: false,
		height: screen.height * 0.4,
		width: '100%',
		title: 'Chart for NAME (LAT, LNG)',
		close: function(ev, ui) {
			$("#map").fadeTo("slow", 1);
		},
		zIndexOffset: 500
	},
	Windfarm: {
		positions: []
	},
	Radar: {
		positions: [],
		images: [],
		url: '',
		speed: 1000, // Change image every milliseconds
		range: 480000 // Range in meters
	}
};