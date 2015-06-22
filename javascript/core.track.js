EA = (function (ea) {
    if(!EA.hasOwnProperty('eventTriggers'))
        EA.eventTriggers = {};

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

    if(!ea.conversionTriggers.hasOwnProperty('adwords')) {
        ea.conversionTriggers.adwords = function(details) {
            if(!details.adwords || !details.adwords.id || !details.adwords.label) return;

            var image = new Image(1,1),
                value = details.adwords.value || details._defaults.value || 1.00,
                currency = details.adwords.currency || details._defaults.currency || 'AUD',
                remarketing = details.adwords.remarketing || details._defaults.remarketing || false;

            image.src = "https://www.googleadservices.com/pagead/conversion/" + details.adwords.id + "/?label=" + details.adwords.label + "&value=" + value + "&currency_code=" + currency + "&remarketing_only=" + remarketing.toString() + "&guid=ON";
        };
    }

    if(!ea.conversionTriggers.hasOwnProperty('facebook')) {
        ea.conversionTriggers.facebook = function(details) {
            if(!details.facebook || !details.facebook.id) return;

            var image = new Image(1,1),
                value = details.facebook.value || details._defaults.value || 1.00,
                currency = details.facebook.currency || details._defaults.currency || 'AUD';

            image.src = "https://www.facebook.com/tr?ev=" + details.facebook.id +"&cd[value]=" + value + "&cd[currency]=" + currency;
        };
    }

    if(!ea.conversionTriggers.hasOwnProperty('twitter')) {
        ea.conversionTriggers.twitter = function(details) {
            if(!details.twitter || !details.twitter.id) return;

            var image = new Image(1,1),
                value = details.twitter.value || details._defaults.value || 1.00,
                quantity = details.twitter.quantity || details._defaults.quantity || 0;

            image.src = "https://analytics.twitter.com/i/adsct?txn_id=" + details.twitter.id +"&tw_sale_amount=" + value + "&tw_order_quantity=" + quantity + "&p_id=Twitter";
        };
    }

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