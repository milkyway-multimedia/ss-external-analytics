<?php
/**
 * Milkyway Multimedia
 * Errors.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\ExternalAnalytics\ScriptAttributes\GoogleAnalytics;

use Milkyway\SS\ExternalAnalytics\ScriptAttributes\Contract;

class Errors implements Contract {
	public function output($controller, $params = [], \Milkyway\SS\ExternalAnalytics\Config\Contract $config = null, $prefix = '') {
		if(($controller instanceof \ErrorPage_Controller) || (($response = $controller->Response) && ($response instanceof \SS_HTTPResponse) && $response->isError())) {
			$errorCode = ($controller instanceof \ErrorPage_Controller) ? $controller->ErrorCode : isset($response) ? $response->getStatusCode() : 403;
			$settings = '{ exDescription: \'' . _t('ErrorPage.' . $errorCode, $errorCode) . '\'}';
			return '__' . $prefix . "('send', 'exception', {$settings});\n";
		}

		return '';
	}
} 