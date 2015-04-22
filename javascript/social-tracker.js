var EA = window.EA || {social: {}};

EA.social = (function (social, mwm) {
    social.extractParamFromUri = function (uri, paramName) {
        if (!uri) return '';

        var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
        var params = regex.exec(uri);
        if (params !== null) return decodeURI(params[1]);

        return '';
    };

    social.fb = function (event, action) {
        try {
            if (window.hasOwnProperty('FB') && window.FB.hasOwnProperty('Event') && window.FB.Event.hasOwnProperty('subscribe')) {
                window.FB.Event.subscribe(event, function (targetUrl) {
                    mwm.utilities.triggerCustomEvent(window, "ea::social-event", [{
                        socialNetwork: 'facebook',
                        socialAction:  action,
                        socialTarget:  targetUrl
                    }]);
                });
            }
        } catch (e) {}
    };

    social.twitter = function (eventName, action) {
        try {
            if (window.hasOwnProperty('twttr') && window.twttr.hasOwnProperty('events')) {
                twttr.ready(function (twttr) {
                    twttr.events.bind(eventName, function (event) {
                        if (event) {
                            var targetUrl;

                            if (event.target && event.target.nodeName == 'IFRAME')
                                targetUrl = social.extractParamFromUri(event.target.src, 'url');

                            if (event.data && event.data.user_id)
                                action = action + ' (@' + event.data.user_id + ')';

                            mwm.utilities.triggerCustomEvent(window, "ea::social-event", [{
                                socialNetwork: 'twitter',
                                socialAction:  action,
                                socialTarget:  targetUrl
                            }]);
                        }
                    });
                });
            }
        } catch (e) {}
    };

    social.init = function () {
        social.fb('edge.create', 'like');
        social.fb('edge.remove', 'unlike');
        social.fb('message.send', 'share');
        social.fb('comment.create', 'comment');
        social.fb('comment.remove', 'deleted comment');

        social.twitter('tweet', 'tweet');
        social.twitter('follow', 'follow');
        social.twitter('favorite', 'favourite');
    };

    social.init();

    mwm.utilities.attachToEvent(window, "mwm::loaded:js", social.init);

    mwm.utilities.attachToEvent(window, "ea::social-event", function(e, data) {
        data.hitType = 'social';
        var key;

        if(EA.hasOwnProperty('GA') && EA.GA.hasOwnProperty('trackers')) {
            for (key in EA.GA.trackers) {
                if (EA.GA.trackers.hasOwnProperty(key) && EA.GA.trackers[key].hasOwnProperty('sendEvent')) {
                    EA.GA.trackers[key].sendEvent(data);
                }
            }
        }

        if(EA.hasOwnProperty('MC') && EA.MC.hasOwnProperty('trackers')) {
            for (key in EA.MC.trackers) {
                if (EA.MC.trackers.hasOwnProperty(key) && EA.MC.trackers[key].hasOwnProperty('sendEvent')) {
                    EA.MC.trackers[key].sendEvent(data.socialAction);
                    EA.MC.trackers[key].sendEvent(data.socialAction + ' (' + data.socialTarget + ')');
                }
            }
        }
    });

    return social;
}(EA.core || {}, window.mwm || {}));