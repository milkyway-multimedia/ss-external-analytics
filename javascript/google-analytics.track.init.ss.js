var EA = window.EA || {GA : {}};

if(!EA.hasOwnProperty('GA'))
    EA.GA = {};

EA.GA.trackers = (function (trackers) {
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;e=o.createElement(i);r=o.getElementsByTagName(i)[0];e.src='//www.google-analytics.com/analytics.js';r.parentNode.insertBefore(e,r)}(window,document,'script','$Var'));

    trackers['$Var'] = {
        variable:       '$Var',
        initCallbacks:  {},
        hitCallback:    function(){},
        sendEvent:      function(data) {
                {$Var}('send', data);
        }
    };

    return trackers;
}(EA.GA.trackers || {}));

EA.GA.trackers = (function (trackers, mwm, EA) {
    <% if $Attributes %>
        $Attributes
    <% else_if $TrackingId %>
        $Var('create', '$TrackingId');
        $Var('send', 'pageview', {
            'hitCallback': function() {
                trackers['{$Var}'].hitCallback();
            }
        });
    <% end_if %>

    {$Var}(function() {
        if(!EA.hasOwnProperty('eventTriggers')) {
            EA.eventTriggers = {};
        }

        EA.eventTriggers['{$Var}'] = trackers['{$Var}'].sendEvent;

        trackers['{$Var}'].trackers = {$Var}.getAll();
        EA.GA.trackers = trackers;

        mwm.utilities.triggerCustomEvent(window, "ea::loaded:google-analytics", [trackers['{$Var}']]);

        if(trackers['{$Var}'].hasOwnProperty('initCallbacks')) {
            for(var callback in trackers['{$Var}'].initCallbacks) {
                if(trackers['{$Var}'].initCallbacks.hasOwnProperty(callback)) {
                    trackers['{$Var}'].initCallbacks[callback].apply({$Var}, [trackers['{$Var}'].trackers]);
                }
            }
        }
    });

    return trackers;
}(EA.GA.trackers || {}, window.mwm || {}, EA || {}));