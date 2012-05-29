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
	_iconTemplate: null,
		
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
			
			WDV._iconTemplate = L.Icon.extend({
				iconUrl: WDV.Settings.Icon.iconUrl,
				shadowUrl: WDV.Settings.Icon.shadowUrl,
				iconSize: WDV.Settings.Icon.iconSize,
				shadowSize: WDV.Settings.Icon.shadowSize,
				iconAnchor: WDV.Settings.Icon.iconAnchor,
				popupAnchor: WDV.Settings.Icon.popupAnchor
			});
			
			WDV._map.on('click', function(e) {
				var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';
				var popup = new L.Popup();
				popup.setLatLng(e.latlng);
				popup.setContent("You clicked the map at " + latlngStr);

				WDV._map.openPopup(popup);
			});
			WDV.InitWindfarms();
		}
	},
	InitWindfarms: function() {
		this._windfarms = [];
		for (i = 0; i < WDV.Settings.Windfarm.positions.length; i++) {
			// Create the marker
			this._windfarms[i] = new L.Marker(new L.LatLng(WDV.Settings.Windfarm.positions[i][0], WDV.Settings.Windfarm.positions[i][1]), {
				icon: new this._iconTemplate()
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
	}
};

WDV.Settings = {
	Icon: {
		iconUrl: 'assets/img/windmill.png',
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
	}
};

WDV.Obj = {
	
};

/* Objects */
WDV.Obj.Marker = {
	position: null,
	marker: null
};