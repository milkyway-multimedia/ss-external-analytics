<?php namespace Milkyway\SS\ExternalAnalytics\Drivers\GoogleAnalytics;
/**
 * Milkyway Multimedia
 * Errors.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\Driver as DriverContract;
use Milkyway\SS\ExternalAnalytics\Drivers\Contracts\ScriptAttribute;
use ViewableData;
use Controller;
use SS_HTTPResponse;

class Errors implements ScriptAttribute {
	public function output(DriverContract $driver, $id, ViewableData $controller = null, $params = []) {
		$errorCode = '';

		if(!$controller)
			$controller = Controller::curr();

		if($controller instanceof \ErrorPage_Controller)
			$errorCode = $controller->ErrorCode;
		elseif(($response = $controller->Response) && ($response instanceof SS_HTTPResponse) && $response->isError())
			$errorCode = $response->getStatusCode();

		if($errorCode) {
			$settings = '{ exDescription: \'' . _t('ErrorPage.' . $errorCode, $errorCode) . '\'}';
			return $id . "('send', 'exception', {$settings});\n";
		}

		return '';
	}
} 