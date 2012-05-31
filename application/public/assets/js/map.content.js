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
				iconUrl: WDV.Settings.Icon.iconUrl.windmill,
				shadowUrl: WDV.Settings.Icon.shadowUrl,
				iconSize: WDV.Settings.Icon.iconSize,
				shadowSize: WDV.Settings.Icon.shadowSize,
				iconAnchor: WDV.Settings.Icon.iconAnchor,
				popupAnchor: WDV.Settings.Icon.popupAnchor
			});
			WDV._iconTemplateRD = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.iconUrl.radar,
				shadowUrl: WDV.Settings.Icon.shadowUrl,
				iconSize: WDV.Settings.Icon.iconSize,
				shadowSize: WDV.Settings.Icon.shadowSize,
				iconAnchor: WDV.Settings.Icon.iconAnchor,
				popupAnchor: WDV.Settings.Icon.popupAnchor
			});
			WDV._iconTemplateRDimg = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.iconUrl.radar,
				shadowUrl: WDV.Settings.Icon.shadowUrl,
				iconSize: new L.Point(240, 240),
				shadowSize: WDV.Settings.Icon.shadowSize,
				iconAnchor: WDV.Settings.Icon.iconAnchor,
				popupAnchor: WDV.Settings.Icon.popupAnchor
			});
			
			/*WDV._map.on('click', function(e) {
				var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';
				var popup = new L.Popup();
				popup.setLatLng(e.latlng);
				popup.setContent("You clicked the map at " + latlngStr);

				WDV._map.openPopup(popup);
			});*/
			
			WDV._map.on('viewreset', function() {
				WDV.UpdateRadarSizes();
			});
			WDV.InitWindfarms();
			WDV.InitRadars();
			WDV.UpdateRadarSizes();
		}
	},
	InitWindfarms: function() {
		this._windfarms = [];
		for (i = 0; i < WDV.Settings.Windfarm.positions.length; i++) {
			// Create the marker
			this._windfarms[i] = new L.Marker(new L.LatLng(WDV.Settings.Windfarm.positions[i][0], WDV.Settings.Windfarm.positions[i][1]), {
				icon: new this._iconTemplateWM()
			});
			
			// Click event
			this._windfarms[i].on('click', function(e) {
				var page = "chart/?lat=" + this.getLatLng().lat.toFixed(3) + "&lng=" + this.getLatLng().lng.toFixed(3);
				var $dialog = $( "#dialog-form" )
				.html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: WDV.Settings.Marker.autoOpen,
					modal: WDV.Settings.Marker.modal,
					height: WDV.Settings.Marker.height,
					width: WDV.Settings.Marker.width,
					title: WDV.Settings.Marker.title.replace('LAT', this.getLatLng().lat.toFixed(3)).replace('LNG', this.getLatLng().lng.toFixed(3)),
					close: WDV.Settings.Marker.close
				});
				$("#map").fadeTo("slow", 0.3);
				$dialog.dialog('open');
			});
			
			// Put it on the map
			WDV._map.addLayer(this._windfarms[i]);
		}
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
			this._radars[i].animate = function() {
				WDV.RotateRadar(this.index);
				this.intval = setInterval("WDV.RotateRadar('"+this.index+"')", WDV.Settings.Radar.speed);
			};
			
			// Click event
			this._radars[i].on('click', function(e) {
				// rotate images
				if (this.intval == 0 || this.intval == undefined)
				{
					this.animate();
				} 
				else
				{
					clearTimeout(this.intval);
					this.intval = 0;
				}
			});
			
			// Put it on the map
			WDV._map.addLayer(this._radars[i]);
		}
	},
	RotateRadar: function(radar) {
		console.log("Rotating " + radar + " with current = " + this._radars[radar].current);
		console.log(this._radars[radar]);
		if (this._radars[radar].current < this._radars[radar].images.length)
		{
			this._radars[radar]._icon.src = this._radars[radar].images[this._radars[radar].current++];
		}
		else
		{
			clearTimeout(this._radars[radar].intval);
			this._radars[radar].intval = 0;
			this._radars[radar].current = 0;
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

		var t = [
		width = Math.round(opt_px * (meters / maxMeters)) + 'px',
		meters = meters,
		centerLat = centerLat,
		left = left,
		right = right,
		maxMeters = maxMeters,
		xx = (Math.round(opt_px * (meters / maxMeters)) / meters) * 240000
		];
		var _width = ((Math.round(opt_px * (meters / maxMeters))-1) / meters) * 240000;
		for(var i = 0; i < WDV._radars.length; i++)
		{
			WDV._radars[i]._icon.style.width = _width;
			WDV._radars[i]._icon.style.height = _width;
			WDV._radars[i]._icon.style.marginTop = (-1*(_width/2));
			WDV._radars[i]._icon.style.marginLeft = (-1*(_width/2));
			
		}
		console.log("changed size to " + _width);
	},
	PlayAllRadars: function() {
		for (i = 0; i < WDV._radars.length; i++) {
			
			this._radars[i].current = 0;
			this._radars[i].animate();
		}
	}
};

WDV.Settings = {
	Icon: {
		iconUrl: {
			windmill: 'assets/img/windmill.png',
			radar: 'assets/img/radar.png'
		},
		shadowUrl: null,
		iconSize: new L.Point(64, 64),
		shadowSize: null,
		iconAnchor: new L.Point(32, 32),
		popupAnchor: new L.Point(-3, -75)
	},
	Marker: {
		autoOpen: false,
		modal: true,
		height: screen.height * 0.7,
		width: screen.width * 0.8,
		title: 'Chart (LAT, LNG)',
		close: function(ev, ui) {
			$("#map").fadeTo("slow", 1);
		}
	},
	Windfarm: {
		positions: []
	},
	Radar: {
		positions: [],
		images: [],
		speed: 1000
	}
};

