<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;
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

class Create implements DriverAttribute {
	public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel) {
		if($trackingId = $driver->setting($id, 'TrackingId', null, ['objects' => [$driver]])) {
			$args = [$trackingId];

			if($settings = $driver->setting($id, 'PageViewSettings', 'auto', ['objects' => [$driver]])) {
				$args[] = $settings;
			}

			singleton('ea')->configure('GA.configuration.' . $id . '.attributes.create', [
				$args,
			]);
		}
	}

	public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model) {

	}
}