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
use Milkyway\SS\ExternalAnalytics\Utilities;

class Create implements Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
		if($trackingId = Utilities::env_value('TrackingId', $controller, $config)) {
			$create = isset($params['create']) ? (array)$params['create'] : [];
			$create['clientId'] = $config->findClientId();

			$create = count($create) ? ', ' . json_encode(array_filter($create)) : '';

			return '__' . $prefix . "('create', '$trackingId'{$create});";
		}

		return '';
	}
} 