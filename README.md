External Analytics
======
**External Analytics** is a wrapper around analytics collection and reporting. Most advanced/backend features are targeted towards Google Analytics as a platform (such as reports, dashboard, etc), but the snippet can use other javascript libraries, whilst using the same configuration.

## Currently supports
* Google Analytics
* Google Adwords (javascript only)
* Facebook (social events and conversions)
* Twitter (social events)
* AddThis (social events)
* Mailchimp (Goals and Ecommerce360)

## Features
* Insert tracking code
* Social event tracking
* Records length of stay on website/page
* Ability to send these statistics via forms using the AnalyticsDataField
* Event tracking, with a universal function that will send a event to all trackers
* Ecommerce tracking
* Plugs in to some modules including userforms
* Non-interaction tracking (via HTTP protocols and events)
* Ability to make the script unique on each page
* Cached when not in dev

## Install
Add the following to your composer.json file

```

    "require"          : {
		"milkyway-multimedia/ss-external-analytics": "dev-master"
	}

```

# IMPORTANT
The javascript is added on the onAfterInit method for the controller. This means that in your actions you have to take care of the analytics collection before this extension is reached. This is due to Controller not having the same entry point as RequestHandler

To fix this, you must set in your config:

```
ExternalAnalytics:
  include_js_after_action_handled: true
```

And add this code to your Page_Controller:

```
	public function getViewer($action) {
	    $res = $this->extend('beforeCallActionHandler', $this->request, $action);
	    if ($res) return reset($res);
	    
	    $viewer = parent::getViewer($action);

	    $res = $this->extend('afterCallActionHandler', $this->request, $action);
	    if ($res) return reset($res);

        return $viewer;
    }
```

This will allow you to add any additional params for external analytics in your action methods. For most implementations it's not needed however.

## Limitations
* Google Adwords does not convert server side

## License 
* MIT

## Version 
* Version 0.1 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")