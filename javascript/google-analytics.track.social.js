var gaTracker = window.gaTracker || {},
    gaVars = window.gaVars || {};

gaTracker.fb = function (event, action) {
	try {
		if (window.FB && window.FB.Event && window.FB.Event.subscribe) {
			FB.Event.subscribe(event, function (targetUrl) {
                for (var key in gaVars) {
                    if (gaVars.hasOwnProperty(key)) {
                        gaVars[key].sendEvent({
                            hitType: 'social',
                            socialNetwork: 'facebook',
                            socialAction: action,
                            socialTarget: targetUrl
                        });
                    }
                }
			});
		}
	} catch (e) {
	}
};

gaTracker.twitter = function (eventName, action) {
	try {
		if (window.twttr && window.twttr.events) {
			twttr.ready(function (twttr) {
				twttr.events.bind(eventName, function (event) {
					if (event) {
						var targetUrl;

						if (event.target && event.target.nodeName == 'IFRAME')
							targetUrl = ga_track.extractParamFromURI(event.target.src, 'url');

						if (event.data && event.data.user_id)
							action = action + ' (@' + event.data.user_id + ')';

                        for (var key in gaVars) {
                            if (gaVars.hasOwnProperty(key)) {
                                gaVars[key].sendEvent({
                                    hitType: 'social',
                                    socialNetwork: 'twitter',
                                    socialAction: action,
                                    socialTarget: targetUrl
                                });
                            }
                        }
					}
				});
			});
		}
	} catch (e) {
	}
};

gaTracker.extractParamFromUri = function(uri, paramName) {
	if (!uri) return '';

	var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
	var params = regex.exec(uri);
	if (params !== null) return decodeURI(params[1]);

	return '';
};

gaTracker.addSocialEvents = function() {
    gaTracker.fb('edge.create', 'like');
    gaTracker.fb('edge.remove', 'unlike');
    gaTracker.fb('message.send', 'share');
    gaTracker.fb('comment.create', 'comment');
    gaTracker.fb('comment.remove', 'deleted comment');

    gaTracker.fb('tweet', 'tweet');
    gaTracker.fb('follow', 'follow');
    gaTracker.fb('favorite', 'favourite');
};

gaTracker.addSocialEvents();

if(typeof window.attachToEvent === "function") {
    window.attachToEvent(window, "mwm::loaded:js", gaTracker.addSocialEvents);
}
else if (window.jQuery) {
    window.jQuery(window).on("mwm::loaded:js", gaTracker.addSocialEvents);
}