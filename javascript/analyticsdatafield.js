;(function($) {
	var analyticsDataField = window.analyticsDataField || {},
		gaVars = window.gaVars || {};

	analyticsDataField.parseSelector = '.analyticsdata-parser';

	analyticsDataField.outputGaTracker = function(t) {
		if(!t) return;

		var $fields = $(this.parseSelector).not('processed'),
			that = this;

		if($fields.length) {
			$fields.each(function() {
				var $me = $(this),
					id = this.id,
					field = $me.data('field'),
					restrictTo = $me.data('restrictTo'),
					tName = t.get('name'),
					output = [];

				$me.addClass('processed');

				if(!field || (restrictTo && tName != restrictTo)) return true;

				if(t.get('referrer'))
					output.push(that.createGaTrackerInput(id, field, tName, 'referrer', t.get('referrer')));
				if(t.get('campaignName'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignName', t.get('campaignName')));
				if(t.get('campaignSource'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignSource', t.get('campaignSource')));
				if(t.get('campaignMedium'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignMedium', t.get('campaignMedium')));
				if(t.get('campaignKeyword'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignKeyword', t.get('campaignKeyword')));
				if(t.get('campaignContent'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignContent', t.get('campaignContent')));
				if(t.get('campaignId'))
					output.push(that.createGaTrackerInput(id, field, tName, 'campaignId', t.get('campaignId')));

				if(t.get('screenResolution'))
					output.push(that.createGaTrackerInput(id, field, tName, 'screenResolution', t.get('screenResolution')));
				if(t.get('viewportSize'))
					output.push(that.createGaTrackerInput(id, field, tName, 'browserSize', t.get('viewportSize')));
				if(t.get('screenColors'))
					output.push(that.createGaTrackerInput(id, field, tName, 'screenColors', t.get('screenColors')));
				if(t.get('encoding'))
					output.push(that.createGaTrackerInput(id, field, tName, 'encoding', t.get('encoding')));
				if(t.get('language'))
					output.push(that.createGaTrackerInput(id, field, tName, 'language', t.get('language')));
				if(t.get('javaEnabled'))
					output.push(that.createGaTrackerInput(id, field, tName, 'javaEnabled', t.get('javaEnabled')));
				if(t.get('flashVersion'))
					output.push(that.createGaTrackerInput(id, field, tName, 'flashVersion', t.get('flashVersion')));

				if(t.get('location'))
					output.push(that.createGaTrackerInput(id, field, tName, 'pageLocation', t.get('location')));
				if(t.get('hostname'))
					output.push(that.createGaTrackerInput(id, field, tName, 'domain', t.get('hostname')));
				if(t.get('documentPath'))
					output.push(that.createGaTrackerInput(id, field, tName, 'pagePath', t.get('documentPath')));
				if(t.get('title'))
					output.push(that.createGaTrackerInput(id, field, tName, 'pageTitle', t.get('title')));

				output.push(that.createGaTrackerInput(id, field, tName, 'pageSession', +new Date()));

				if(output.length)
					$me.append(output.join("\n"));

				return true;
			});
		}
	};

	analyticsDataField.createGaTrackerInput = function(id, field, tName, name, val) {
		return '<input type="hidden" id="' + id + '-' + field.replace(/\[\]/g, '-') + '-' + tName + '-' + name + '" name="' + field + '[' + tName + ']' + '[' + name + ']' + '" value="' + val + '" />';
	};

	if(gaVars) {
		var that = window.analyticsDataField;

		for (var key in gaVars) {
			if (gaVars.hasOwnProperty(key) && gaVars[key].variable) {
				var trackers = window[gaVars[key].variable].getAll();

				if(trackers.length) {
					for (var i=0; i < trackers.length; ++i) {
						that.outputGaTracker(trackers[i]);
					}
				}
			}
		}
	}

})(jQuery);