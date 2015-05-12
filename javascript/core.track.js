EA = (function (ea) {
    if(!ea.hasOwnProperty('eventTriggers'))
        ea.eventTriggers = {};

    ea.event = function(details, except) {
        if(!except || except.constructor !== Array) {
            except = except ? [except] : [];
        }

        for(var prop in EA.eventTriggers) {
            if(EA.eventTriggers.hasOwnProperty(prop) && except.indexOf(prop) === -1) {
                EA.eventTriggers[prop].call(this, details);
            }
        }
    };

    if(!ea.hasOwnProperty('conversionTriggers'))
        ea.conversionTriggers = {};

    ea.conversion = function(details, except) {
        if(!except || except.constructor !== Array) {
            except = except ? [except] : [];
        }

        for(var prop in EA.conversionTriggers) {
            if(EA.conversionTriggers.hasOwnProperty(prop) && except.indexOf(prop) === -1) {
                EA.conversionTriggers[prop].call(this, details);
            }
        }
    };

    return ea;
}(window.EA || {}));