var EA = window.EA || {};

EA.dataField = (function(dataField, $) {
	var $inputs = [],
		pageTime = +new Date();

	if(!dataField.hasOwnProperty('parseSelector'))
		dataField.parseSelector = '.analyticsdata-parser';

	dataField.render = function(name) {
		var $fields = $(dataField.parseSelector).not('.analyticsdata-parser_processed_' + name);

		if($fields.length) {
			$fields.each(function() {
				var $this = $(this),
					id = this.id,
                    $done = $this.data('ea:done') || [],
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

				$done.push(name);
                $this.data('ea:done', $done);

				return true;
			});
		}

		$inputs = [];
	};

	dataField.outputGaTracker = function(id, tracker) {
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
				$inputs.push([[id + '][' + trackerName, types[type]], tracker.get(type)]);
		}

		dataField.render(trackerName);
	};

	dataField.createTrackerInput = function(id, field, name, val) {
        if(val === null) val = '';
		return '<input type="hidden" id="' + id + '-' + field.replace(/\[\]/g, '-') + '-' + name.join('-') + '" name="' + field + '[' + name.join('][') + ']' + '" value="' + val + '" />';
	};

	dataField.render_all = function() {
		if(EA.hasOwnProperty('GA') && EA.GA.hasOwnProperty('trackers')) {
			for (var trackerName in EA.GA.trackers) {
				if (EA.GA.trackers.hasOwnProperty(trackerName)) {
					EA.GA.trackers[trackerName].initCallbacks = EA.GA.trackers[trackerName].initCallbacks || {};

					EA.GA.trackers[trackerName].initCallbacks.parseFields = function(trackers) {
						if(!trackers || !trackers.length)
							return;

						for (var i = 0; i < trackers.length; ++i) {
							dataField.outputGaTracker(trackerName, trackers[i]);
						}
					};

					if(window.hasOwnProperty(trackerName) && EA.GA.trackers[trackerName].hasOwnProperty('trackers')) {
						EA.GA.trackers[trackerName].initCallbacks.parseFields(EA.GA.trackers[trackerName].trackers);
					}
				}
			}
		}

		$inputs.push([['pageSession'], pageTime]);
	};

	dataField.render_all();

	if($.hasOwnProperty('ajaxComplete')) {
		$(document).ajaxComplete(function() {
			EA.dataField.render_all();
		});
	}

	return dataField;
})(EA.dataField || {}, window.jQuery || window.zepto);