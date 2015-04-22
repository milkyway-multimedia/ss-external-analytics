External Analytics
======
**External Analytics** is a wrapper around analytics collection and reporting. Most advanced/backend features are targeted towards Google Analytics as a platform (such as reports, dashboard, etc), but the snippet can use other javascript libraries, whilst using the same configuration.

## Currently supports
* Google Analytics
* Mailchimp (Goals and Ecommerce360)

## Features
* Insert tracking code
* Social event tracking
* Records length of stay on website
* Ability to send these via forms using the AnalyticsDataField
* Event tracking, with a universal function that will send a event to all trackers
* Ecommerce tracking
* Plugs in to userforms
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

## License 
* MIT

## Version 
* Version 0.1 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")