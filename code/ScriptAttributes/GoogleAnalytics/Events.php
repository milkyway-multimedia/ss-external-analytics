<?php
/**
 * Milkyway Multimedia
 * Create.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\ScriptAttributes\GoogleAnalytics;

use Milkyway\SS\ExternalAnalytics\ScriptAttributes\Contract;

class Events implements Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
		$output = [];
		$events = isset($params['events']) ? $params['events'] : false;

		if($events && is_array($events)) {
			foreach($events as $type => $options) {
				$settings = ['hitType' => 'event'];

				if(is_array($options))
					$settings = array_merge($options, $settings);

				if(!isset($settings['eventCategory']))
					$settings['eventCategory'] = $type;

				if(!isset($settings['eventAction']))
					$settings['eventAction'] = 'click';

				$settings = json_encode($settings);

				$output[] = '__' . $prefix . "('send', {$settings});";
			}
		}

		return trim(implode("\n", $output));
	}
} 