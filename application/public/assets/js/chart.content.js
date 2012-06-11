WDV.Chart = {
	_columns: [],
	_data: [],
	_today: null,
	_month: [
	"January",
	"February",
	"March",
	"April",
	"May",
	"June",
	"July",
	"August",
	"September",
	"October",
	"November",
	"December"
	],
	_view: null,
	_views: {
		DAILY: 1,
		WEEKLY: 2,
		TWOWEEKS: 3
	},
	_viewsrange: [
	0,
	1, // Daily
	7, // Weekly
	14 // 2-weeks
	],
	_url: null,
	_id: null,
	_interval: 0,
	
	getJson: function (col, from, to) {
		var json = null;
		$.ajax({
			'async': false,
			'global': false,
			'url': WDV.Chart._url + '?id=' + WDV.Chart._id + '&c='+col+'&f='+from+'&t='+to,
			'dataType': "json",
			'success': function (data) {
				json = data;
			}
		});
		return json;  
	},
	
	GetViewRange: function() {
		return parseInt(this._viewsrange[this._view]);
	},
	
	GetViewRangeFor: function(view) {
		return parseInt(this._viewsrange[view]);
	},
	
	isDaily: function() {
		return this._view == this._views.DAILY;
	},
	
	isWeekly: function() {
		return this._view == this._views.WEEKLY;
	},
	
	isTwoWeeks: function() {
		return this._view == this._views.TWOWEEKS;
	},
	
	moveChart: function(days, hours) {
		WDV.Chart._today.setDate(WDV.Chart._today.getDate() + days);
		WDV.Chart._today.setHours(WDV.Chart._today.getHours() + hours);
		var offsetDays = this.GetViewRange();
		var f = new Date(WDV.Chart._today.getFullYear(), WDV.Chart._today.getMonth(), WDV.Chart._today.getDate() + offsetDays, WDV.Chart._today.getHours(), WDV.Chart._today.getMinutes(), WDV.Chart._today.getSeconds(), WDV.Chart._today.getMilliseconds());
		if (f > WDV.Chart._today)
		{
			WDV.Chart.plotdata(WDV.Chart._today, f);
		}
		else
		{
			WDV.Chart.plotdata(f, WDV.Chart._today);
		}
	},
			
	Init: function() {
		// Temp fix for date
		WDV.Chart.getArray(new Date(2010, 01, 01), new Date(2010, 01, 14));
		WDV.Chart._today = new Date(2010, 01, 01);
		
			
		$('.label_check').each(function(){
			if ($(this).children('input').is(':checked'))
			{
				$(this).children('i').addClass('icon-ok-sign');
			}
			else
			{
				$(this).children('i').addClass('icon-remove-sign');
			}
		});
		$('.label_check').click(function(){
			$child = $(this).children('i');
			$(this).children('input').prop('checked', !$(this).children('input').is(':checked'));
			if ($(this).children('input').is(':checked'))
			{
				$child.addClass('icon-ok-sign');
				$child.removeClass('icon-remove-sign');
			}
			else
			{
				$child.removeClass('icon-ok-sign');
				$child.addClass('icon-remove-sign');
			}
				
		});
			
		var placeholder = $("#placeholder");
		// Placeholder (the plot)
		plot = $.plot(placeholder, WDV.Chart.getArray(), { 
			xaxis: { 	
				mode: "time",
				min: new Date(WDV.Chart._today.getFullYear(), WDV.Chart._today.getMonth(), WDV.Chart._today.getDate() - WDV.Chart.GetViewRangeFor(WDV.Chart._views.TWOWEEKS), 0, 0, 0).getTime(),
				max: WDV.Chart._today.getTime()
			},
			legend: {
				position: 'ne'
			},

			series: {
				lines: {
					show: true , 
					shadowSize:0
				}
			},
			clickable: true,
			hoverable: true
		});
    
		// Check checkbox
		$(".label_check").click(function() {
			WDV.Chart.plotdata(new Date(WDV.Chart._today.getFullYear(), WDV.Chart._today.getMonth(), WDV.Chart._today.getDate() - WDV.Chart.GetViewRange()), WDV.Chart._today);
		});
	
		// Backfast button (-14 days in 2-week view, -7 days in weekly view, -1 day in daily)
		$("#fast-backward").click(function () {
			WDV.Chart.moveChart(-1 * WDV.Chart.GetViewRangeFor(WDV.Chart._view - 1), 0);
		});

		// Back button (- 7 days, - 1 day, - 1 hour)
		$("#backward").click(function () {
			if (WDV.Chart.isWeekly() || WDV.Chart.isTwoWeeks())
			{
				// Move in days (1 or 7 days)
				WDV.Chart.moveChart(-1 * WDV.Chart.GetViewRangeFor(WDV.Chart._view - 1), 0);
			}
			else
			{
				// Move in hours
				WDV.Chart.moveChart(0, -1);
			}
		});

		// WDV.Chart._today button
		$("#play").click(function () {
			if (WDV.Chart._interval == 0)
			{
				if (!WDV.Chart.isDaily())
				{
					$("#viewtype button:last-child").click();
					WDV.Chart.moveChart(0,0); // Prevent it skipping the first day
				}
				
				WDV.Chart._interval = setInterval(function() {
					WDV.Chart.moveChart(0, 1);
				}, /*WDV.Settings.Radar.speed*/200 * 6);
				$(this).children('i').removeClass('icon-play').addClass('icon-pause');
			}
			else
			{
				clearTimeout(WDV.Chart._interval);
				WDV.Chart._interval = 0;
				$(this).children('i').removeClass('icon-pause').addClass('icon-play');
			}
		});

		// Forward button (7 days, 1 day, 1 hour)
		$("#forward").click(function () {
			if (WDV.Chart.isWeekly() || WDV.Chart.isTwoWeeks())
			{
				// Move in days (1 or 7 days)
				WDV.Chart.moveChart(WDV.Chart.GetViewRangeFor(WDV.Chart._view - 1), 0);
			}
			else
			{
				// Move in hours
				WDV.Chart.moveChart(0, 1);
			}
		});

		// Forwardfast button (14 days in 2-week view, 7 days in weekly view, 1 day in daily)
		$("#fast-forward").click(function () {
			WDV.Chart.moveChart(WDV.Chart.GetViewRange(), 0);
		});
	
		// Change view - checks if 2-week or weekly view is activated.  
		$("#viewtype button").click(function () {
			WDV.Chart._view = $(this).val();
			var dayoffset = 0;
			if (WDV.Chart._view == 1) {
				dayoffset = 1;
			} else if (WDV.Chart._view == 2) {
				dayoffset = 7;
			} else if (WDV.Chart._view == 3) {
				dayoffset = 14;
			}
			WDV.Chart._today = new Date(WDV.Chart._today.getFullYear(), WDV.Chart._today.getMonth(), WDV.Chart._today.getDate()-WDV.Chart.GetViewRange() + dayoffset, WDV.Chart._today.getHours(), WDV.Chart._today.getMinutes(), WDV.Chart._today.getSeconds(), WDV.Chart._today.getMilliseconds());
			var f = new Date(WDV.Chart._today.getFullYear(), WDV.Chart._today.getMonth(), WDV.Chart._today.getDate()-WDV.Chart.GetViewRange(), WDV.Chart._today.getHours(), WDV.Chart._today.getMinutes(), WDV.Chart._today.getSeconds(), WDV.Chart._today.getMilliseconds());
			WDV.Chart.plotdata(f, WDV.Chart._today);
		});
		
		$('#viewtype button:first-child').click();
		$('.dropdown-menu li:first-child .label_check').click();
		WDV.Chart.moveChart(0, 0);
	},
	getTimeStamp: function(date) {
		return WDV.getTimeStamp(date);
	},
	plotdata: function(sDate, eDate) {
		for(i = 0; i < this._columns.length; i++)
		{
			if ($("#cb_" + this._columns[i]).is(':checked')) 
			{
				WDV.Chart._data[this._columns[i]] = WDV.Chart.getJson(this._columns[i], WDV.Chart.getTimeStamp(sDate), WDV.Chart.getTimeStamp(eDate));
			}
		}
		
		var temps = new Date(sDate.getFullYear(), sDate.getMonth(), sDate.getDate()+1);
		var tempe = new Date(eDate.getFullYear(), eDate.getMonth(), eDate.getDate()+1);
		$("#zoom").text(temps.getDate() + ". " + WDV.Chart._month[temps.getMonth()] + " " + temps.getFullYear() + " \t - \t " + tempe.getDate() + ". " + WDV.Chart._month[tempe.getMonth()] + " " + tempe.getFullYear());
	
		plot = $.plot($('#placeholder'), WDV.Chart.getArray(sDate, eDate), {
			xaxis: {
				mode: "time"
			}
		});
		plot.setupGrid();
		plot.draw();
	},
	getArray: function (mind, maxd) {
		var array = [];
		var j = 0;
		for(i = 0; i < this._columns.length; i++)
		{
			if ($("#cb_" + this._columns[i]).is(':checked')) {
				if (WDV.Chart._data[this._columns[i]].length == 0)
				{
					WDV.Chart._data[this._columns[i]] = WDV.Chart.getJson(this._columns[i], WDV.Chart.getTimeStamp(mind), WDV.Chart.getTimeStamp(maxd));
				}
				array[j++] = {
					data: WDV.Chart._data[WDV.Chart._columns[i]], 
					label: WDV.Chart._columns[i], 
					yaxis: j
				};
			}
		}
		return 	array;
	}
};