var EA = window.EA || {MC : {}};

if(!EA.hasOwnProperty('MC'))
    EA.MC = {};

EA.MC.trackers = (function (trackers) {
    trackers['{$Var}'] = {
        settings: {$Settings}
    };
    (function() {
        var sp = document.createElement('script'); sp.type = 'text/javascript'; sp.async = true; sp.defer = true;
        sp.src = ('https:' == document.location.protocol ? 'https://s3.amazonaws.com/downloads.mailchimp.com' : 'http://downloads.mailchimp.com') + '/js/goal.min.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sp, s);
    })();

    return trackers;
}(EA.MC.trackers || {}));