<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;
/**
 * Milkyway Multimedia
 * Errors.php
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

class Errors implements DriverAttribute {
	public function preRequest(DriverContract $driver, $id, Request $request, Session $session, DataModel $dataModel) {

	}

	public function postRequest(DriverContract $driver, $id, Request $request, Response $response, DataModel $model) {
		if(!$response->isError())
			return;

		singleton('ea')->configure('GA.configuration.' . $id . '.attributes.send', [
			[
				$response->getStatusCode() < 500 ? 'exception' : 'fatalException',
				(array)$driver->setting($id, 'ErrorSettings', [
					'exDescription' => _t('ErrorPage.' . $response->getStatusCode(), $response->getStatusCode())
				], ['objects' => [$driver]]),
			],
		]);
	}
}