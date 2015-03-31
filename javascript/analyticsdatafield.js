var EA = window.EA || {};

EA.dataField = (function(dataField, $) {
	var $inputs = [],
		$done = [];

	if(!dataField.hasOwnProperty('parseSelector'))
		dataField.parseSelector = '.analyticsdata-parser';

	dataField.render = function(name) {
		var $fields = $(dataField.parseSelector).not('.analyticsdata-parser_processed_' + name);

		if($fields.length) {
			$fields.each(function() {
				var $this = $(this),
					id = this.id,
					field = $this.data('field'),
					restrictTo = $this.data('restrictTo');

				if(!field || (restrictTo && name != restrictTo) || $done.indexOf(name) !== -1)
					return true;

				$this.removeClass('analyticsdata-parser_processing_' + name)
					.addClass('analyticsdata-parser_processed_' + name);

				if($inputs.length) {
					for(var i=0;i<$inputs.length;i++) {
						$this.append(dataField.createTrackerInput(id, field, $inputs[i][0], $inputs[i][1]));
					}
				}

				$inputs = [];
				$done.push(name);

				return true;
			});
		}
	};

	dataField.outputGaTracker = function(tracker) {
		if(!tracker) return;

		var types = dataField.GA_types || {},
			trackerName = tracker.get('name');

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
				$inputs.push([[trackerName, types[type]], tracker.get(type)]);
		}

		dataField.render(trackerName);
	};

	dataField.createTrackerInput = function(id, field, name, val) {
		return '<input type="hidden" id="' + id + '-' + field.replace(/\[\]/g, '-') + '-' + name.join('-') + '" name="' + field + '[' + name.join('][') + ']' + '" value="' + val + '" />';
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

	$inputs.push([['pageSession'], +new Date()]);
	dataField.render('+defaults');

	return dataField;
})(EA.dataField || {}, window.jQuery);