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

class Send implements ScriptAttribute {
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		$send = isset($params['send']) ? $params['send'] : false;
		$output = [];

		if($send && is_array($send)) {
			foreach($send as $type => $options) {
				if(is_numeric($type))
					$output[] = $id . "('send', '$options');";
				else
					$output[] = $id . "('send', " . json_encode(array_merge(['hitType' => $type], $options)) . ");";
			}
		}
		else
			$output[] = $id . "('send', 'pageview');";

		return trim(implode("\n", $output));
	}
} 