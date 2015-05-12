<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;

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
			$output[] = '$mcGoal.processEvent("' . $this->getTitleFromOptions($options, $type) . '");';
		}

		$additionalEvents = (array)singleton('ea')->unqueue('event', $id);

		foreach ($additionalEvents as $type => $options) {
			$output[] = '$mcGoal.processEvent("' . $this->getTitleFromOptions($options, $type) . '");';
		}

		return trim(implode("\n", $output));
	}

	protected function getTitleFromOptions($options, $type) {
		if(is_string($options))
			return $options;

		if(!is_int($type))
			return $type;

		if(is_array($options)) {
			if(isset($options['title']))
				return $options['title'];
			if(isset($options['label']))
				return $options['label'];
			else if(isset($options['event']))
				return $options['event'];
			else if(isset($options['eventCategory']))
				return $options['eventCategory'];
			else if(isset($options['eventAction']))
				return $options['eventAction'];
			else if(isset($options['hitType']))
				return $options['hitType'];
		}

		return trim(json_encode($options), '"');
	}
} 