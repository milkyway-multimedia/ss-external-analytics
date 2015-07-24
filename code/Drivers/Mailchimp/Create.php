<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\Mailchimp;
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
		if($uuId = $driver->setting($id, 'UUId', null, ['objects' => [$driver]])) {

			if($settings = $driver->setting($id, 'PageViewSettings', 'auto', ['objects' => [$driver]])) {
				$args[] = $settings;
			}

			singleton('ea')->configure('MC.configuration.' . $id, [
				'uuid' => $uuId,
				'dc' => $this->setting($id, 'dc', 'us1', [
					'objects' => [$driver]
				]),
			]);
		}
	}

	public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model) {

	}
}