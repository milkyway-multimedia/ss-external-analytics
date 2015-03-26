var EA = window.EA || {};

EA.dataField = (function(dataField, $) {
	if(!dataField.hasOwnProperty('parseSelector'))
		dataField.parseSelector = '.analyticsdata-parser';

	dataField.outputGaTracker = function(tracker) {
		if(!tracker) return;

		var $fields = $(dataField.parseSelector).not('.analyticsdata-parser_processed'),
			that = this;

		if($fields.length) {
			$fields.each(function() {
				var $this = $(this),
					id = this.id,
					field = $this.data('field'),
					restrictTo = $this.data('restrictTo'),
					trackerName = tracker.get('name'),
					output = [];

				$this.removeClass('analyticsdata-parser_processing processing')
					.addClass('analyticsdata-parser_processed processed');

				if(!field || (restrictTo && trackerName != restrictTo)) return true;

				var types = dataField.types || {};

				types = $.extend({}, {
					'referrer': 'referrer',
					'dataSource': 'dataSource',
					'expId': 'experimentId',
					'expVar': 'experimentVariant',
					'campaignName': 'campaignName',
					'campaignSource': 'campaignSource',
					'campaignMedium': 'campaignMedium',
					'campaignKeyword': 'campaignKeyword',
					'campaignContent': 'campaignContent',
					'campaignId': 'campaignId',

					'screenResolution': 'screenResolution',
					'viewportSize': 'windowSize',
					'screenColors': 'screenColours',
					'encoding': 'encoding',
					'language': 'language',
					'javaEnabled': 'javaEnabled',
					'flashVersion': 'flashVersion',

					'appName': 'applicationName',
					'appId': 'applicationId',
					'appVersion': 'applicationVersion',
					'appInstallerId': 'applicationInstallerId',

					'location': 'pageLocation',
					'hostname': 'domain',
					'documentPath': 'pagePath',
					'title': 'pageTitle'
				}, types);

				for(var type in types) {
					if(types.hasOwnProperty(type) && tracker.get(type))
						output.push(that.createGaTrackerInput(id, field, trackerName, types[type], tracker.get(type)));
				}

				output.push(that.createGaTrackerInput(id, field, trackerName, 'pageSession', +new Date()));

				if(output.length)
					$this.append(output.join("\n"));

				return true;
			});
		}
	};

	dataField.createGaTrackerInput = function(id, field, tName, name, val) {
		return '<input type="hidden" id="' + id + '-' + field.replace(/\[\]/g, '-') + '-' + tName + '-' + name + '" name="' + field + '[' + tName + ']' + '[' + name + ']' + '" value="' + val + '" />';
	};

	if(EA.hasOwnProperty('GA') && EA.GA.hasOwnProperty('trackers')) {
		for (var trackerName in EA.GA.trackers) {
			if (EA.GA.trackers.hasOwnProperty(trackerName)) {
				EA.GA.trackers[trackerName].initCallbacks = EA.GA.trackers[trackerName].initCallbacks || {};

				EA.GA.trackers[trackerName].initCallbacks.parseFields = function(trackers) {
					if(!trackers || !trackers.length)
						return;

					for (var i = 0; i < trackers.length; ++i) {
						dataField.outputGaTracker(trackers[i]);
					}
				};
			}
		}
	}

	return dataField;
})(EA.dataField || {}, window.jQuery);