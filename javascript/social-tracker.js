var EA = window.EA || {social: {}};

EA.social = (function (social, mwm) {
    var done = [];

    social.extractParamFromUri = function (uri, paramName) {
        if (!uri) return '';

        var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
        var params = regex.exec(uri);
        if (params !== null) return decodeURI(params[1]);

        return '';
    };

    social.fb = function (event, action) {
        if(done.indexOf('fb:'+event+':'+action) !== -1) return;

        try {
            if (window.hasOwnProperty('FB') && window.FB.hasOwnProperty('Event') && window.FB.Event.hasOwnProperty('subscribe')) {
                done.push('fb:'+event+':'+action);

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
        if(done.indexOf('twitter:'+eventName+':'+action) !== -1) return;

        try {
            if (window.hasOwnProperty('twttr') && window.twttr.hasOwnProperty('events')) {
                done.push('twitter:'+eventName+':'+action);

                twttr.ready(function (twttr) {
                    twttr.events.bind(eventName, function (event) {
                        if (event) {
                            var targetUrl;

                            if (event.target && event.target.nodeName == 'IFRAME')
                                targetUrl = social.extractParamFromUri(event.target.src, 'url');
                            else if(event.data && event.data.screen_name)
                                targetUrl = event.data.screen_name;

                            if (event.data && event.data.screen_name)
                                action = action + ' (@' + event.data.screen_name + ')';

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

    social.addthis = function (action) {
        if(done.indexOf('addthis:'+action) !== -1) return;

        try {
            if (window.hasOwnProperty('addthis')) {
                done.push('addthis:'+action);

                var share = function (event) {
                    var socialTarget = event.hasOwnProperty('hash') ? event.hash : window.location.href;

                    mwm.utilities.triggerCustomEvent(window, "ea::social-event", [{
                        socialNetwork: event.service,
                        socialAction:  action,
                        socialTarget:  socialTarget
                    }]);
                };

                addthis.addEventListener('addthis.menu.share', share);
                addthis.addEventListener('addthis.user.clickback', share);
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
        social.twitter('retweet', 'retweet');
        social.twitter('follow', 'follow');
        social.twitter('unfollow', 'unfollow');
        social.twitter('favorite', 'favourite');
        social.twitter('unfavorite', 'unfavourite');

        social.addthis('addthis:share');
    };

    social.init();

    mwm.utilities.attachToEvent(window, "mwm::loaded:js", social.init);

    mwm.utilities.attachToEvent(window, "ea::social-event", function(e, data) {
        if(!window.EA || !EA.hasOwnProperty('event')) return;

        if(!data)
            data = e.detail[0];

        data.hitType = 'social';

        EA.event(data);
    });

    return social;
}(EA.social || {}, window.mwm || {}));