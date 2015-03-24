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

class Send implements Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
		$send = isset($params['send']) ? $params['send'] : false;
		$output = [];

		if($send && is_array($send)) {
			foreach($send as $type => $options) {
				if(is_numeric($type))
					$output[] = $prefix . "('send', '$options');";
				else
					$output[] = $prefix . "('send', " . json_encode(array_merge(['hitType' => $type], $options)) . ");";
			}
		}
		else
			$output[] = $prefix . "('send', 'pageview');";

		return trim(implode("\n", $output));
	}
} 