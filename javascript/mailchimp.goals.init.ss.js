var EA = window.EA || {MC : {}};

if(!EA.hasOwnProperty('MC'))
    EA.MC = {};

EA.MC.trackers = (function (trackers, mwm, EA) {
    window.\$mcGoal = {$Settings};

    trackers['{$Var}'] = {
        settings: {$Settings}
    };

    (function() {
        var sp = document.createElement('script'); sp.type = 'text/javascript'; sp.async = true; sp.defer = true;
        sp.src = ('https:' == document.location.protocol ? 'https://s3.amazonaws.com/downloads.mailchimp.com' : 'http://downloads.mailchimp.com') + '/js/goal.min.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sp, s);

            mwm.utilities.attachToEvent(sp, 'load', function() {
                <% if $Attributes %>
                    $Attributes
                <% end_if %>

                trackers['{$Var}'].sendEvent = function(data) {
                    var title;

                    if(data.hasOwnProperty('longAction'))
                        title = data.longAction;
                    else if(data.hasOwnProperty('title'))
                        title = data.title;
                    else if(data.hasOwnProperty('label'))
                        title = data.label;

                    if(title)
                        \$mcGoal.processEvent(title);
                };

                mwm.utilities.triggerCustomEvent(window, "ea::loaded:mailchimp-goals", [trackers['{$Var}']]);

                if(!EA.hasOwnProperty('eventTriggers')) {
                    EA.eventTriggers = {};
                }

                EA.eventTriggers['{$Var}'] = trackers['{$Var}'].sendEvent;
            });
    })();

    return trackers;
}(EA.MC.trackers || {}, window.mwm || {}, window.EA || {}));