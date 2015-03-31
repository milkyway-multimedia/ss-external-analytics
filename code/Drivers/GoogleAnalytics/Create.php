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

class Create implements ScriptAttribute {
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		if($trackingId = $driver->setting($id, 'TrackingId', null, ['objects' => [$controller, $driver]])) {
			$create = isset($params['create']) ? (array)$params['create'] : [];
			$create['clientId'] = Driver::session_user_id($id);

			$create = count($create) ? ', ' . json_encode(array_filter($create)) : '';

			return $id . "('create', '$trackingId'{$create});";
		}

		return '';
	}
} 