<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;

/**
 * Milkyway Multimedia
 * Create.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ScriptAttribute;
use ViewableData;

class Events implements ScriptAttribute
{
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = [])
	{
		$output = [];
		$events = isset($params['events']) ? (array)$params['events'] : [];

		foreach ($events as $type => $options) {
			$output[] = $id . "('send', {$this->createEvent($options, $type)});";
		}

		$additionalEvents = (array)singleton('ea')->unqueue('events');

		foreach ($additionalEvents as $type => $options) {
			$output[] = $id . "('send', {$this->createEvent($options, $type)});";
		}

		return trim(implode("\n", $output));
	}

	protected function createEvent($params = [], $type = 'happening')
	{
		$settings = ['hitType' => 'event'];

		if (is_array($params))
			$settings = array_merge($params, $settings);

		if (!isset($settings['eventCategory']))
			$settings['eventCategory'] = $type;

		if (!isset($settings['eventAction']))
			$settings['eventAction'] = 'click';

		return json_encode($settings);
	}
} 