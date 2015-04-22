var EA = window.EA || {};

EA.core = (function (core, mwm) {
    /*
    core.loaded = false;

    <% if $SessionLink %>
        mwm.utilities.requestViaAjax('$SessionLink', 'JSON', function(response) {
            core = response.data || {};
            core.loaded = true;
            EA.core = core;

            mwm.utilities.triggerCustomEvent(window, "ea::loaded:core", [response.data, response]);
        });
    <% end_if %>
    */

    return core;
}(EA.core || {}, window.mwm || {}));

EA = (function (ea) {
    if(!ea.hasOwnProperty('eventTriggers'))
        ea.eventTriggers = {};

    ea.event = function(details, except) {
        if(!except || except.constructor !== Array) {
            except = except ? [except] : [];
        }

        for(var trigger in EA.eventTriggers) {
            if(EA.eventTriggers.hasOwnProperty(trigger) && except.indexOf(trigger) === -1) {
                EA.eventTriggers[trigger].call(this, details);
            }
        }
    };

    return ea;
}(EA || {}));