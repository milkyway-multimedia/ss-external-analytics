(function (mwm) {
    var appendSettingTo = function(url, setting, data, defaults) {
            if(data && data.hasOwnProperty(setting))
                url += setting + '=' + data[setting] + '&';
            else if(defaults && defaults.hasOwnProperty(setting))
                url += setting + '=' + defaults[setting] + '&';

            return url;
        },
        appendQuestionMark = function(url) {
            if(url.indexOf('?') !== -1)
                return url;
            else
                return url + '?';
        },
        //appendAmpersand = function(url) {
        //    if(url.indexOf('&', url.length - 1) !== -1)
        //        return url;
        //    else
        //        return url + '&';
        //},
        sendConversion =     function(data) {
            if(EA && EA.hasOwnProperty('conversion_trackers')) {
                var tracker, type, option;

                for(type in EA.conversion_trackers) {
                    if(EA.conversion_trackers.hasOwnProperty(type) && EA.conversion_trackers[type].hasOwnProperty('track')) {
                        for(tracker in EA.conversion_trackers[type].track) {
                            if(EA.conversion_trackers[type].track.hasOwnProperty(tracker)) {
                                var id = data.id ? data.id : settings.id,
                                    url = appendQuestionMark(EA.conversion_trackers[type].url.replace('$id', id)),
                                    image = new Image(1,1);

                                for(option in EA.conversion_trackers[type].track[tracker]) {
                                    if(EA.conversion_trackers[type].track[tracker].hasOwnProperty(option)) {
                                        if(['events', 'element'].indexOf(option) !== -1) continue;
                                        url = appendSettingTo(url, option, EA.conversion_trackers[type].track[tracker]);
                                    }
                                }

                                if(data) {
                                    for(option in data) {
                                        if(data.hasOwnProperty(option)) {
                                            url = appendSettingTo(url, option, data, EA.conversion_trackers[type].track[tracker]);
                                        }
                                    }
                                }

                                image.src = url;
                            }
                        }
                    }
                }
            }
        },
        tracking = [],
        trackConversions = function() {
            if(EA && EA.hasOwnProperty('conversion_trackers')) {
                var tracker, type, i, events, element, once;

                for(type in EA.conversion_trackers) {
                    if(EA.conversion_trackers.hasOwnProperty(type) && EA.conversion_trackers[type].hasOwnProperty('track')) {
                        for(tracker in EA.conversion_trackers[type].track) {
                            if(EA.conversion_trackers[type].track.hasOwnProperty(tracker)) {
                                if(EA.conversion_trackers[type].track[tracker].hasOwnProperty('events'))
                                    events = [tracker];
                                else
                                    events = EA.conversion_trackers[type].track[tracker].events;

                                element = EA.conversion_trackers[type].track[tracker].hasOwnProperty('element') ? EA.conversion_trackers[type].track[tracker].element : window;
                                once = EA.conversion_trackers[type].track[tracker].once ? true : false;

                                if(element === 'window')
                                    element = window;
                                else if(element === 'document')
                                    element = document;
                                else
                                    element = document.querySelectorAll(element);

                                for(i=0;i<events.length;i++) {
                                    if(tracking.indexOf(type+':'+tracker+':'+events[i]) !== -1)
                                        continue;

                                    tracking.push(type+':'+tracker+':'+events[i]);

                                    (function (data) {
                                        mwm.utilities.attachToEvent(element, events[i], function () {
                                            EA.conversion(data);
                                        }, once);
                                    }(EA.conversion_trackers[type].track[tracker]));
                                }
                            }
                        }
                    }
                }
            }
        };

    if(!EA.hasOwnProperty('conversionTriggers')) {
        EA.conversionTriggers = {};
    }

    EA.conversionTriggers['core'] = sendConversion;
    trackConversions();
}(window.mwm || {}));