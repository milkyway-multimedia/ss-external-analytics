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
		$errorCode = '';

		if($controller instanceof \ErrorPage_Controller)
			$errorCode = $controller->ErrorCode;
		elseif(($response = $controller->Response) && ($response instanceof \SS_HTTPResponse) && $response->isError())
			$errorCode = $response->getStatusCode();

		if($errorCode) {
			$settings = '{ exDescription: \'' . _t('ErrorPage.' . $errorCode, $errorCode) . '\'}';
			return '__' . $prefix . "('send', 'exception', {$settings});\n";
		}

		return '';
	}
} 