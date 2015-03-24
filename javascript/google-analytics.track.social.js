var EA = window.EA || { GA: {} };

if(EA.GA.hasOwnProperty('trackers')) {
    EA.GA.social = (function (social) {
        social.fb = function (event, action) {
            try {
                if (window.FB && window.FB.Event && window.FB.Event.subscribe) {
                    FB.Event.subscribe(event, function (targetUrl) {
                        for (var key in EA_GA.trackers) {
                            if (EA_GA.trackers.hasOwnProperty(key)) {
                                EA_GA.trackers[key].sendEvent({
                                    hitType:       'social',
                                    socialNetwork: 'facebook',
                                    socialAction:  action,
                                    socialTarget:  targetUrl
                                });
                            }
                        }
                    });
                }
            } catch (e) {
            }
        };

        social.twitter = function (eventName, action) {
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

                                for (var key in EA_GA.trackers) {
                                    if (EA_GA.trackers.hasOwnProperty(key)) {
                                        EA_GA.trackers[key].sendEvent({
                                            hitType:       'social',
                                            socialNetwork: 'twitter',
                                            socialAction:  action,
                                            socialTarget:  targetUrl
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

        social.extractParamFromUri = function (uri, paramName) {
            if (!uri) return '';

            var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
            var params = regex.exec(uri);
            if (params !== null) return decodeURI(params[1]);

            return '';
        };

        social.addSocialEvents = function () {
            social.fb('edge.create', 'like');
            social.fb('edge.remove', 'unlike');
            social.fb('message.send', 'share');
            social.fb('comment.create', 'comment');
            social.fb('comment.remove', 'deleted comment');

            social.fb('tweet', 'tweet');
            social.fb('follow', 'follow');
            social.fb('favorite', 'favourite');
        };

        social.addSocialEvents();

        if (typeof window.attachToEvent === "function") {
            window.attachToEvent(window, "mwm::loaded:js", social.addSocialEvents);
        }
        else if (window.jQuery) {
            window.jQuery(window).on("mwm::loaded:js", social.addSocialEvents);
        }

        return social;
    }(EA.GA.social || {}));
}