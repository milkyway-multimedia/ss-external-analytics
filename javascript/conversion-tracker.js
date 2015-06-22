(function (mwm, $) {
    var EA = window.EA || {},
        tracking = [],
        trackConversions = function () {
            if (!EA.hasOwnProperty('conversion_trackers')) {
                return;
            }

            var type, i, events, element, once;

            for (type in EA.conversion_trackers) {
                if (!EA.conversion_trackers.hasOwnProperty(type)) {
                    continue;
                }

                events = EA.conversion_trackers[type].events || [type];
                element = EA.conversion_trackers[type].element || window;
                once = EA.conversion_trackers[type].once || false;

                if (element === 'window') {
                    element = window;
                }
                else if (element === 'document') {
                    element = document;
                }
                else if (typeof element == 'string' || element instanceof String) {
                    element = $ ? $(element) : document.querySelectorAll(element);
                }

                for (i = 0; i < events.length; i++) {
                    if (tracking.indexOf(type + ':' + events[i]) !== -1) {
                        continue;
                    }

                    tracking.push(type + ':' + events[i]);

                    (function (data, currentEvent) {
                        if(!data.hasOwnProperty('_defaults'))
                            data._defaults = {};

                        mwm.utilities.attachToEvent(element, currentEvent, function () {
                            EA.conversion(data);

                            if(data.hasOwnProperty('_trackEvent')) {
                                var eventDetails = {
                                    hitType: 'event',
                                    eventCategory: data._trackEvent.category ? data._trackEvent.category : currentEvent,
                                    eventAction: data._trackEvent.action ? data._trackEvent.action : '',
                                    eventLabel: data._trackEvent.label ? data._trackEvent.label : window.location.href
                                };

                                if(data._defaults.value)
                                    eventDetails.eventValue = data._defaults.value;

                                window.EA.event(eventDetails);
                            }
                        }, once);
                    }(EA.conversion_trackers[type], events[i]));
                }
            }
        };

    if (!EA.hasOwnProperty('conversionTriggers')) {
        EA.conversionTriggers = {};
    }

    trackConversions();
}(window.mwm || {}, window.jQuery || window.Zepto || null));