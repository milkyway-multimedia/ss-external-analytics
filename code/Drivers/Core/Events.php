<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Core;

/**
 * Milkyway Multimedia
 * Create.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\DriverAttribute;

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;

class Events implements DriverAttribute {
	public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel) {
		singleton('assets')->utilities_js();
		singleton('assets')->javascript(SS_EXTERNAL_ANALYTICS_DIR . '/javascript/event-tracker.js');

		$events = (array)$driver->setting($id, 'EventTracking', []);

		foreach($events as $event => $params) {
			if(!is_array($params)) continue;

			foreach($params as $paramName => $vars) {
				if(!is_array($vars)) continue;

				// Disable a conversion if a mission session variable is found
				if(isset($vars['check_session_var']) && !Session::get($vars['check_session_var']))
					unset($events[$event][$paramName]);
			}
		}

		if(count($events)) {
			singleton('ea')->configure('events', $events);
		}
	}

	public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model) {
		$rawEvents = singleton('ea')->unqueue('event');
		$events = [];

		foreach ($rawEvents as $type => $options) {
			$events[$type] = $this->createEvent($options, $type);
		}

		if(count($events))
			singleton('ea')->configure('events', $events);
	}

	protected function createEvent($params = [], $type = 'happening')
	{
		$settings = ['hitType' => 'event'];

		if (is_array($params))
			$settings = array_merge($params, $settings);

		if (!isset($settings['eventCategory']))
			$settings['eventCategory'] = isset($settings['category']) ? $settings['category'] : $type;

		if (!isset($settings['eventAction'])) {
			$settings['eventAction'] = isset($settings['action']) ? $settings['action'] : 'click';
		}

		if (!isset($settings['eventLabel']) && isset($settings['label'])) {
			$settings['eventLabel'] = $settings['label'];
		}

		if (!isset($settings['eventValue']) && isset($settings['value'])) {
			$settings['eventValue'] = $settings['value'];
		}

		return $settings;
	}
}