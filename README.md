External Analytics
==================
**External Analytics** is a wrapper around analytics collection and reporting. Most advanced/backend features are targeted towards Google Analytics as a platform (such as reports, dashboard, etc), but the snippet can use other javascript libraries, whilst using the same configuration.

# Important - Breaking changes in 0.1.2
* Configuration is now injected into a page using a RequestFilter. This means all configuration should only be executed once, and definitely when the final response is done.
* Script Attributes are now Driver Attributes, and spit out configuration variables instead of manipulation JS directly (though you can create your own that do as long as they return a before and/or after variable in the postResponse).
* Caching is no longer done by the module. For caching, I recommend: [Silverstripe Cache Include](https://github.com/heyday/silverstripe-cacheinclude), which supports caching responses.

## Currently supports
* Google Analytics
* Google Adwords (javascript only)
* Facebook (social events and conversions)
* Twitter (social events and conversions)
* AddThis (social events)
* Mailchimp (Goals and Ecommerce360)

## Features
* Insert tracking code
* Social event tracking
* Records length of stay on website/page
* Ability to send these statistics via forms using the AnalyticsDataField
* Event tracking, with a universal function that will send a event to all trackers
* Ecommerce tracking (WIP)
* Conversion tracking
* Plugs in to some modules including userforms
* Non-interaction tracking (via HTTP protocols and events)
* Ability to make the script unique on each page

## Install
Add the following to your composer.json file

```

    "require"          : {
		"milkyway-multimedia/ss-external-analytics": "dev-master"
	}

```

## Usage
### Google Analytics
Set a tracking id using either YAML configuration, or within the CMS.

```

  GoogleAnalytics:
    ga_tracking_id: 'ID IN HERE'

```

### Mailchimp
Set a UUId using either YAML configuration, or within the CMS.

```

  Mailchimp:
    mc_uuid: 'ID IN HERE'

```

### Events
You can queue events to all drivers simply by calling the following code. $params is the event details.

```

    singleton('ea')->queue('event', $params = [
    	'eventCategory' => 'buttons',
    	'eventAction' => 'clicked',
    	'eventLabel' => 'Submit', // optional
    	'eventValue' => 1.00, // optional
    ]);

```

### Conversion
You can queue conversions to all drivers simply by calling the following code. $params is the conversion details.

```

    singleton('ea')->queue('conversion', $params = [
    	'defaults' => [
    		'value' => 2.00,
    	],
    	'adwords' => [
    		'id' => 'ADWORDS ID HERE',
    		'label' => 'ADWORDS LABEL HERE',
    	],
    ]);

```

### Ecommerce
You can queue ecommerce transactions to all drivers simply by calling the following code. $params is the transaction/commerce details. This is not fully functional yet and needs more work.

```

    singleton('ea')->queue('ecommerce', $params = [
    	'order_id' => $order->ID,
    	
    	// Other order details
    ]);

```

### Custom configuration
You can also completely configure the EA configuration variable yourself. If you know the ID of the item you wish to configure (the defaults are ga for Google Analytics and mc for Mailchimp), you can edit it directly. For example, set up a Google Analytics experiment as below.

```

	// The $id for the driver implementation is ga (small caps)
    singleton('ea')->configure('GA.configuration.ga.attributes.set', [
    	['expId', $experimentId],
    	['expVar', $experimentVariation],
    ]);
    
    // Execute on all drivers, and let the JS handle the rest
    singleton('ea')->executeDrivers(function($driver, $id) {
    	// Only applies to Google Analytics drivers
    	if(!($driver instanceof \Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics\Driver))
    		continue;
    		
		singleton('ea')->configure('GA.configuration.' . $id . '.attributes.set', [
			['expId', $experimentId],
			['expVar', $experimentVariation],
		]);
	});

```

## Limitations
* Google Adwords does not convert server side

## License 
* MIT

## Version 
* Version 0.1.2 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")